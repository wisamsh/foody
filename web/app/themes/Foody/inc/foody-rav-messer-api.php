<?php

class Rav_Messer_API_Handler
{
    private static $lists_array = [];

    public static function add_member_to_rav_messer($member_data)
    {
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

        self::do_subscribers_api_request_to_rav_messer('POST', $member_data, $post_data);
    }

    public static function remove_member_from_rav_messer_list($member_data)
    {
        $post_data = 'subscribers=' . json_encode(
                array(
                    array('EMAIL' => $member_data['member_email'])
                )
            );

        self::do_subscribers_api_request_to_rav_messer('DELETE', $member_data, $post_data);
    }

    private static function do_subscribers_api_request_to_rav_messer($request_method, $member_data, $post_data)
    {
        $course_rav_meeser_id = self::get_course_rav_messer_id($member_data['course_name']);
        if ($course_rav_meeser_id !== false) {
            $endpoint = '/lists/' . $course_rav_meeser_id . '/subscribers';
            $response = self::do_api_request($request_method, $endpoint, $post_data);
            if (isset($response->SUBSCRIBERS_CREATED) && !empty($response->SUBSCRIBERS_CREATED)) {
                // SUBSCRIBERS CREATED successfully
            } elseif (isset($response->DELETED_SUBSCRIBERS) && !empty($response->DELETED_SUBSCRIBERS)) {
                // SUBSCRIBERS DELETED successfully
            } else {
                $is_refund = $request_method == 'DELETE';
                self::handle_error_response_rav_messer($member_data, $response, $is_refund);
            }
        }
        else{
            //todo: list not exist
        }
    }

    private static function handle_error_response_rav_messer($member_data, $response, $is_refund = false)
    {
        $response_fields = !$is_refund ? ["EMAILS_INVALID", "EMAILS_EXISTING", "EMAILS_BANNED", "PHONES_INVALID", "PHONES_EXISTING", "ERRORS"] : ["INVALID_SUBSCRIBER_IDS", "INVALID_SUBSCRIBER_EMAILS"];
        $errors_log = '';
        foreach ($response_fields as $field) {
            if (isset($response->{$field}) && !empty($response->{$field})) {
                $response_field = is_array($response->{$field}) ? $response->{$field} : [$response->{$field}];
                foreach ($response_field as $response_val) {
                    $errors_log .= '<p>' . $field . ' : ' . $response_val . '</p>';
                }
            }
        }
        if (!empty($errors_log)) {
            self::send_api_error_log($member_data, $errors_log, $is_refund);
        }
    }

    private static function get_course_rav_messer_id($course_name)
    {
        $rav_messer_lists = self::get_all_lists();
        foreach ($rav_messer_lists as $list) {
            if ($course_name == $list['course_name']) {
                return $list['ID'];
            }
        }

        return false;
    }

    private static function get_all_lists()
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
            //todo: problem
        }

    }

    private static function do_api_request($request_method, $endpoint, $request_body = false)
    {
        $headers = array(self::createAuthDataHeader());
        $url = 'http://api.responder.co.il/main/' . $endpoint;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);

        if ($request_method == 'POST' && $request_body) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request_body);
        } elseif ($request_method == 'DELETE' && $request_body) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request_body);
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        $response = curl_exec($curl);
        $response_json = json_decode($response);

        curl_close($curl);
        return $response_json;
    }


    public static function createAuthDataHeader()
    {
        $timestamp = time();
        $nonce = md5(microtime() . mt_rand());

        return 'Authorization: c_key=' . urlencode(get_option('foody_consumer_key_for_ravmesser'))
            . ',c_secret=' . urlencode(md5(get_option('foody_consumer_secret_for_ravmesser') . $nonce))
            . ',u_key=' . urlencode(get_option('foody_access_token_for_ravmesser'))
            . ',u_secret=' . urlencode(md5(get_option('foody_token_secret_for_ravmesser') . $nonce))
            . ',nonce=' . urlencode($nonce)
            . ',timestamp=' . urlencode($timestamp);
    }

    private static function send_api_error_log($member_data, $error_log, $is_refund)
    {
        $to = get_option('foody_courses_admin_email');
        if (!empty($to)) {
            $subject = 'Rav Messer API Error Log';
            $body = self::create_api_error_log_mail_body($member_data, $error_log, $is_refund);
            $headers = array('Content-Type: text/html; charset=UTF-8');

            return wp_mail($to, $subject, $body, $headers);
        } else {
            return false;
        }
    }

    private static function create_api_error_log_mail_body($member_data, $error_log, $is_refund = false)
    {
        $mail_body = '<p>';
        if ($is_refund) {
            $mail_body .= __('ההסרה של המשתמש ') . $member_data['name'] . __(' מרשימות של רב מסר נכשלה');
        } else {
            $mail_body .= __('ההרשמה של המשתמש ') . $member_data['name'] . __(' לרשימות של רב מסר נכשלה');
        }
        $mail_body .= '</p>';
        $mail_body .= '<p>';
        $mail_body .= 'User Email: ' . $member_data['member_email'];
        $mail_body .= '</p>';
        $mail_body .= '<p>';
        $mail_body .= 'Phone: ' . $member_data['phone'];
        $mail_body .= '</p>';
        $mail_body .= '<p>';
        $mail_body .= 'Name: ' . $member_data['name'];
        $mail_body .= '</p>';
        $mail_body .= '<p>';
        $mail_body .= 'Course Name: ' . $member_data['course_name'];
        $mail_body .= '</p>';
        $mail_body .= '<p>';
        $mail_body .= 'Errors: ' . $error_log;
        $mail_body .= '</p>';

        return $mail_body;
    }
}


