<?php

class Rav_Messer_API_Handler
{
    private static $consumer_key = 'D3DE58D4CB22C707DC398CC4A861B69C';
    private static $consumer_secret = 'B0D59EF9827533B54EFFCF2F52FDCE72';
    private static $access_token = '3148CBA7BF711ADBB4635FE925A19A1B';
    private static $token_secret = 'BE8E29946D892DD801EAABF1DFFE38D8';
    private static $lists_array = [];

    public static function add_member_to_rav_messer($member_data, $course_name)
    {
        $course_rav_meeser_id = self::get_course_rav_messer_id($course_name);
        if ($course_rav_meeser_id !== false) {
            $request_method = 'POST';
            $endpoint = '/lists/' . $course_rav_meeser_id . '/subscribers';
            $post_data = 'subscribers=' . json_encode(
                    array(
                        array(
                            'EMAIL' => $member_data['member_email'],
                            'NAME' => $member_data['name'],
                            'PHONE' => $member_data['phone'],
                            'NOTIFY' => 2
                        )
                    )

                );
            self::do_api_request($request_method, $endpoint, $post_data);
        }
    }

    public static function get_course_rav_messer_id($course_name)
    {
        $rav_messer_lists = self::get_all_lists();
        foreach ($rav_messer_lists as $list) {
            if ($course_name == $list['course_name']) {
                return $list['ID'];
            }
        }

        return false;
    }

    public static function get_all_lists()
    {
        if (!empty(self::$lists_array)) {
            return self::$lists_array;
        }

        $endpoint = '/lists';
        $request_method = 'GET';

        $request_result = self::do_api_request($request_method, $endpoint);
        if (isset($request_result->LISTS) && is_array($request_result->LISTS)) {
            self::$lists_array = array_map(function ($list) {
                if (isset($list->ID) && isset($list->DESCRIPTION)) {
                    return ['ID' => $list->ID, 'course_name' => $list->DESCRIPTION];
                }
            }, $request_result->LISTS);

            return self::$lists_array;
        } else {
            // problem
        }

    }

    private static function do_api_request($request_method, $endpoint, $request_body = false)
    {
//        $date = new DateTime();  $date->getTimestamp() k415u7HT9oG
        $timestamp = time();
        $nonce = md5(mt_rand());

        $signature = self::generate_signature($request_method, "http://api.responder.co.il/v1.0" . $endpoint, $timestamp, $nonce);
        $curl_data_array = array(
            CURLOPT_URL => "http://api.responder.co.il/v1.0" . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request_method,
            CURLOPT_HTTPHEADER => array(
                "Authorization: OAuth oauth_consumer_key=\"" . self::$consumer_key . "\",oauth_token=\"" . self::$access_token . "\",oauth_signature_method=\"HMAC-SHA1\",oauth_timestamp=\"" . $timestamp . "\",oauth_nonce=\"" . $nonce . "\",oauth_version=\"1.0\",oauth_signature=\"" . $signature . "\"",
                "Content-Type: application/json"
            ),
        );

        if ($request_method == "POST" || $request_body !== false) {
            $curl_data_array[CURLOPT_POSTFIELDS] = $request_body;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $curl_data_array);

        $response = curl_exec($curl);
        $response_json = json_decode($response);

        curl_close($curl);
        return $response_json;
    }

    private static function generate_signature($method_request, $url, $timestamp, $nonce)
    {
        $base = $method_request . '&' . rawurlencode($url) . '&'
            . rawurlencode("oauth_consumer_key=" . rawurlencode(self::$consumer_key) . '&'
                . 'oauth_nonce=' . rawurlencode($nonce)
                . '&oauth_signature_method=' . rawurlencode('HMAC-SHA1')
                . '&oauth_timestamp=' . rawurlencode($timestamp)
                . '&oauth_token=' . rawurlencode(self::$access_token)
                . '&oauth_version=' . rawurlencode('1.0'));

        $key = rawurlencode(self::$consumer_secret) . '&' . rawurlencode(self::$token_secret);

        return base64_encode(hash_hmac("sha1", $base, $key, true));
    }
}

//    static function createAuthDataHeader($auth_detailed){
//        return 'Authorization: c_key=' . urlencode($auth_detailed['client_key'])
//            .',c_secret='.urlencode(md5($auth_detailed['client_secret'].$auth_detailed['nonce']))
//            .',u_key='.urlencode($auth_detailed['user_key'])
//            .',u_secret='.urlencode(md5($auth_detailed['user_secret'].$auth_detailed['nonce']))
//            .',nonce='.urlencode($auth_detailed['nonce'])
//            .',timestamp='.urlencode($auth_detailed['timestamp']);
//    }

//    public static function createAuthDataHeader()
//    {
//        $client_key = 'D3DE58D4CB22C707DC398CC4A861B69C';
//        $client_secret = 'B0D59EF9827533B54EFFCF2F52FDCE72';
//
//        $user_key    = '3148CBA7BF711ADBB4635FE925A19A1B';
//        $user_secret = 'BE8E29946D892DD801EAABF1DFFE38D8';
//        $timestamp = time();
//        $nonce = md5(microtime() . mt_rand());
//
//        return 'Authorization: c_key=' . urlencode($client_key)
//            . ',c_secret=' . urlencode(md5($client_secret . $nonce))
//            . ',u_key=' . urlencode($user_key)
//            . ',u_secret=' . urlencode(md5($user_secret . $nonce))
//            . ',nonce=' . urlencode($nonce)
//            . ',timestamp=' . urlencode($timestamp);
//    }
//}


