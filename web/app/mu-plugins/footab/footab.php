<?php

/*
Plugin Name: Foody Taboola
Plugin URI: http://www.foody.co.il
Description: A plugin to easily install the foody taboola options
Version: 1.0
Author: Uri Chachick
Author URI: http://www.glove.co.il
License: A "Slug" license name e.g. GPL2
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  footab
Domain Path:  /languages
*/

load_plugin_textdomain('footab', false, basename(dirname(__FILE__)) . '/languages');


define('FOOTAB_PATH', plugin_dir_path(__FILE__));
define('FOOTAB_URL', plugin_dir_url(__FILE__));

include(FOOTAB_PATH . 'footab_cpts.php');
include(FOOTAB_PATH . 'footab_rsss.php');
include(FOOTAB_PATH . 'footab_rsssposts.php');
require_once  FOOTAB_PATH . 'cmb2/init.php';
