<?php

function foody_start_bit_pay_process()
{
    $token = Bit_Token_Manager::get_token();

    // add new pending payment to db
    $pending_payment_id = insert_new_pending_payment();
    if ($pending_payment_id != false) {
        $single_payment_ids = do_single_payment_bit($token, $pending_payment_id);

        //update pending table row
        if (isset($single_payment_ids['paymentInitiationId']) && isset($single_payment_ids['transactionSerialId'])) {
            update_pending_payment($pending_payment_id, $single_payment_ids);
        }

        wp_send_json_success(['single_payment_ids' => $single_payment_ids]);
    }
}

add_action('wp_ajax_foody_nopriv_start_bit_pay_process', 'foody_start_bit_pay_process');
add_action('wp_ajax_foody_start_bit_pay_process', 'foody_start_bit_pay_process');


function do_single_payment_bit($token, $id)
{
    $cert_path = get_certificate_data();
    $amount = isset($_POST['price']) ? doubleval($_POST['price']) : false;
    $item_name = isset($_POST['item_name']) ? __('עבור ') . $_POST['item_name'] : false;
    $franchisingId = 32;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/payments/bit/v2/single-payments",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\r\n  \"currencyTypeCode\": 1,\r\n  \"debitMethodCode\": 2,\r\n  \"externalSystemReference\": " . $id . ",\r\n  \"franchisingId\": " . $franchisingId .
            ",\r\n  \"requestAmount\":" . $amount . ",\r\n  \"requestSubjectDescription\": " . $item_name . "\r\n}\r\n",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json;charset=UTF-8",
            "Ocp-Apim-Subscription-Key: ",
            "Content-Type: text/plain"
        ),
    ));

    curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);

    $response = curl_exec($curl);
    $response_json = json_decode($response);

    curl_close($curl);
    if (isset($response_json->paymentInitiationId) && isset($response_json->transactionSerialId)) {
        return ['paymentInitiationId' => $response_json->paymentInitiationId, 'transactionSerialId' => $response_json->transactionSerialId];
    } else {
        return false;
    }

}

function get_certificate_data()
{
    $pfx_path = get_template_directory() . '/inc/certificates/partner12-pre-poalim-api.pfx';
    $pfx_pass = 'Aa123456';
    $pfx_values = [];

    openssl_pkcs12_read(file_get_contents($pfx_path), $pfx_values, $pfx_pass);

    $cert_path = get_template_directory() . '/inc/certificates/me.pem';
    file_put_contents($cert_path, $pfx_values['cert'] . "\n" . $pfx_values['pkey']);

    return $cert_path;
}

function insert_new_pending_payment()
{
    $email = isset($_POST['email']) ? $_POST['email'] : false;
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : false;
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : false;
    $last_id = false;

    if ($email && $first_name && $last_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'foody_pre_payment_bit';

        $insert_query = "INSERT INTO {$table_name} ( email, first_name, last_name, bit_paymentInitiationId, bit_transactionSerialId, status)  
                            VALUES('$email','$first_name','$last_name', null ,null,'pending')";

        $wpdb->query($insert_query);
        $last_id = $wpdb->insert_id;
    }

    return $last_id;
}

function update_pending_payment($id, $data)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_pre_payment_bit';

    $update_query = "UPDATE {$table_name} SET bit_paymentInitiationId = '". $data['paymentInitiationId'] . "', bit_transactionSerialId = '". $data['transactionSerialId'] ."' WHERE coupon_id = ".$id;

    $wpdb->query($update_query);
}

class Bit_Token_Manager
{
    private static $token;
    private static $token_expiration_time;

    public static function get_token()
    {
        if (isset(self::$token) && self::$token_expiration_time) {
            if (strtotime(date("m/d/Y h:i:s a", time())) < strtotime(self::token_expiration_time)) {
                return self::$token;
            }
        }

        // generate new token
        $subscription_key = get_option('foody_subscription_key_for_bit', false);
        $token_json = self::get_token_call($subscription_key);

        if ($token_json && isset($token_json->expires_in) && isset($token_json->access_token)) {
            self::$token = $token_json->access_token;
            self::$token_expiration_time = date("m/d/Y h:i:s a", time() + $token_json->expires_in);
        }
        return self::$token;
    }

    private static function get_token_call($subscription_key)
    {
        $client_id = get_option('foody_client_id_for_bit', false);
        $client_secret = get_option('foody_client_secret_for_bit', false);
        $cert_path = get_certificate_data();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/bank/auth/clients/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "client_id=" . $client_id . "&client_secret=" . $client_secret . "&response_type=token&scope=bit_payment",
            CURLOPT_HTTPHEADER => array(
                "Ocp-Apim-Subscription-Key: " . $subscription_key,
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);


        $response = curl_exec($curl);
        $response_json = json_decode($response);

        curl_close($curl);
        return $response_json;
    }
}