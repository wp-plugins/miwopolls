<?php
/*
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.application.component.model');

if (!class_exists('MiwisoftModel')) {
	if (interface_exists('MModel')) {
		abstract class MiwisoftModel extends MModelLegacy {}
	}
	else {
		class MiwisoftModel extends MModel {}
	}
}

class MiwopollsModel extends MiwisoftModel {

	public function __construct() {
		parent::__construct();
	}
}
