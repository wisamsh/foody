<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */


if (is_main_site()){
    require_once plugin_dir_path(__FILE__) . "content-sync.php";
}

require_once plugin_dir_path(__FILE__) . "acf.php";
require_once plugin_dir_path(__FILE__) . "http.php";