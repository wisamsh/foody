<?php

class Api_class {

    public static function remote_request( $remote_data, $content_type = '' ) {
        $remote_url = 'https://api.pushengage.com/apiv1/' . $remote_data['action'];
        $headers['api_key'] = $remote_data['api_key'];

        // by default, Content-Type is "application/x-www-form-urlencoded"
        if(!empty($content_type)) {
            $headers['Content-Type'] = $content_type;
        } else {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            // adding source and version in case of Content-Type' = application/x-www-form-urlencoded' only.
            $remote_data['remoteContent']['source'] =  'Wordpress '.get_bloginfo('version');
            $remote_data['remoteContent']['plugin_version'] = Pushengage::$pushengage_version;
        }

        $remote_array = array (
            'method'    => $remote_data['method'],
            'timeout'     => 10,
            'headers'   => $headers,
            'body'      => $remote_data['remoteContent'],
        );

        $response = wp_remote_request( esc_url_raw( $remote_url ), $remote_array );
        return $response;
    }

    public static function decode_request( $remote_data , $content_type = '') {
        $res = self::remote_request($remote_data, $content_type);
        $body = wp_remote_retrieve_body($res);
        // log the errors.
        self::sendApiErrorReport($body, $remote_data['action']);
        return  json_decode($body, true);
    }

    public static function verifyUser($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'info?type=verify_user',
            "method" => "GET",
        );

