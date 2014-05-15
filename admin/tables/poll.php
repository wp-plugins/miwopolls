<?php
/**
* @version		1.0.0
* @package		MiwoPolls
* @subpackage	MiwoPolls
* @copyright	2009-2011 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html

*
* Based on Apoll Component
* @copyright (C) 2009 - 2011 Hristo Genev All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.afactory.org
*/

// Check to ensure this file is included in Joomla!
defined('MIWI') or die( 'Restricted access' );

require_once(MPATH_WP_PLG.'/miwopolls/admin/helpers/miwopolls.php');

class TablePoll extends MTable {

	public $id					= 0;
	public $title				= '';
	public $alias				= '';
	public $checked_out			= 0;
	public $checked_out_time	= 0;
	public $published			= 0;
	public $publish_up			= 0;
	public $publish_down		= 0;
	public $params 				= null;
	public $access				= 0;
	public $lag					= 1440;

	function __construct(&$db) {
		parent::__construct('#__miwopolls_polls', 'id', $db);
	}

	function bind($array, $ignore = '') {
		if (MiwopollsHelper::is15()) {
			if (key_exists('params', $array) && is_array($array['params'])) {
				$registry = new MRegistry();
				$registry->loadArray($array['params']);
				$array['params'] = $registry->toString();
			}
		}
		else {
			if (isset($array['params']) && is_array($array['params'])) {
				$registry = new MRegistry();
				$registry->loadArray($array['params']);
				$array['params'] = (string)$registry;
			}
		}
		
		return parent::bind($array, $ignore);
	}
	
	function check() {
		$mainframe = MFactory::getApplication();
		
		// check for valid name
		if (trim($this->title) == '') {
			$this->setError(MText::_('Your Poll must contain a title.'));
			return false;
		}
		
		// check for valid lag
		$this->lag = floatval($this->lag * 60);
		if ($this->lag == 0) {
			$this->setError(MText::_('Your Poll must have a non-zero lag time.'));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		
		$this->alias = MFilterOutput::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {

			$datenow = MFactory::getDate();
			$datenow->setOffset($mainframe->getCfg('offset'));
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}

	// overloaded delete function
	function delete($oid=null) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval($oid);
		}

		if (parent::delete($oid)) {
			$db = MFactory::getDBO();

			$db->setQuery("DELETE FROM #__miwopolls_options WHERE poll_id = ".(int)$oid);
			if (!$db->query()) {
				$this->_error .= $db->getErrorMsg() . "\n";
			}
			
			$db->setQuery("DELETE FROM #__miwopolls_votes WHERE poll_id = ".(int)$oid);
			if (!$db->query()) {
				$this->_error .= $db->getErrorMsg() . "\n";
			}

			return true;
		}

		return false;
	}
	
	// function to get the options for current poll
	function getOptions($poll_id) {
		$query = "SELECT o.*, COUNT(v.id) AS hits"
		." FROM #__miwopolls_options AS o"
		." LEFT JOIN #__miwopolls_votes AS v"
		." ON (o.id = v.option_id AND v.poll_id = ".(int) $poll_id . ")"
		." WHERE o.poll_id = ".(int) $poll_id
		." AND text <> '' GROUP BY o.id ORDER BY o.ordering";
		
		$this->_db->setQuery($query);
		
		return $this->_db->loadObjectList();	
	}
}