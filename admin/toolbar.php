<?php
/**
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');


$views = array( '&controller=polls'			=> MText::_('COM_MIWOPOLLS_POLLS'),
				'&controller=votes'			=> MText::_('COM_MIWOPOLLS_VOTES')
	);
if (!class_exists('JSubMenuHelper')) {
	return;
}
require_once(MPATH_COMPONENT.'/helpers/helper.php');
MHTML::_('behavior.switcher');

if (MRequest::getInt('hidemainmenu') != 1) {
	JSubMenuHelper::addEntry(MText::_('COM_MIWOPOLLS_POLLS'), 'index.php?option=polls&controller=polls', MiwopollsHelper::isActiveSubMenu('Polls'));
	JSubMenuHelper::addEntry(MText::_('COM_MIWOPOLLS_VOTES'), 'index.php?option=polls&controller=votes', MiwopollsHelper::isActiveSubMenu('Votes'));

}
			