        $verify_usr_info = self::decode_request($request_data);
        return $verify_usr_info;
    }

    public static function filter_string($string) {
        $string = str_replace( '&#8220;', '&quot;', $string );
        $string = str_replace( '&#8221;', '&quot;', $string );
        $string = str_replace( '&#8216;', '&#39;', $string );
        $string = str_replace( '&#8217;', '&#39;', $string );
        $string = str_replace( '&#8211;', '-', $string );
        $string = str_replace( '&#8212;', '-', $string );
        $string = str_replace( '&#8242;', '&#39;', $string );
        $string = str_replace( '&#8230;', '...', $string );
        $string = str_replace( '&prime;', '&#39;', $string );
        return html_entity_decode($string, ENT_QUOTES);
    }

    // get segment information
    public static function getSegments($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'segments',
            "method" => "GET",
        );

        $segment_data = self::decode_request($request_data);
        return $segment_data;
    }

    // get general settings data
    public static function getGeneralSettings($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users/get_general_settings',
            "method" => "GET",
        );

        $get_general_settings_data = self::decode_request($request_data);
        return $get_general_settings_data;
    }

    // get subscription popup settings data
    public static function getSubscriptionPoupSettings($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users/get_subscription_poup_settings',
            "method" => "GET",
        );

        $get_subscription_poup_settings_data = self::decode_request($request_data);
        return $get_subscription_poup_settings_data;
    }

    // get subscription popup settings data
    public static function getWelcomeNotificationSettings($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users/get_welcome_notification_settings',
            "method" => "GET",
        );

        $get_welcome_notification_data = self::decode_request($request_data);
        return $get_welcome_notification_data;
    }

    // get site information
    public static function getSiteinfo($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'info?type=siteinfo',
            "method" => "GET",
        );

        $site_data = self::decode_request($request_data);
        return $site_data;
    }

    // get user, site and plan  information
    public static function getUserSitePlanInfo($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users/get_user_site_plan_info',
            "method" => "GET",
        );

        $response_data = self::decode_request($request_data);
        return $response_data;
    }

    // get list of automatic segmentation
    public static function getAutomaticSegmentList($api_key) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'automatic-segments',
            "method" => "GET",
        );

        $response_data = self::decode_request($request_data);
        return $response_data;
    }

    // send notification
    public static function sendNotification($api_key, $note_title, $note_text, $note_link, $segments=false, $image_url=false, $adv_options=false ) {

        $request_data = array();
        $remoteContent = array();
        $remoteContent['notification_title'] = self::filter_string($note_title);
        $remoteContent['notification_message'] = self::filter_string($note_text);
        $remoteContent['notification_url'] = $note_link;

        if (!empty($segments)) {
            $remoteContent['include_segments'] = $segments;
        }

        if(!empty($image_url)) {
            $remoteContent['image_url'] = $image_url;
        }

        if (!empty($adv_options['segments'])) {
            $remoteContent['include_segments'] = $adv_options['segments'];
        }

        if ( !empty($adv_options['require_interaction']) ) {
            $remoteContent['require_interaction'] = $adv_options['require_interaction'];
        } else {
            $remoteContent['require_interaction'] = '0';
        }

        if(!empty($adv_options['big_image_url'])) {
            $remoteContent['big_image_url'] = $adv_options['big_image_url'];
        }

        $request_data['action'] = "notifications?post_id=".$adv_options['post_id']."&old_status=".$adv_options['old_status']."&new_status=".$adv_options['new_status'];
        $request_data['method'] = "POST";
        $request_data['api_key'] = $api_key;
        $request_data['remoteContent'] = !empty($remoteContent)?$remoteContent:array();
        $response = self::decode_request($request_data);
        return $response;

    }


    // update site settings
    public static function updateSiteSettings($api_key, $data) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users',
            "method" => "POST",
            "remoteContent" => $data,
        );

        $result = self::decode_request($request_data);
        return $result;
    }

    // update user profile settings
    public static function updateProfileSettings($api_key, $data) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users',
            "method" => "POST",
            "remoteContent" => $data,
        );

        $result = self::decode_request($request_data);
        return $result;
    }

    // updated subscription dailog box settings
    public static function updateSubscriptionboxSettings($api_key, $data) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users/update_subscriptionbox_settings',
            "method" => "POST",
            "remoteContent" => $data,
        );

        $result = self::decode_request($request_data);
        return $result;
    }

    // update welcome notification settings
    public static function updateWelcomeNotification($api_key, $data) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'users',
            "method" => "POST",
            "remoteContent" => $data,
        );

        $gcm_data = self::decode_request($request_data);
        return $gcm_data;
    }

    // update intermediate page settings
    public static function updateOptinSettings($api_key, $data) {
        $request_data = array(
            "api_key" => $api_key,
            "action" => 'users',
            "method" => "POST",
            "remoteContent"=>$data,
        );

        $result = self::decode_request($request_data);
        return $result;
    }

    // create automatic segmentation.
    public static function createAutomaticSegmentation($api_key, $data) {
        $content_type = 'application/json';
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'automatic-segments',
            "method" => "PUT",
            "remoteContent" => $data,
        );

        $result = self::decode_request($request_data, $content_type);
        return $result;
    }

    // update automatic segmentation.
    public static function updateAutomaticSegmentation($api_key, $data) {
        $content_type = 'application/json';
        $request_data = array (
            "api_key" => $api_key,
            "action" => 'automatic-segments',
            "method" => "PATCH",
            "remoteContent" => $data,
        );

        $result = self::decode_request($request_data, $content_type);
        return $result;
    }

    // error logging system.
    public static function logErrorReport($payload) {
        try {
            $request_payload = array(
                'method'    => 'POST',
                'blocking'  => false,
                'headers'   => array("Content-Type" => 'application/json',),
                'body'      => json_encode($payload),
                'timeout'	=> 5,
            );

            $res = wp_remote_request('https://notify.pushengage.com/v1/logs', $request_payload);
            return;
        } catch(Exception $e) {
            return;
        }
    }

    // api response error logging if api returns error response
    public static function sendApiErrorReport($body, $action ) {
        $response_body = json_decode($body, true);

        if( (isset($response_body['success']) && $response_body['success'] !== false) || $response_body !== null ) {
            return;
        }

        // wp_remote_request return WP_Error on failure
        $network_error = is_wp_error($response)?'Newtwork Error':'';
        $pushengage_settings = get_option('pushengage_settings');
        unset($pushengage_settings['appKey']);

        $payload = array (
            "name"=> "wordpressApiError",
            "app"=> "wordpressPlugin",
            "version"=> Pushengage::$pushengage_version,
            "data" => array (
                "site_name" => $pushengage_settings['site_name'],
                "site_key" => $pushengage_settings['site_key'],
                "wordpressVersion" => get_bloginfo('version'),
                "setting" => $pushengage_settings,
                "apiAction" => $action,
                "error" => $network_error,
                "apiResponse"  => $response_body,
            )
        );

        self::logErrorReport($payload);
    }

    // error logging if error occured in the code.
    public static function sendCodeErrorReport($error_data) {
        $pushengage_settings = get_option('pushengage_settings');
        unset($pushengage_settings['appKey']);

        $payload = array(
            "name"=> "wordpressCodeError",
            "app"=> "wordpressPlugin",
            "version"=> Pushengage::$pushengage_version,
            "data" => array (
                "site_name" => $pushengage_settings['site_name'],
                "site_key" => $pushengage_settings['site_key'],
                "wordpressVersion" => get_bloginfo('version'),
                "setting" => $pushengage_settings,
                "error" => $error_data,
            )
        );

        self::logErrorReport($payload);
    }

    // update service worker settings
    public static function updateServiceWorkerSetting($api_key, $data) {
        $request_data = array (
            "api_key" => $api_key,
            "action" => "users",
            "method" => "POST",
            "remoteContent" => $data,
        );

        $service_worker_setting = self::decode_request($request_data);
        return $service_worker_setting;
    }

}
?>
