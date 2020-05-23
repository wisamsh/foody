<?php
function foody_get_course_price()
{
    $course_id = $_POST['course_id'];
    return wp_send_json_success(['course_price' => (float)get_field('course_register_data_final_price', $course_id)]);
}

add_action('wp_ajax_foody_nopriv_get_course_price', 'foody_get_course_price');
add_action('wp_ajax_foody_get_course_price', 'foody_get_course_price');

function foody_start_bit_pay_process()
{
    $token = Bit_Token_Manager::get_token();

    // add new pending payment to db
    $pending_payment_id = insert_new_pending_payment();
    if ($pending_payment_id != false) {
        $single_payment_ids = do_single_payment_bit($token, $pending_payment_id, $_POST['memberData']);

        wp_send_json_success(['single_payment_ids' => $single_payment_ids]);
    }
}

add_action('wp_ajax_foody_nopriv_start_bit_pay_process', 'foody_start_bit_pay_process');
add_action('wp_ajax_foody_start_bit_pay_process', 'foody_start_bit_pay_process');


function foody_bitcom_transaction_complete()
{
    $payment_initiation_id = isset($_POST['paymentInitiationId']) ? $_POST['paymentInitiationId'] : false;
    $member_data = isset($_POST['memberData']) ? $_POST['memberData'] : false;
    $coupon_id = isset($_POST['couponId']) ? $_POST['couponId'] : false;
    $coupon_type = isset($_POST['couponType']) ? $_POST['couponType'] : false;
    $coupon_code = isset($_POST['couponCode']) ? $_POST['couponCode'] : false;

    if ($payment_initiation_id && $member_data) {
        $coupon_details = null;
        if ($coupon_id && $coupon_type && $coupon_code) {
            $coupon_details = ['id' => $coupon_id, 'type' => $coupon_type, 'coupon_code' => $coupon_code];
        }
        foody_query_process_for_bit_status($payment_initiation_id, $member_data, $coupon_details);
    } else {
        // todo: handle error
    }
}

add_action('wp_ajax_foody_nopriv_bitcom_transaction_complete', 'foody_bitcom_transaction_complete');
add_action('wp_ajax_foody_bitcom_transaction_complete', 'foody_bitcom_transaction_complete');

function foody_bit_refund_process()
{
    if (isset($_POST['paymentInitiation_id']) && $_POST['paymentInitiation_id']) {
        $token = Bit_Token_Manager::get_token();
        $cert_path = get_certificate_data();
        $subscription_key = get_option('foody_subscription_key_for_bit', false);
        $bit_transaction_id_and_status = get_id_and_status_by_paymentInitiationId($_POST['paymentInitiation_id']);
        if (isset($bit_transaction_id_and_status->bit_trans_id)) {
            $ids_and_price_paid_obj = get_columns_data_by_paymentMethodId($bit_transaction_id_and_status->bit_trans_id, ['member_id', 'price_paid']);
            if (isset($ids_and_price_paid_obj->price_paid) && isset($ids_and_price_paid_obj->member_id)) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/payments/bit/v2/refund",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\n    \"creditAmount\": " . $ids_and_price_paid_obj->price_paid . ",\n    \"currencyTypeCode\": 1,\n    \"externalSystemReference\": \"bit_trans_" . $bit_transaction_id_and_status->bit_trans_id . "\",\n    \"paymentInitiationId\": \"" . $_POST['paymentInitiation_id'] . "\",\n    \"refundExternalSystemReference\": \"bit_trans_" . $bit_transaction_id_and_status->bit_trans_id . "_refund\"\n}",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer " . $token,
                        "Content-Type: application/json;charset=UTF-8",
                        "Ocp-Apim-Subscription-Key: " . $subscription_key
                    ),
                ));

                curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);

                $response = curl_exec($curl);
                $response_json = json_decode($response);

                curl_close($curl);

                if (isset($response_json->requestStatusCode) && isset($response_json->issuerAuthorizationNumber)) {
                    $is_refunded = !bit_handle_status_code($response_json->requestStatusCode);
                    if ($is_refunded) {
                        update_pre_pay_bit_data_by_id_and_cloumns($bit_transaction_id_and_status->bit_trans_id, ['status' => 'refunded', 'authorization_number' => $response_json->issuerAuthorizationNumber]);
                        update_course_member_by_id_and_cloumns($ids_and_price_paid_obj->member_id, ['status' => 'refunded']);
                        return wp_send_json_success(['msg' => __('העסקה עם מזהה ' . $ids_and_price_paid_obj->member_id . ' בוטלה')]);
                    }
                } else {
                    wp_send_json_error(array(
                        'error' => __('הזיכוי נכשל')
                    ));
                }

            }
        }
    }

}

