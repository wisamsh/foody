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

    if (WP_ENV == 'local'){
//        $ip = file_get_contents('https://ipecho.net/plain');
        $local_url = str_replace(WP_HOME,'localhost',$url);
        $ch = curl_init($local_url);
        if (strpos(WP_HOME,$url) !== false){
            curl_setopt($ch,CURLOPT_HTTPHEADER,[
                'Host: '. str_replace('http://','',WP_HOME),
            ]);
        }
    }else{
        $ch = curl_init($url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}