<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */

require_once plugin_dir_path(__FILE__) . "functions.php";
if (is_main_site()) {
    require_once plugin_dir_path(__FILE__) . "content-sync.php";
    require_once plugin_dir_path(__FILE__) . "sites.php";
}

require_once plugin_dir_path(__FILE__) . "acf.php";
require_once plugin_dir_path(__FILE__) . "http.php";
require_once plugin_dir_path(__FILE__) . "admin.php";