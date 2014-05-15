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
defined('MIWI') or die('Restricted access');

class MiwopollsViewVotes extends MiwopollsView {

	function display($tpl = null) {
		$document = MFactory::getDocument();
  		$document->addStyleSheet(MURL_MIWOPOLLS.'/admin/assets/css/miwopolls.css');
		
		$title = $this->get('Title');
		
		$t_title = ($title) ? MText::_('COM_MIWOPOLLS_VOTES_FOR').': '.$title : MText::_('COM_MIWOPOLLS_SELECT_POLL');
		MToolBarHelper::title($t_title, 'miwopolls');
		MToolBarHelper::deleteList(MText::_('COM_MIWOPOLLS_DELETE_CONFIRM'), "deleteVotes", MText::_('COM_MIWOPOLLS_DELETE'), true);
		MToolBarHelper::divider();
		MToolBarHelper::preferences('com_miwopolls', 500);

		$this->mainframe = MFactory::getApplication();
		$this->option = MRequest::getWord('option');

		$filter_order		= $this->mainframe->getUserStateFromRequest($this->option.'.votes.filter_order',		'filter_order',		'v.date',	'cmd');
		$filter_order_Dir	= $this->mainframe->getUserStateFromRequest($this->option.'.votes.filter_order_Dir',	'filter_order_Dir',	'',			'word');
		$search				= $this->mainframe->getUserStateFromRequest($this->option.'.votes.search',				'search',			'',			'string');
		
		// Get data from the model
		$lists = $this->get('List');
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->title = $title;
		$this->lists = $lists;
		$this->votes = $this->get('Data');
		$this->pagination = $this->get('Pagination');
		$this->poll_id = MRequest::getInt('id', 0);
		
		parent::display($tpl);
	}
}
