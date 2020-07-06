<?php
function foody_get_course_price()
{
    $course_id = $_POST['course_id'];
    return wp_send_json_success(['course_price' => (float)get_field('course_register_data_final_price', $course_id)]);
}

add_action('wp_ajax_nopriv_foody_get_course_price', 'foody_get_course_price');
add_action('wp_ajax_foody_get_course_price', 'foody_get_course_price');
function foody_start_bit_pay_process()
{
    // add new pending payment to db
    $pending_payment_id = insert_new_pending_payment();
    if ($pending_payment_id != false && isset($_POST['memberData']) && isset($_POST['isMobile']) && isset($_POST['thankYou'])) {
        try {
            $single_payment_ids = do_single_payment_bit($pending_payment_id, $_POST['memberData'], $_POST['isMobile'], $_POST['thankYou']);
            wp_send_json_success(['single_payment_ids' => $single_payment_ids]);
        } catch (Exception $e) {
            update_pre_pay_bit_data_by_id_and_cloumns($pending_payment_id, ['status' => 'canceled']);
            wp_send_json_error(['msg' => __('יש בעיה עם אמצעי התשלום, אנא נסה שנית מאוחר יותר או בחר אמצעי תשלום אחר')]);
        }
    }
}

add_action('wp_ajax_nopriv_foody_start_bit_pay_process', 'foody_start_bit_pay_process');
add_action('wp_ajax_foody_start_bit_pay_process', 'foody_start_bit_pay_process');


function foody_bitcom_transaction_complete()
{
    $payment_initiation_id = isset($_POST['paymentInitiationId']) ? $_POST['paymentInitiationId'] : false;
    $coupon = isset($_POST['coupon']) ? $_POST['coupon'] : false;

    if ($payment_initiation_id) {
        $coupon_details = get_coupon_data_by_name($coupon);

        $ids_and_authorization_number = do_bit_payment_capture($payment_initiation_id);
        if (is_array($ids_and_authorization_number) && isset($ids_and_authorization_number['trans_id']) && isset($ids_and_authorization_number['issuerAuthorizationNumber'])) {
            update_pre_pay_bit_data_by_id_and_cloumns($ids_and_authorization_number['trans_id'], ['status' => 'paid', 'authorization_number' => $ids_and_authorization_number['issuerAuthorizationNumber']]);
            if ($coupon_details != null) {
                update_coupon_to_used($coupon_details);
            }
            wp_send_json_success('captured');
        } else {
            wp_send_json_error(['msg' => __('התרחשה שגיאה ב- capture')]);
        }
    } else {
        wp_send_json_error(['msg' => __('payment id - התרחשה שגיאה ברכישה')]);
    }
}

add_action('wp_ajax_nopriv_foody_bitcom_transaction_complete', 'foody_bitcom_transaction_complete');
add_action('wp_ajax_foody_bitcom_transaction_complete', 'foody_bitcom_transaction_complete');

