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
        $ch = curl_init($url);
    } else {
        $ch = curl_init($url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function foody_build_url(array $parts) {
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