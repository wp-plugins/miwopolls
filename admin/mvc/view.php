<?php
/*
* @package		MiwoPolls
* @copyright	2009-2014 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die ('Restricted access');

mimport('framework.application.component.view');

if (!class_exists('MiwisoftView')) {
    if (interface_exists('MView')) {
        abstract class MiwisoftView extends MViewLegacy {}
    }
    else {
        class MiwisoftView extends MView {}
    }
}

class MiwopollsView extends MiwisoftView {

    public function __construct() {
        parent::__construct();
    }
}