add_action('wp_ajax_foody_nopriv_bit_refund_process', 'foody_bit_refund_process');
add_action('wp_ajax_foody_bit_refund_process', 'foody_bit_refund_process');

function get_payment_status($payment_initiation_id, $member_data)
{
    $token = Bit_Token_Manager::get_token();
    $cert_path = get_certificate_data();
    $subscription_key = get_option('foody_subscription_key_for_bit', false);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/payments/bit/v2/single-payments/" . $payment_initiation_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $token,
            "Ocp-Apim-Subscription-Key: " . $subscription_key,
            "Content-Type: application/json;charset=UTF-8"
        ),
    ));
    curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);

    $response = curl_exec($curl);
    $response_json = json_decode($response);

    curl_close($curl);
    return isset($response_json->requestStatusCode) ? $response_json->requestStatusCode : $response_json;
}

function bit_handle_status_code($code, $payment_initiation_id = null, $member_data = null, $coupon_details = null)
{
    $result = '';
    switch ($code) {
        case 11:
            // payment confirmed - final
            send_new_course_member_data([
                'member_email' => $member_data['email'],
                'phone' => $member_data['phone'],
                'name' => $member_data['first_name'] . ' ' . $member_data['last_name'],
                'course_name' => $member_data['course_name'],
                'price' => $member_data['price'],
                'enable_marketing' => $member_data['enable_marketing']
            ]);
            $result = false;
            break;
        case 10:
            // refund confirmed - final
            $result = false;
            break;
        case 7:
            // time expired - final
            do_delete_bit_transaction($payment_initiation_id);
            $result = false;
            break;
        case 2:
            // canceled by business or failed - final
            $result = false;
            break;
        case 3:
            // canceled by client before money is held - final
            do_delete_bit_transaction($payment_initiation_id);
            $result = false;
            break;
        case 15:
            // payment failed - final
            do_delete_bit_transaction($payment_initiation_id);
            $result = false;
            break;
        case 16:
            // refund failed - final
            $result = false;
            break;
        case 4:
        case 12:
        case 13:
        case 14:
        case 17:
            // request still pending we need to keep asking the server for final result
            $result = true;
            break;
        case 9:
            // payment is being held => can preform capture
            $result = true;
            $ids_and_authorization_number = do_bit_payment_capture($payment_initiation_id);
            if (is_array($ids_and_authorization_number) && isset($ids_and_authorization_number['trans_id']) && isset($ids_and_authorization_number['issuerAuthorizationNumber'])) {
                update_pre_pay_bit_data_by_id_and_cloumns($ids_and_authorization_number['trans_id'], ['status' => 'paid', 'authorization_number' => $ids_and_authorization_number['issuerAuthorizationNumber']]);
                update_course_member_by_id_and_cloumns($ids_and_authorization_number['member_id'], ['status' => 'paid']);
                if ($coupon_details != null) {
                    update_coupon_to_used($coupon_details);
                }
            }
            break;
        default:
            $result = true;
            break;
    }
    return $result;
}

function do_delete_bit_transaction($paymentInitiationId)
{
    $bit_transaction_id_and_status = get_id_and_status_by_paymentInitiationId($paymentInitiationId);
    if (isset($bit_transaction_id_and_status->bit_trans_id) && isset($bit_transaction_id_and_status->status)) {
        $member_id = get_columns_data_by_paymentMethodId($bit_transaction_id_and_status->bit_trans_id, ['member_id', 'price_paid']);
        if ($bit_transaction_id_and_status->status != 'pending' && isset($member_id->member_id)) {
            $token = Bit_Token_Manager::get_token();
            $cert_path = get_certificate_data();
            $subscription_key = get_option('foody_subscription_key_for_bit', false);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/payments/bit/v2/single-payments/62D2D620-E3D7-4A37-82D6-74B5A92528FF",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $token,
                    "Content-Type: application/json;charset=UTF-8",
                    "Ocp-Apim-Subscription-Key: " . $subscription_key
                ),
            ));

            curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);

            $response = curl_exec($curl);
            $response_json = json_decode($response);

            curl_close($curl);
            if (isset($response_json->requestStatusCode) && $response_json->requestStatusCode == 2) {
                update_pre_pay_bit_data_by_id_and_cloumns($bit_transaction_id_and_status->bit_trans_id, ['status' => 'canceled']);
                update_course_member_by_id_and_cloumns($bit_transaction_id_and_status->bit_trans_id, ['status' => 'canceled']);
            }
        }
    }
}

