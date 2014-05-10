<?php
/*
Plugin Name: MiwoPolls
Plugin URI: http://miwisoft.com
Description: MiwoPolls is a simple and flexible component for voting. It shows vote results in 2 types, a nice pie chart or default table style.
Author: Miwisoft LLC
Version: 1.0.1
Author URI: http://miwisoft.com
Plugin URI: http://miwisoft.com/wordpress-plugins/miwopolls-wordpress-polls-simplified
*/

defined('ABSPATH') or die('MIWI');

if (!class_exists('MWordpress')) {
    require_once(dirname(__FILE__) . '/wordpress.php');
}

final class MPolls extends MWordpress {

	public function __construct() {
		if (!defined('MURL_MIWOPOLLS')) {
			define('MURL_MIWOPOLLS', plugins_url('', __FILE__));
		}
		
		parent::__construct('miwopolls', '33.0005');
	}
	
	public function initialise() {
		$miwi = MPATH_WP_CNT.'/miwi/initialise.php';

		if (!file_exists($miwi)) {
			return false;
		}

		require_once($miwi);

		$this->app = MFactory::getApplication();

		$this->app->initialise();
		
		// Voit for cookie set
		$session = MFactory::getSession();
		$cookieName = $session->get('miwopollscookie');
		$cookielag = $session->get('miwopollscookielag');
		setcookie($cookieName, '1', time() + 60*$cookielag);
	}
}

$mpolls = new MPolls();

register_activation_hook(__FILE__, array($mpolls, 'activate'));
register_deactivation_hook(__FILE__, array($mpolls, 'deactivate'));