<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/2/19
 * Time: 2:54 PM
 */


/**
 * Clear all files from a directory
 * @uses glob()
 * @param $path string directory path
 */
function clear_directory($path)
{
    if (preg_match('/\*$/', $path) == false) {
        $path = trailingslashit($path);
        $path = "$path*";
    }

    $files = glob($path); // get all file names

    foreach ($files as $file) { // iterate files
        if (is_file($file))
            unlink($file); // delete file
    }
}


function foody_get_main_site_domain()
{
    $main = get_main_site_id();
    $site = get_site($main);
    return $site->domain;
}

function foody_get_main_site_url()
{
    return foody_add_scheme(foody_get_main_site_domain());
}

function foody_add_scheme($url, $scheme = 'http://')
{
    return parse_url($url, PHP_URL_SCHEME) === null ?
        $scheme . $url : $url;
}
