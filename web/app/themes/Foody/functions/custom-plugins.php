<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/25/18
 * Time: 4:07 PM
 */


$plugins = array('users-order');

$custom_plugins_path = get_template_directory() . '/inc/plugins/';

foreach ( $plugins as $plugin ) {
	require_once $custom_plugins_path . $plugin . '/index.php';
}


