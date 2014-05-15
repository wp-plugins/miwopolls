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

defined('MIWI') or die('Restricted access'); 

$document = MFactory::getDocument();
$document->setMimeEncoding('text/xml');

echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
echo $this->xml;