<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/14/19
 * Time: 5:37 PM
 */

add_filter('acfwpcli_fieldgroup_paths', 'foody_add_acf_cli_path');
function foody_add_acf_cli_path($paths)
{
    $paths['foody'] = WEB_ROOT . '/acf-json/';
    return $paths;
}