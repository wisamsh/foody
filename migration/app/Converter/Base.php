<?php
/**
 * Created by PhpStorm.
 * User: liore
 * Date: 06/02/17
 * Time: 11:23
 */

namespace App\Converter;


class BaseConverter
{
    protected $originDB = null;
    protected $wp = null;
    protected $lang_code = [
        1 => 'he',
        2 => 'en',
        3 => 'fr',
        4 => 'es',
        5 => 'de',
        6 => 'ru',
        7 => 'ar',
        8 => 'fa',
    ];
    /**
     * @param $xml
     * @param bool $return_json
     * @return array|string
     */
    public function convertXml($xml, $return_json = true) {

       //return $xml;
        try {
            return $xml;

            //eturn gzcompress($xml);
        }
        catch (\ErrorException $e) {

            dump('XML Error');
            return $xml;

        }

    }
}