<?php
/**
* @version		1.0.0
* @package		MiwoPolls
* @subpackage	MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwopolls/admin/helpers/miwopolls.php');

// Set the table directory
MTable::addIncludePath(MPATH_COMPONENT_ADMINISTRATOR.'/tables');

require_once(MPATH_COMPONENT_ADMINISTRATOR.'/mvc/model.php');
require_once(MPATH_COMPONENT_ADMINISTRATOR.'/mvc/view.php');
require_once(MPATH_COMPONENT_ADMINISTRATOR.'/mvc/controller.php');

// Require specific controller if requested
if ($controller = MRequest::getWord('view')) {
	$path = MPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'MiwopollsController'.ucfirst($controller);
$controller = new $classname();

$controller->registerTask('results', 'display');
$controller->execute(MRequest::getCmd('task'));
$controller->redirect();