function do_single_payment_bit($token, $id, $member_data)
{
    $cert_path = get_certificate_data();
    $subscription_key = get_option('foody_subscription_key_for_bit', false);
    $amount = isset($_POST['price']) ? doubleval($_POST['price']) : false;
    $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : false;
    $franchisingId = 32;
    $bit_trans_id = 'bit_trans_' . $id;


    $curl = curl_init();

    $request_body = "{\r\n  \"currencyTypeCode\": 1,\r\n  \"debitMethodCode\": 2,\r\n  \"externalSystemReference\": \"" . $bit_trans_id . "\",\r\n  \"franchisingId\": \"" . $franchisingId .
        "\",\r\n  \"requestAmount\":" . $amount . ",\r\n  \"requestSubjectDescription\": \"" . __($item_name) . "\"\r\n}\r\n";

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/payments/bit/v2/single-payments",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $request_body,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $token,
            "Content-Type: application/json;charset=UTF-8",
            "Ocp-Apim-Subscription-Key: " . $subscription_key,
        ),
    ));

    curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);

    $response = curl_exec($curl);
    $response_json = json_decode($response);

    curl_close($curl);
    if (isset($response_json->paymentInitiationId) && isset($response_json->transactionSerialId)) {
        $member_data['transaction_id'] = $response_json->paymentInitiationId;
        $member_data['payment_method_id'] = $id;

        update_pre_pay_bit_data_by_id_and_cloumns($id, ['bit_paymentInitiationId' => $response_json->paymentInitiationId, 'bit_transactionSerialId' => $response_json->transactionSerialId]);
        foody_add_course_member_to_table($member_data);
        return ['paymentInitiationId' => $response_json->paymentInitiationId, 'transactionSerialId' => $response_json->transactionSerialId, 'paymentMethodId' => $id];
    } else {
        return false;
    }
}

function do_bit_payment_capture($paymentInitiationId)
{
    $token = Bit_Token_Manager::get_token();
    $cert_path = get_certificate_data();
    $subscription_key = get_option('foody_subscription_key_for_bit', false);
    $bit_transaction_data = get_pre_pay_bit_data_by_paymentInitiationId($paymentInitiationId);
    $response = false;
    if (isset($bit_transaction_data->bit_trans_id)) {
        $bit_trans_id = 'bit_trans_' . $bit_transaction_data->bit_trans_id;
        $member_id_and_price_paid = get_columns_data_by_paymentMethodId($bit_transaction_data->bit_trans_id, ['member_id', 'price_paid']);
        if (isset($member_id_and_price_paid->price_paid)) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.pre.bankhapoalim.co.il/payments/bit/v2/single-payments/" . $paymentInitiationId . "/capture",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n\t\"requestAmount\": " . (float)$member_id_and_price_paid->price_paid . ",\n\t\"currencyTypeCode\": 1 , \n\t\"externalSystemReference\": \"" . $bit_trans_id . "\",\n\t\"paymentInitiationId\": \"" . $paymentInitiationId . "\", \n\t\"sourceTransactionId\": " . $bit_transaction_data->bit_trans_id . "\n}\n",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $token,
                    "Content-Type: application/json;charset=UTF-8",
                    "Ocp-Apim-Subscription-Key: " . $subscription_key
                ),
            ));
            curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);


            $response = curl_exec($curl);
            $jason_response = json_decode($response);

            curl_close($curl);
            if (isset($jason_response->issuerAuthorizationNumber)) {
                $response = ['member_id' => $member_id_and_price_paid->member_id, 'trans_id' => $bit_transaction_data->bit_trans_id, 'issuerAuthorizationNumber' => $jason_response->issuerAuthorizationNumber];
            } else {
                $response = false;
            }
        }
    }
    return $response;

}

