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

class MiwopollsViewPolls extends MiwisoftView {

	function display($tpl = null) {
		$this->mainframe = MFactory::getApplication();
		$this->option = MRequest::getCmd('option');

		$filter_order		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order',		'filter_order',			'm.title',	'string');
		$filter_order_Dir	= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order_Dir',	'filter_order_Dir',		'',			'word');
		$search				= $this->mainframe->getUserStateFromRequest($this->option.'.polls.search',				'search',				'',			'string');

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;
		
		MHtml::_('behavior.tooltip');
		
					
			$menu = MFactory::getApplication()->getMenu()->getActive();
			
			$menu_params = new MRegistry($menu->params);
			$params = clone($this->mainframe->getParams());
			$params->merge($menu_params);









		
		$this->lists = $lists;
		$this->params = $params;
		$this->items = $this->get('Data');
		$this->pagination = $this->get('Pagination');
		
		



		parent::display($tpl);
	}
}