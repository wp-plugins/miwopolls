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



























































































































	mimport('framework.application.component.modeladmin');

	class MiwopollsModelPoll extends MModelAdmin {

		var $_id = null;
		var $_data = null;	
		
		function __construct()	{
			parent::__construct();

			$array = MRequest::getVar('cid', array(0), '', 'array');
			$edit = MRequest::getVar('edit', true);
			
			if ($edit) {
				$this->setId((int)$array[0]);
			}
		}
		
		function setId($id)	{
			$this->_id		= $id;
			$this->_data	= null;
		}
		
		function isCheckedOut($uid = 0) {
			if ((boolean)$this->_data)	{
				if ($uid) {
					return ($this->_data->checked_out && $this->_data->checked_out != $uid);
				}
				else {
					return $this->_data->checked_out;
				}
			}
		}
		
		public function checkin($pks = array()) {
			if ($this->_id) {
				$poll = $this->getTable();
				
				if (!$poll->checkin($this->_id)) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			
			return false;
		}
		
		public function checkout($uid = null) {
			if ($this->_id) {
				// Make sure we have a user id to checkout the article with
				if (is_null($uid)) {
					$user	= MFactory::getUser();
					$uid	= $user->get('id');
				}
				
				// Lets get to it and checkout the thing...
				$poll = $this->getTable();
				
				if(!$poll->checkout($uid, $this->_id)) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}

				return true;
			}
			
			return false;
		}
		
		function getItemData() {
			static $row;
			
			if (!isset($row)) {
				$row = MTable::getInstance('Poll', 'Table');
				$row->load($this->_id);
			}
			
			return $row;
		}
		
		public function getForm($data = array(), $loadData = true) {
			// Get the form.
			$form = $this->loadForm('com_miwopolls.poll', 'poll', array('control' => 'params', 'load_data' => $loadData));
			if (empty($form)) {
				return false;
			}

			return $form;
		}
		
		protected function loadFormData() {
			// Check the session for previously entered form data.
			$data = MFactory::getApplication()->getUserState('com_miwopolls.edit.poll.data', array());

			if (empty($data)) {
				$row = self::getItemData();
				
				if ($row->id != 0) {
					$registry = new MRegistry($row->params);
					
					$data = $registry->toArray();
				}
			}

			return $data;
		}
		
		function store($data) {
			$row = $this->getTable();
			
			// Bind the form fields to the web link table
			if (!$row->bind($data)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Create the timestamp for the date
			$row->date = gmdate('Y-m-d H:i:s');

			// Make sure the web link table is valid
			if (!$row->check()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Store the web link table to the database
			if (!$row->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}
		
		//delete empty options to clear the db
		function deleteOption($id) {
			$query = "DELETE FROM #__miwopolls_options WHERE id = ".$this->_db->Quote((int)$id)." LIMIT 1";
			$this->_db->setQuery($query);
			
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			return true;
		}
	}