function foody_bit_refund_process()
{
    if (isset($_POST['paymentInitiation_id']) && $_POST['paymentInitiation_id']) {
        $bit_transaction_id_and_status = get_id_and_status_by_paymentInitiationId($_POST['paymentInitiation_id']);
        if (isset($bit_transaction_id_and_status->bit_trans_id)) {
            $member_data = get_columns_data_by_paymentMethodId($bit_transaction_id_and_status->bit_trans_id, ['*']);
            if (isset($member_data->price_paid) && isset($member_data->member_id)) {
                $prefix_for_trans = get_option('foody_identifier_trans_bit', false);
                $bit_trans_id = 'bit_trans_' . $prefix_for_trans . '_' . $bit_transaction_id_and_status->bit_trans_id;

                $request_url_path = '/refund';
                $request_body = "{\n    \"creditAmount\": " . $member_data->price_paid . ",\n    \"currencyTypeCode\": 1,\n    \"externalSystemReference\": \"" . $bit_trans_id . "\",\n    \"paymentInitiationId\": \"" . $_POST['paymentInitiation_id'] . "\",\n    \"refundExternalSystemReference\": \"" . $bit_trans_id . "_refund\"\n}";

                $response_json = bit_api_request("POST", $request_url_path, $request_body);

                if (isset($response_json->requestStatusCode) && isset($response_json->issuerAuthorizationNumber)) {
                    $is_refunded = bit_handle_status_code($response_json->requestStatusCode, ['member_id' => $member_data->member_id]);
                    if ($is_refunded) {
                        // update bit pre paid table
                        update_pre_pay_bit_data_by_id_and_cloumns($bit_transaction_id_and_status->bit_trans_id, ['status' => 'refunded', 'authorization_number' => $response_json->issuerAuthorizationNumber]);

                        // update course member table
                        update_course_member_by_id_and_cloumns($member_data->member_id, ['status' => 'refunded']);

                        // remove member user from course at Rav Messer
                        Rav_Messer_API_Handler::remove_member_from_rav_messer_list([
                            'member_email' => $member_data->member_email,
                            'course_name' => $member_data->course_name,
                            'name' => $member_data->first_name . ' ' . $member_data->last_name,
                            'phone' => $member_data->phone
                        ]);

                        // send refund invoice
                        foody_create_and_send_refund_invoice($member_data);
                        wp_send_json_success(['msg' => __('העסקה עם מזהה ' . $member_data->member_id . ' בוטלה')]);
                    } else {
                        if ($response_json->requestStatusCode == 4 || $response_json->requestStatusCode == 14) {
                            wp_send_json_error(array(
                                'error' => __('הזיכוי בהמתנה אנא נסו שוב בדקות הקרובות')
                            ));
                        }
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

add_action('wp_ajax_nopriv_foody_bit_refund_process', 'foody_bit_refund_process');
add_action('wp_ajax_foody_bit_refund_process', 'foody_bit_refund_process');

function get_member_data_for_finish_process($payment_initiation_id)
{
    $member_data = [];
    $member_results = get_columns_data_by_paymentMethodId($payment_initiation_id, ['*']);
    if (!empty($member_results)) {
        $member_data = [
            'email' => $member_results->email,
            'phone' => $member_results->phone,
            'first_name' => $member_results->first_name,
            'last_name ' => $member_results->last_name,
            'course_name' => $member_results->course_name,
            'price' => $member_results->price_paid,
            'enable_marketing' => $member_results->marketing_status == 1 ? 'true' : 'false',
            'coupon' => $member_results->coupon
        ];
    }
    return $member_data;
}

function get_payment_status($payment_initiation_id, $member_data = null)
{
    $request_url_path = '/single-payments/' . $payment_initiation_id;
    $response_json = bit_api_request("GET", $request_url_path);
    if (isset($response_json->requestStatusCode)) {
        return $response_json->requestStatusCode;
    } else {
        $error_handler = new Bit_API_Error_handler($response_json);
        $error_handler->throw_new_error();
    }
}

function bit_handle_status_code($code, $payment_initiation_id = null, $member_data = null, $coupon_details = null)
{
    global $wpdb;
    switch ($code) {
        case 11:
            // payment confirmed - final
            update_course_member_by_id_and_cloumns($member_data['member_id'], ['status' => 'paid']);

            send_new_course_member_data([
                'member_email' => $member_data['email'],
                'phone' => $member_data['phone'],
                'name' => $member_data['first_name'] . ' ' . $member_data['last_name'],
                'course_name' => $member_data['course_name'],
                'price' => $member_data['price'],
                'enable_marketing' => $member_data['enable_marketing'],
                'coupon' => $member_data['coupon']
            ], $member_data['course_id']);

            Rav_Messer_API_Handler::add_member_to_rav_messer(
                [
                    'member_email' => $member_data['email'],
                    'course_name' => $member_data['course_name'],
                    'name' => $member_data['first_name'] . ' ' . $member_data['last_name'],
                    'phone' => $member_data['phone']
                ],
                $member_data['course_name']);

            foody_create_and_send_purchase_invoice([
                'client_email' => $member_data['email'],
                'name' => $member_data['first_name'] . ' ' . $member_data['last_name'],
                'phone' => $member_data['phone']
            ], $member_data['course_name'], $member_data['price']);

            $result = 'transaction completed';
            break;
        case 10:
            // refund confirmed - final
            $result = 'refund completed';
            break;
        case 2: // canceled by business or failed - final
        case 15: // payment failed - final
            try {
                do_delete_bit_transaction($payment_initiation_id, $coupon_details);
                $result = 'canceled and deleted';
                break;
            } catch (Exception $e) {
                throw $e;
            }
        case 3: // canceled by client before money is held - final
        case 7: // time expired - final
            $result = 'canceled and deleted';
            $pre_pay_data = get_id_and_status_by_paymentInitiationId($payment_initiation_id);
            if (isset($pre_pay_data->bit_trans_id)) {
                $member_and_trans_data = get_columns_data_by_paymentMethodId($pre_pay_data->bit_trans_id, ['member_id']);
                if (isset($member_and_trans_data->member_id) && $coupon_details !== null) {
                    update_tables_after_cancellation($pre_pay_data->bit_trans_id, $member_and_trans_data->member_id, $coupon_details);
                }
            }
            return $result;
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
            // update status back to pending
            $table_name = $wpdb->prefix . 'foody_courses_members';
            $update_query = "UPDATE {$table_name} SET status='pending' where member_id ={$member_data['member_id']}  AND status = 'in_progress'";
            $wpdb->query($update_query);

            $result = false;
            break;
        case 9:
            // payment is being held => can preform capture
            $result = false;
            $ids_and_authorization_number = do_bit_payment_capture($payment_initiation_id);
            if (is_array($ids_and_authorization_number) && isset($ids_and_authorization_number['trans_id']) && isset($ids_and_authorization_number['issuerAuthorizationNumber'])) {
                update_pre_pay_bit_data_by_id_and_cloumns($ids_and_authorization_number['trans_id'], ['status' => 'paid', 'authorization_number' => $ids_and_authorization_number['issuerAuthorizationNumber']]);
                if ($coupon_details != null) {
                    update_coupon_to_used($coupon_details);
                }

                // try to check capture
                $status = get_payment_status($payment_initiation_id, $member_data);
                if ($status == 11) {
                    bit_handle_status_code($status, $payment_initiation_id, $member_data);
                } else {
                    $table_name = $wpdb->prefix . 'foody_courses_members';
                    $update_query = "UPDATE {$table_name} SET status='pending' where member_id ={$member_data['member_id']}  AND status = 'in_progress'";
                    $wpdb->query($update_query);
                }
            } else {
                $error_handler = new Bit_API_Error_handler($ids_and_authorization_number);
                $error_handler->throw_new_error();
            }
            break;
        default:
            $result = false;
            break;
    }
    return $result;
}

function do_delete_bit_transaction($paymentInitiationId, $coupon_details)
{
    $bit_transaction_id_and_status = get_id_and_status_by_paymentInitiationId($paymentInitiationId);
    if (isset($bit_transaction_id_and_status->bit_trans_id) && isset($bit_transaction_id_and_status->status)) {
        $member_id = get_columns_data_by_paymentMethodId($bit_transaction_id_and_status->bit_trans_id, ['member_id', 'price_paid']);
        if (($bit_transaction_id_and_status->status == 'pending' || $bit_transaction_id_and_status->status == 'in_progress') && isset($member_id->member_id)) {
//        if ($bit_transaction_id_and_status->status == 'pending' && isset($member_id->member_id)) {
            $request_url_path = '/single-payments/' . $paymentInitiationId;

            $response_json = bit_api_request("DELETE", $request_url_path);

            if (isset($response_json->requestStatusCode) && $response_json->requestStatusCode == 2) {
                update_tables_after_cancellation($bit_transaction_id_and_status->bit_trans_id, $member_id->member_id, $coupon_details);
            } else {
                $error_handler = new Bit_API_Error_handler($response_json);
                $error_handler->throw_new_error();
            }
        }
    }
}

function update_tables_after_cancellation($bit_trans_id, $member_id, $coupon_details)
{
    update_pre_pay_bit_data_by_id_and_cloumns($bit_trans_id, ['status' => 'canceled']);
    update_course_member_by_id_and_cloumns($member_id, ['status' => 'canceled']);

    if (isset($coupon_details['id']) && !is_array($coupon_details['id']) && isset($coupon_details['type']) && isset($coupon_details['coupon_code'])) {
        if ($coupon_details['type'] == 'unique') {
            $coupon_code_array = explode('_', $coupon_details['coupon_code']);
            update_unique_coupon_to_free($coupon_details['id'], $coupon_code_array[1]);
            //update_unique_coupon_to_free_in_all_coupons_table($coupon_details['id']);
        } else {
            update_general_coupon_to_free($coupon_details['id']);
        }
    }
}

function do_single_payment_bit($id, $member_data, $isMobile, $thank_you_page = null)
{
    $amount = isset($_POST['price']) ? doubleval($_POST['price']) : false;
    $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : false;
    $franchisingId = 32;
    $prefix_for_trans = get_option('foody_identifier_trans_bit', false);
    $bit_trans_id = 'bit_trans_' . $prefix_for_trans . '_' . $id;

    $request_url_path = '/single-payments';
    $request_body = "{\r\n  \"currencyTypeCode\": 1,\r\n  \"debitMethodCode\": 2,\r\n  \"externalSystemReference\": \"" . $bit_trans_id . "\",\r\n  \"franchisingId\": \"" . $franchisingId .
        "\",\r\n  \"requestAmount\":" . $amount . ",\r\n  \"requestSubjectDescription\": \"" . __($item_name) . "\"\r\n}\r\n";

    $response_json = bit_api_request("POST", $request_url_path, $request_body);
    if (isset($response_json->paymentInitiationId) && isset($response_json->transactionSerialId)) {
        $member_data['transaction_id'] = $response_json->paymentInitiationId;
        $member_data['payment_method_id'] = $id;

        update_pre_pay_bit_data_by_id_and_cloumns($id, ['bit_paymentInitiationId' => $response_json->paymentInitiationId, 'bit_transactionSerialId' => $response_json->transactionSerialId]);
        foody_add_course_member_to_table($member_data);
        if (isset($member_data['coupon']) && !empty($member_data['coupon'])) {
            update_relevant_coupon_to_pending($member_data['coupon']);
        }
        if ($isMobile != "false") {
            $phoneSchema = $isMobile == 'Android' ? $response_json->applicationSchemeAndroid : $response_json->applicationSchemeIos;
            $phoneSchema = add_merchantURL_to_mobile_schema($phoneSchema, $thank_you_page, $response_json->paymentInitiationId);
            return ['paymentInitiationId' => $response_json->paymentInitiationId, 'transactionSerialId' => $response_json->transactionSerialId, 'paymentMethodId' => $id, 'mobileSchema' => $phoneSchema];
        } else {
            return ['paymentInitiationId' => $response_json->paymentInitiationId, 'transactionSerialId' => $response_json->transactionSerialId, 'paymentMethodId' => $id];
        }
    } else {
        $error_handler = new Bit_API_Error_handler($response_json);
        $error_handler->throw_new_error();
    }
}

function do_bit_payment_capture($paymentInitiationId)
{
    $bit_transaction_data = get_pre_pay_bit_data_by_paymentInitiationId($paymentInitiationId);
    $response = false;
    if (isset($bit_transaction_data->bit_trans_id)) {
        $prefix_for_trans = get_option('foody_identifier_trans_bit', false);
        $bit_trans_id = 'bit_trans_' . $prefix_for_trans . '_' . $bit_transaction_data->bit_trans_id;
        $bit_sourceTransactionId = '000' . $bit_transaction_data->bit_trans_id;
        $member_id_and_price_paid = get_columns_data_by_paymentMethodId($bit_transaction_data->bit_trans_id, ['member_id', 'price_paid']);
        if (isset($member_id_and_price_paid->price_paid)) {

            $request_url_path = "/single-payments/" . $paymentInitiationId . "/capture";
            // "POST"
            $request_body = "{\n\t\"requestAmount\": " . (float)$member_id_and_price_paid->price_paid . ",\n\t\"currencyTypeCode\": 1 , \n\t\"externalSystemReference\": \"" . $bit_trans_id . "\",\n\t\"paymentInitiationId\": \"" . $paymentInitiationId . "\", \n\t\"sourceTransactionId\": \"" . $bit_sourceTransactionId . "\"\n}\n";
            $json_response = bit_api_request("POST", $request_url_path, $request_body);
            if (isset($json_response->issuerAuthorizationNumber)) {
                $response = ['member_id' => $member_id_and_price_paid->member_id, 'trans_id' => $bit_transaction_data->bit_trans_id, 'issuerAuthorizationNumber' => $json_response->issuerAuthorizationNumber];
            } else {
                $response = $json_response;
            }
        }
    }
    return $response;

}

function bit_api_request($request_type, $request_url_path, $request_body = null)
{
    try {
        $token = Bit_Token_Manager::get_token();
        $cert_path = get_certificate_data();
        $subscription_key = get_option('foody_subscription_key_for_bit', false);

        $curl_data_array = array(
            CURLOPT_URL => "https://api.bankhapoalim.co.il/payments/bit/v2" . $request_url_path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request_type,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Content-Type: application/json;charset=UTF-8",
                "Ocp-Apim-Subscription-Key: " . $subscription_key,
            ));

        if ($request_type == "POST" || $request_body != null) {
            $curl_data_array[CURLOPT_POSTFIELDS] = $request_body;
        }

        $curl = curl_init();


        curl_setopt_array($curl, $curl_data_array);

        curl_setopt($curl, CURLOPT_SSLCERT, $cert_path);

        $response = curl_exec($curl);
        $response_json = json_decode($response);

        curl_close($curl);

        return $response_json;
    } catch (Exception $e) {
        throw $e;
    }
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
    $query = "SELECT bit_trans_id, status FROM {$table_name} where bit_paymentInitiationId = '" . $paymentInitiationId . "'";

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
    $pfx_path = get_template_directory() . '/inc/certificates/partner12-poalim-api.pfx';
    $pfx_pass = 'Aa123456';
    $pfx_values = [];

    openssl_pkcs12_read(file_get_contents($pfx_path), $pfx_values, $pfx_pass);

    $cert_path = get_template_directory() . '/inc/certificates/me.pem';
    file_put_contents($cert_path, $pfx_values['cert'] . "\n" . $pfx_values['pkey']);

    return $cert_path;
}

function bit_escape_name($name)
{
    $escaped_name = $name;
    if (strpos($name, "'") == false) {
        $escaped_name = str_replace("'", "\'", $name);
    }

    return $escaped_name;
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

function update_unique_coupon_to_free($id, $coupon_code)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_unique_coupons_meta';
    $update_query = "UPDATE {$table_name} SET pending = 0 WHERE coupon_id= {$id} AND coupon_code = '{$coupon_code}'";
    return $wpdb->query($update_query);
}

function update_unique_coupon_to_used_in_all_coupons_table($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $update_query = "UPDATE {$table_name} SET used_amount = used_amount + 1 WHERE coupon_id= {$id}";
    return $wpdb->query($update_query);
}

function update_unique_coupon_to_free_in_all_coupons_table($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $update_query = "UPDATE {$table_name} SET gen_coupons_held = gen_coupons_held - 1 WHERE coupon_id= {$id} AND gen_coupons_held > 0";
    return $wpdb->query($update_query);
}

function update_general_coupon_to_used($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $update_query = "UPDATE {$table_name} SET gen_coupons_held = gen_coupons_held -1 , used_amount = used_amount + 1 WHERE coupon_id= {$id}";
    return $wpdb->query($update_query);
}

function update_general_coupon_to_free($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $update_query = "UPDATE {$table_name} SET gen_coupons_held = gen_coupons_held -1 WHERE coupon_id= {$id} AND gen_coupons_held > 0";
    return $wpdb->query($update_query);
}

function update_relevant_coupon_to_pending($coupon)
{
    $coupon_details = get_coupon_data_by_name($coupon);

    if (isset($coupon_details['id']) && !is_array($coupon_details['id']) && isset($coupon_details['type']) && isset($coupon_details['coupon_code'])) {
        switch ($coupon_details['type']) {
            case 'unique':
                $coupon_parts = explode('_', $coupon_details['coupon_code']);
                if (count($coupon_parts) == 2) {
                    update_unique_copupon_columns($coupon_details['id'], $coupon_parts, ['pending' => 1]);
                }
                break;
            case 'general':
                $coupon_gen_coupons_held = get_gen_coupons_held_by_coupon_id($coupon_details['id']);
                update_general_copupon_columns($coupon_details['id'], ['gen_coupons_held' => (int)$coupon_gen_coupons_held + 1]);
                break;
        }
    }
}

function get_gen_coupons_held_by_coupon_id($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $gen_coupons_held = '';

    $query = "SELECT gen_coupons_held FROM {$table_name} where coupon_id =" . $id;

    $results = $wpdb->get_results($query);
    $results = is_array($results) ? $results : [];

    foreach ($results as $result) {
        $gen_coupons_held = isset($result->gen_coupons_held) ? $result->gen_coupons_held : '';
    }

    return $gen_coupons_held;
}

function get_coupon_by_payment_initiation_id($payment_initiation_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_members';
    $coupon = '';

    $query = "SELECT coupon FROM {$table_name} where transaction_id ='" . $payment_initiation_id . "'";

    $results = $wpdb->get_results($query);
    $results = is_array($results) ? $results : [];

    foreach ($results as $result) {
        $coupon = isset($result->coupon) ? $result->coupon : '';
    }

    return $coupon;
}

function add_merchantURL_to_mobile_schema($mobile_schema, $thank_you_page, $paymentInitiationId)
{
    $add_to_schema = '';
    if ($thank_you_page != null && strpos($thank_you_page, '?') != false) {
        $thank_you_page_arr = explode('?', $thank_you_page);
        $thank_you_param = urlencode(urlencode($thank_you_page_arr[0]));
        $thank_you_page_params = explode('&', $thank_you_page_arr[1]);
        $course_params = explode('=', $thank_you_page_params[0]);
        $mobile_params = explode('=', $thank_you_page_params[1]);
        $thank_you_url_param_key = urlencode(urlencode($course_params[0]));
        $thank_you_url_param_value = urlencode(urlencode($course_params[1]));
        $mobile_params_param_key = urlencode(urlencode($mobile_params[0]));
        $mobile_params_param_value = urlencode(urlencode($mobile_params[1]));
        $payment_initiation_id_key = urlencode(urlencode('payment_initiation_id'));
        $payment_initiation_id_value = urlencode(urlencode($paymentInitiationId));
        $add_to_schema = '%26return_scheme%3D' . $thank_you_param . '%3F' . $thank_you_url_param_key . '%253D' . $thank_you_url_param_value . ',' . $payment_initiation_id_value;
    }
    return $mobile_schema . $add_to_schema;
}

function bit_fetch_status_process()
{
//    if (FOODY_BIT_FETCH_STATUS_PROCESS) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_members';
    $payment_method = __('ביט');
    $query = "SELECT * FROM {$table_name} where status = 'pending' AND payment_method = '{$payment_method}'";
    $update_query = "UPDATE {$table_name} SET status='in_progress' where member_id > 0 AND status = 'pending' AND payment_method = '{$payment_method}'";

    $pending_payments = $wpdb->get_results($query);
    $wpdb->query($update_query);
    $pending_payments = is_array($pending_payments) ? $pending_payments : [];

    foreach ($pending_payments as $pending_payment) {
        $data_of_member = [
            'member_id' => $pending_payment->member_id,
            'email' => $pending_payment->member_email,
            'first_name' => $pending_payment->first_name,
            'last_name' => $pending_payment->last_name,
            'phone' => $pending_payment->phone,
            'purchase_date' => $pending_payment->purchase_date,
            'enable_marketing' => $pending_payment->marketing_status == 1 ? 'true' : 'false',
            'course_name' => $pending_payment->course_name,
            'course_id' => $pending_payment->course_id,
            'price' => $pending_payment->price_paid,
            'payment_method' => $pending_payment->payment_method,
            'transaction_id' => $pending_payment->transaction_id,
            'coupon' => $pending_payment->coupon,
            'status' => $pending_payment->status,
            'payment_method_id' => $pending_payment->payment_method_id
        ];

        $coupon_details = get_coupon_data_by_name($pending_payment->coupon);

//            foody_query_process_for_bit_status($pending_payment->transaction_id, $data_of_member, $coupon_details);
        try {
            $status = get_payment_status($pending_payment->transaction_id, $data_of_member);
            if (!is_array($status)) {
                bit_handle_status_code($status, $pending_payment->transaction_id, $data_of_member, $coupon_details);
            }
        } catch (Exception $e) {
            // handle error
        }
//        }
    }
}

add_action('foody_bit_fetch_status_processes', 'bit_fetch_status_process');

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
        } else {
            $error_handler = new Bit_API_Error_handler($token_json);
            $error_handler->throw_new_error();
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
            CURLOPT_URL => "https://api.bankhapoalim.co.il/bank/auth/clients/token",
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

class Bit_API_Error_handler
{
    public $error_code;
    public $msg;
    public $incident_id;

    function __construct($json_response)
    {
        $this->error_code = isset($json_response->statusCode) ? $json_response->statusCode : null;
        $this->msg = isset($json_response->message) ? $json_response->message : null;
        $this->incident_id = isset($json_response->incidentId) ? $json_response->incidentId : null;
    }

    public function throw_new_error()
    {
        if ($this->error_code && $this->msg) {
            throw new Exception($this->msg, $this->error_code);
        } elseif (isset($this->incident_id)) {
            throw new Exception($this->incident_id);
        } else {
            throw new Exception('unknown');
        }
    }
}