function get_pre_pay_bit_data_by_paymentInitiationId($paymentInitiationId)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_pre_payment_bit';
    $query = "SELECT * FROM {$table_name} where bit_paymentInitiationId = '" . $paymentInitiationId . "' AND status = 'pending'";

    $result = $wpdb->get_results($query);
    $result = is_array($result) && isset($result[0]) ? $result[0] : $result;

    return $result;

}

function get_columns_data_by_paymentMethodId($payment_method_id, $columns)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_members';
    $columns_to_return = implode(',', $columns);
    $query = "SELECT {$columns_to_return} FROM {$table_name} where payment_method_id = " . $payment_method_id;

    $result = $wpdb->get_results($query);
    $result = is_array($result) && isset($result[0]) ? $result[0] : $result;

    return $result;
}

function get_id_and_status_by_paymentInitiationId($paymentInitiationId)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_pre_payment_bit';
    $query = "SELECT bit_trans_id, status FROM {$table_name} where bit_paymentInitiationId = '" . $paymentInitiationId."'";

    $result = $wpdb->get_results($query);
    $result = is_array($result) && isset($result[0]) ? $result[0] : $result;

    return $result;
}

function update_pre_pay_bit_data_by_id_and_cloumns($id, $table_columns)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_pre_payment_bit';

    $update_query = "UPDATE {$table_name} SET ";

    foreach ($table_columns as $table_column => $column_value) {
        $update_query .= $table_column . "= " . "'" . $column_value . "'" . ',';
    }

    if (substr($update_query, -1) == ',') {
        $update_query = substr($update_query, 0, -1);
    }

    $update_query .= " WHERE bit_trans_id=" . $id;

    return $wpdb->query($update_query);
}

function update_course_member_by_id_and_cloumns($id, $table_columns)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_members';

    $update_query = "UPDATE {$table_name} SET ";

    foreach ($table_columns as $table_column => $column_value) {
        $update_query .= $table_column . "= " . "'" . $column_value . "'" . ',';
    }

    if (substr($update_query, -1) == ',') {
        $update_query = substr($update_query, 0, -1);
    }

    $update_query .= " WHERE member_id=" . $id;

    return $wpdb->query($update_query);
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

        $insert_query = "INSERT INTO {$table_name} ( email, first_name, last_name, bit_paymentInitiationId, bit_transactionSerialId, status, authorization_number)  
                            VALUES('$email','$first_name','$last_name', null ,null,'pending',null)";

        $wpdb->query($insert_query);
        $last_id = $wpdb->insert_id;
    }

    return $last_id;
}

function update_coupon_to_used($coupon_details)
{
    if (isset($coupon_details['id']) && isset($coupon_details['type']) && isset($coupon_details['coupon_code'])) {
        if ($coupon_details['type'] == 'unique') {
            $coupon_code_array = explode('_', $coupon_details['coupon_code']);
            update_unique_coupon_to_used($coupon_details['id'], $coupon_code_array[1]);
            update_unique_coupon_to_used_in_all_coupons_table($coupon_details['id']);
        } else {
            update_general_coupon_to_used($coupon_details['id']);
        }
    }
}

function update_unique_coupon_to_used($id, $coupon_code)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_unique_coupons_meta';
    $update_query = "UPDATE {$table_name} SET pending = 0, used = 1 WHERE coupon_id= {$id} AND coupon_code = '{$coupon_code}'";
    return $wpdb->query($update_query);
}

function update_unique_coupon_to_used_in_all_coupons_table($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $update_query = "UPDATE {$table_name} SET used_amount = used_amount + 1 WHERE coupon_id= {$id}";
    return $wpdb->query($update_query);
}


function update_general_coupon_to_used($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $update_query = "UPDATE {$table_name} SET gen_coupons_held = gen_coupons_held -1 , used_amount = used_amount + 1 WHERE coupon_id= {$id}";
    return $wpdb->query($update_query);
}


class Bit_Token_Manager
{
    private static $token;
    private static $token_expiration_time;

    public static function get_token()
    {
        if (isset(self::$token) && isset(self::$token_expiration_time)) {
            if (strtotime(date("m/d/Y h:i:s a", time())) < strtotime(self::$token_expiration_time)) {
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