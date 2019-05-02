<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/15/19
 * Time: 8:37 PM
 */

/**
 * @param $url
 * @return mixed
 *
 */
function foody_get($url)
{

    if (WP_ENV == 'local') {
        if (strpos($url, WP_HOME) !== false) {
            $parsed = parse_url($url);
            $parsed['port'] = '8080';
            $url = foody_build_url($parsed);
        }
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function foody_build_url(array $parts)
{
    return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
        ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
        (isset($parts['user']) ? "{$parts['user']}" : '') .
        (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
        (isset($parts['user']) ? '@' : '') .
        (isset($parts['host']) ? "{$parts['host']}" : '') .
        (isset($parts['port']) ? ":{$parts['port']}" : '') .
        (isset($parts['path']) ? "{$parts['path']}" : '') .
        (isset($parts['query']) ? "?{$parts['query']}" : '') .
        (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
}

/*
 * Modify HTTP header
 */

add_action('send_headers', 'foody_set_cors');
add_action('admin_init', 'foody_set_cors');
function foody_set_cors()
{

//    $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : get_site(get_main_site_id())->domain;
//    $allowed_http_origins = foody_get_cors();
//
//    if (in_array($http_origin, $allowed_http_origins)) {
//        @header("Access-Control-Allow-Origin: " . $http_origin);
//    }
    @header("Access-Control-Allow-Origin: *" );
}

function foody_get_cors()
{
    $sites = get_sites();

    $allowed_origins = array_map(function ($site) {
        return $site->domain;
    }, $sites);

    return $allowed_origins;
}
