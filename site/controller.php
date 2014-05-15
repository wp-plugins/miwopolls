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

// no direct access
defined('MIWI') or die( 'Restricted access' );

mimport('framework.application.component.controller');

class MiwopollsController extends MController {

	public function display() {
		//Set the default view, just in case
		$view = MRequest::getCmd('view');
		if (empty($view)) {
			MRequest::setVar('view', 'polls');
		}
		
		parent::display();
	}

	public function vote()	{
		// Check for request forgeries
		MRequest::checkToken() or mexit('Invalid Token');

		$mainframe 	= MFactory::getApplication();
		$poll_id	= MRequest::getInt('id', 0);
		$option_id	= MRequest::getInt('voteid', 0);
		$poll 		= MTable::getInstance('Poll', 'Table');
		
		if (!$poll->load($poll_id) || $poll->published != 1) {
			MError::raiseWarning(404, MText::_('ALERTNOTAUTH'));
			return;
		}

        $model = $this->getModel('Poll');

		$cookieName	= JUtility::getHash($mainframe->getName().'poll'.$poll_id);
		$voted_cookie = MRequest::getVar($cookieName, '0', 'COOKIE', 'INT');
        $voted_ip = $model->ipVoted($poll, $poll_id);
		
			$params = new MRegistry($poll->params);





		
		if ($params->get('ip_check') and ($voted_cookie or $voted_ip or !$option_id)) {
			if ($voted_cookie || $voted_ip) {
				$msg = MText::_('COM_MIWOPOLLS_ALREADY_VOTED');
				$tom = "error";
			}

			if (!$option_id){
				$msg = MText::_('COM_MIWOPOLLS_NO_SELECTED');
				$tom = "error";
			}
		}
		else {
			if ($model->vote($poll_id, $option_id)) {
				//Set cookie showing that user has voted
				setcookie($cookieName, '1', time() + 60*$poll->lag);
            }

			$msg = MText::_('COM_MIWOPOLLS_THANK_YOU');
			$tom = "";
			
			if (MFactory::getUser()->id != 0) {
				MPluginHelper::importPlugin('miwopolls');
				$dispatcher = MDispatcher::getInstance();
				$dispatcher->trigger('onAfterVote', array($poll, $option_id));
			}
		}

		// set Itemid id for links
		$menu 		=  MFactory::getApplication()->getMenu();
		$items   = $menu->getItems('link', 'index.php?option=com_miwopolls');

		$itemid  = isset($items[0]) ? '&Itemid='.$items[0]->id : '';

		$this->setRedirect(MRoute::_('index.php?option=com_miwopolls&view=poll&id='. $poll_id.':'.$poll->alias.$itemid, false), $msg, $tom);
	}
}
?>