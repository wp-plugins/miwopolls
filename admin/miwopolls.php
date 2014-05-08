<?php
/**
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwopolls/admin/helpers/miwopolls.php');

require_once(MPATH_COMPONENT.'/mvc/model.php');
require_once(MPATH_COMPONENT.'/mvc/view.php');
require_once(MPATH_COMPONENT.'/mvc/controller.php');

require_once(MPATH_COMPONENT.'/toolbar.php');

MTable::addIncludePath(MPATH_COMPONENT.'/tables');

if ($controller = MRequest::getWord('controller')) {
	$path = MPATH_COMPONENT.'/controllers/'.$controller.'.php';

	if (file_exists($path)) {
		require_once $path;
	}
	else {
		$controller = '';
	}
}

$classname = 'MiwopollsController'.ucfirst($controller);

// Create the controller
$controller = new $classname();
$controller->execute(MRequest::getCmd('task'));
$controller->redirect();