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

mimport('framework.html.pane');

class MiwopollsViewPoll extends MiwopollsView {

	function display($tpl = null) {
		$document = MFactory::getDocument();
  		$document->addStyleSheet(MURL_MIWOPOLLS.'/admin/assets/css/miwopolls.css');
		
		$cid = MRequest::getVar('cid', array(0), '', 'array');
		$edit = MRequest::getVar('edit', true);
		$text = (($edit) ? MText::_('Edit') : MText::_('New'));

		
		MToolBarHelper::apply();
		MToolBarHelper::save();
		MToolBarHelper::title(MText::_('COM_MIWOPOLLS_POLL').': <small><small>[ '.$text.' ]</small></small>', 'miwopolls');
		MToolBarHelper::Preview( MRoute::_('index.php?option=com_miwopolls&cid[]='.$cid[0]));
		MToolBarHelper::cancel();
			




		
		$this->mainframe = MFactory::getApplication();
		$user = MFactory::getUser();
		
		$row = $this->get('ItemData');

		// fail if checked out not by 'me'
		if ($row->isCheckedOut($user->get('id'))) {
			$msg = MText::sprintf('DESCBEINGEDITTED', MText::_('COM_MIWOPOLLS_THE_POLL'), $row->title);
			$this->setRedirect('index.php?option=com_miwopolls', $msg);
		}

		if ($row->id == 0) {
			$row->published	= 1;
		}

		$options = array();
		$ordering = array();

		if ($edit) {
			$options = $row->getOptions($row->id);
		}
		else {
			$row->lag = 24*60;
		}
		
		//default colors for slices
		$colors = array("ff0000","ffff99","00ccff","66ff99","ffcc00","d7ebff","ccffcc", "cccccc", "ffff00", "006699", "660000", "ffddee");

        $task = MRequest::getCmd('task');

		
		if ($task != 'preview') {
			$tpl = '30';
            $this->params = $this->get('Form');
		}
			












		
		$this->row = $row;
		$this->options = $options;
		$this->color = $colors;
		$this->edit = $edit;
		
		parent::display($tpl);
	}
}