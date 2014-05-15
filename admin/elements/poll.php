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

mimport('framework.html.parameter.element');

class JElementPoll extends JElement {

	var	$_name = 'Poll';

	function fetchElement($name, $value, &$node, $control_name) {
		$db = MFactory::getDBO();
        
		$query = 'SELECT a.id, a.title'
		. ' FROM ' . $db->nameQuote("#__miwopolls_polls") . ' AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.title'
		;
		$db->setQuery( $query );
		$options = $db->loadObjectList();
        
        if (MRequest::getCmd('option') == "com_modules") {
            array_unshift($options, MHtml::_('select.option', '', '- - - - - - - - - - -', 'id', 'title'));  
            array_unshift($options, MHtml::_('select.option', '0', MText::_('Show random poll'), 'id', 'title'));        
        } else {
            array_unshift($options, MHtml::_('select.option', '0', '- - '.MText::_('Select Poll').' - -', 'id', 'title'));  
        }

		return MHtml::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'title', $value, $control_name.$name );
	}
}
