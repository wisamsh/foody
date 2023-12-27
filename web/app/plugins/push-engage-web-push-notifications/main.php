<?php
/*
Plugin Name: PushEngage
Plugin URI: https://www.pushengage.com
Description: This plugin will push notifications for chrome, firefox, Opera, Microsoft Edge, Safari, UC Browser and Samsung Internet  browsers.
Version: 3.2.0
Author: PushEngage
Author URI: https://www.pushengage.com
License: GPLv2 or later
.
*/
error_reporting(0);
if(!defined( 'PUSHENGAGE_URL')) {
    define('PUSHENGAGE_URL', plugin_dir_url( __FILE__));
}

if(!defined('PUSHENGAGE_PLUGIN_DIR')) {
    define('PUSHENGAGE_PLUGIN_DIR', plugin_dir_path( __FILE__));
}

if(!defined('PUSHENGAGE_SITE_URL')) {
    define( 'PUSHENGAGE_SITE_URL', 'https://www.pushengage.com/');
}

if(!defined('PUSHENGAGE_PLUGIN_URL')) {
    $plugin_url = plugin_dir_url( __FILE__);
    $purl_arr = explode($_SERVER['HTTP_HOST'], $plugin_url);
    if(!isset($purl_arr[1])) {
        $purl_arr[1] = '/';
    }
    define('PUSHENGAGE_PLUGIN_URL', $purl_arr[1]);
}

require_once(plugin_dir_path( __FILE__ ) . 'core_class.php');
require_once(plugin_dir_path( __FILE__ ) . 'api_class.php');
Pushengage::init();

?>
