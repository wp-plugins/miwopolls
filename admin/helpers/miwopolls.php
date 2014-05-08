<?php
/**
* @version		1.0.0
* @package		MiwoPolls
* @subpackage	MiwoPolls
* @copyright	2009-2011 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('MIWI') or die( 'Restricted access' );

abstract class MiwopollsHelper {

		public static function &get($filePath, $options = null) {
        static $instances = array();

        $parts = explode('.', $filePath);
        $class = array_pop($parts);

		if (!isset($instances[$class])) {
			require_once(MPATH_WP_PLG.'/miwopolls/admin/helpers/'.strtolower(str_replace('.', '/', $filePath)).'.php');
			
			$class_name = 'Miwopolls'.ucfirst($class);
			if (class_exists($class_name)) {
                $instances[$class] = new $class_name($options);
            }
		}

		return $instances[$class];
    }
	
	    public static function getConfig() {

        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            MError::raiseWarning('100', MText::sprintf('MiwoPolls requires PHP 5.2.x to run, please contact your hosting company.'));
            return false;
        }

        return MComponentHelper::getParams('com_miwopolls');

    }

    public static function storeConfig($config) {
        $config = $config->toString();

        $db = MFactory::getDBO();
        $db->setQuery('UPDATE #__options SET `option_value` = '.$db->Quote($config).' WHERE `option_name` = "miwopolls"');
        $db->query();
    }
			

	public static function is15() {
		static $status;
		
		if (!isset($status)) {
			if (version_compare(MVERSION, '1.6.0', 'ge')) {
				$status = false;
			}
			else {
				$status = true;
			}
		}
		
		return $status;
	}

	public static function is30() {
		static $status;
		
		if (!isset($status)) {
			if (version_compare(MVERSION, '3.0.0', 'ge')) {
				$status = true;
			}
			else {
				$status = false;
			}
		}
		
		return $status;
	}
}