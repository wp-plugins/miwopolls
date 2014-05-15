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

class MiwopollsModelPolls extends MiwopollsModel {

	var $_query = null;
	var $_data = null;
	var $_total = null;
	var $_pagination = null;

	function __construct() {
		parent::__construct();

		$this->mainframe = MFactory::getApplication();
		$this->option = MRequest::getWord('option');

		// Get the pagination request variables
		$limit		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.limit', 'limit', $this->mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $this->mainframe->getUserStateFromRequest($this->option.'.polls.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState($this->option.'.polls.limit', $limit);
		$this->setState($this->option.'.polls.limitstart', $limitstart);
		
		$this->_buildViewQuery();
	}
	
	function getData() {
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->_query, $this->getState($this->option.'.polls.limitstart'), $this->getState($this->option.'.polls.limit'));
		}

		return $this->_data;
	}
	
	function getTotal()	{
		if (empty($this->_total)) {
			$this->_total = $this->_getListCount($this->_query);
		}

		return $this->_total;
	}

	function getPagination() {
		if (empty($this->_pagination)) {
			mimport('framework.html.pagination');
						$this->_pagination = new MPagination($this->getTotal(), $this->getState($this->option.'.polls.limitstart'), $this->getState($this->option.'.polls.limit'));

		}

		return $this->_pagination;
	}
	
	function _buildViewQuery() {
		if (empty($this->_query)) {
			$db	= MFactory::getDBO();
			
			$where		= $this->_buildViewWhere();
			$orderby	= $this->_buildViewOrderBy();
			
			$this->_query = "SELECT m.*, u.user_login AS editor, COUNT(o.id) AS options, (SELECT count(v.id) FROM #__miwopolls_votes AS v 
			WHERE v.poll_id = m.id) AS votes FROM #__miwopolls_polls AS m 
			LEFT JOIN #__users AS u ON u.id = m.checked_out 
			LEFT JOIN #__miwopolls_options AS o ON o.poll_id = m.id AND o.text <> ''"
			. $where
			. " GROUP BY m.id"
			. $orderby;
		}

		return $this->_query;
	}
	
	function _buildViewOrderBy()	{
		$filter_order		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order',		'filter_order',		'm.title',	'string');
		$filter_order_Dir	= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order_Dir',	'filter_order_Dir',	'',			'word');

		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		return $orderby;
	}
	
	function _buildViewWhere() {
		$db	= MFactory::getDBO();
		
		$filter_order		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order',			'filter_order',		'm.title',	'string');
		$filter_order_Dir	= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order_Dir',		'filter_order_Dir',	'',			'word');
		$filter_state		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_state',			'filter_state',		'',			'word');
		$search				= $this->mainframe->getUserStateFromRequest($this->option.'.polls.search',					'search',			'',			'string');
		$search				= MString::strtolower($search);

		$where = array();

		if ($search) {
			$where[] = 'LOWER(m.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'm.published = 1';
			}
			else if ($filter_state == 'U') {
				$where[] = 'm.published = 0';
			}
		}
		
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

		return $where;
	}	

	function resetVotes($cid = Null) {
		$db = MFactory::getDBO();
		
		$cid = MRequest::getVar('cid', array(), '', 'array');	
		MArrayHelper::toInteger($cid);
		$cids = implode(',', $cid);
		
		$query = "DELETE FROM #__miwopolls_votes WHERE poll_id IN (".$cids.")";
		$db->setQuery($query);
		
		if ($db->query()) {
			return true;
		}
		else {
			return false;
		}
	}
}