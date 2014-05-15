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

class MiwopollsViewPolls extends MiwopollsView {

	function display($tpl = null) {
		$document = MFactory::getDocument();
  		$document->addStyleSheet(MURL_MIWOPOLLS.'/admin/assets/css/miwopolls.css');
		
		MToolBarHelper::title(MText::_('COM_MIWOPOLLS_POLLS'), 'miwopolls');
		MToolBarHelper::addNew();
		






		MToolBarHelper::preferences('com_miwopolls', 500);
	
		$this->mainframe = MFactory::getApplication();
		$this->option = MRequest::getWord('option');

		$filter_published		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_published',		'filter_published',		'',			'cmd');	
		$this->acl = MiwopollsHelper::get('acl');
		$options = array();
		$options[] = MHTML::_('select.option', '', MText::_('MOPTION_SELECT_PUBLISHED'));
		$options[] = MHTML::_('select.option', 1, MText::_('COM_MIWOVIDEOS_PUBLISHED'));
		$options[] = MHTML::_('select.option', 0, MText::_('COM_MIWOVIDEOS_UNPUBLISHED'));
		$lists['filter_published'] = MHTML::_('select.genericlist', $options, 'filter_published', ' class="inputbox"  ', 'value', 'text', $filter_published);

       
            $options = array();
            $options[] = MHTML::_('select.option', '', MText::_('Bulk Actions'));

            if ($this->acl->canEditState()) {
                $options[] = MHTML::_('select.option', 'publish', MText::_('MTOOLBAR_PUBLISH'));
                $options[] = MHTML::_('select.option', 'unpublish', MText::_('MTOOLBAR_UNPUBLISH'));
            }

            if ($this->acl->canManage()) {
                $options[] = MHTML::_('select.option', 'edit', MText::_('MTOOLBAR_EDIT'));
            }

            if ($this->acl->canDelete()) {
                $options[] = MHTML::_('select.option', 'remove', MText::_('MTOOLBAR_DELETE'));
                $options[] = MHTML::_('select.option', 'resetVotes', MText::_('MTOOLBAR_RESETVOTES'));
            }

            $lists['bulk_actions'] = MHTML::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			

		$filter_order		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order',		'filter_order',		'm.title',	'string');
		$filter_order_Dir	= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_order_Dir',	'filter_order_Dir',	'',			'word');
		$filter_state		= $this->mainframe->getUserStateFromRequest($this->option.'.polls.filter_state',		'filter_state',		'',			'word');
		$search				= $this->mainframe->getUserStateFromRequest($this->option.'.polls.search',				'search',			'',			'string');
		
		MHtml::_('behavior.tooltip');
		
		// state filter
		$lists['state']	= MHtml::_('grid.state', $filter_state);

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->user = MFactory::getUser();
		$this->lists = $lists;
		$this->items = $this->get('Data');
		$this->pagination = $this->get('Pagination');
		
		



		parent::display($tpl);
	}

		public function getIcon($i, $task, $img, $check_acl = false) {
        if ($check_acl and !$this->acl->canEditState()) {
            $html = '<img src="'.MURL_MIWOPOLLS.'/admin/assets/images/'.$img.'" border="0" />';
        }
        else {
            $html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')">';
            $html .= '<img src="'.MURL_MIWOPOLLS.'/admin/assets/images/'.$img.'" border="0" />';
            $html .= '</a>';
        }

		return $html;
		}
	
	    public function canEditState() {
        return $this->actions->get('core.edit.state');
		}
			
}