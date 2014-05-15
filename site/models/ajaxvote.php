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

class MiwopollsModelAjaxvote extends MiwisoftModel {

	var $_query = null;
	var $_data = null;
	var $_total = null;
	var $_voted = null;

	function getVoted() {
		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');

		$mainframe 	= MFactory::getApplication();
		$poll_id	= MRequest::getInt('id', 0);
		$option_id	= MRequest::getInt('voteid', 0);
		$poll 		= MTable::getInstance('Poll','Table');
		
		if (!$poll->load($poll_id) || $poll->published != 1) {
			$mainframe->redirect('index.php', MText::_('ALERTNOTAUTH'));
			//MError::raiseWarning(404, MText::_('ALERTNOTAUTH'));
			return;
		}

        require_once(MPATH_COMPONENT.'/models/poll.php');
		$model = new MiwopollsModelPoll();

		if (MiwopollsHelper::is15()) {
			$params = new JParameter($poll->params);
			$cookieName	= JUtility::getHash($mainframe->getName().'poll'.$poll_id );
		}
		else {
			$params = new MRegistry($poll->params);
			$cookieName	= MApplication::getHash($mainframe->getName().'poll'.$poll_id );
		}
		
		$voted_cookie = MRequest::getVar($cookieName, '0', 'COOKIE', 'INT');
        $voted_ip = $model->ipVoted($poll, $poll_id);
		
		if ($params->get('ip_check') and ($voted_cookie or $voted_ip or !$option_id)) {
			if ($voted_cookie || $voted_ip) {
				$msg = MText::_('COM_MIWOPOLLS_ALREADY_VOTED');
				$tom = "error";
			}
			
			if (!$option_id){
				$msg = MText::_('COM_MIWOPOLLS_NO_SELECTED');
				$tom = "error";
			}
			
			$this->_voted = 0;
		}
		else {			
			if ($model->vote($poll_id, $option_id)) {
				$this->_voted = 1;
				//Set cookie showing that user has voted
				setcookie($cookieName, '1', time() + 60 * $poll->lag);
			}
			else {
				$this->_voted = 0;
			}
		}
		
		return $this->_voted = 1;
	}

	function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}
		
		return $this->_data;
	}
	
	function getTotal() {
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}
	
	function _buildQuery() {
		if (empty($this->_query)) {
			$db	= MFactory::getDBO();
			$poll_id = MRequest::getVar('id', 0, 'POST', 'int');
			
			$this->_query = "SELECT o.id, o.text, o.color, COUNT(v.id) AS votes" 
			." FROM #__miwopolls_options AS o "
			." LEFT JOIN #__miwopolls_votes AS v "
			." ON o.id = v.option_id "
			." WHERE o.poll_id = ".(int)$poll_id
			." GROUP BY o.id "
			;
		}
		
		return $this->_query;
	}
}