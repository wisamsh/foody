<?php
function foody_start_cardcom_pay_process()
{
    $new_member_data = $_POST['memberData'];
    $thank_you_page = $_POST['thankYou'];

    // add new user to table
    $added_id = foody_add_course_member_to_table($new_member_data, true);

    if ($added_id !== false) {
        try {
            // generate dynamic cardcom form
            $responseArray = generate_dynamic_cardcom_form($added_id, $new_member_data, $thank_you_page);

            // update member in table with transaction id
            update_course_member_by_id_and_cloumns($added_id, ['credit_low_profile_code' => $responseArray['LowProfileCode']]);

            // if coupon was used update to pending
            if (isset($new_member_data['coupon']) && !empty($new_member_data['coupon'])) {
                update_relevant_coupon_to_pending($new_member_data['coupon']);
            }
            return wp_send_json_success(['iframe_url' => $responseArray['url']]);
        } catch (Exception $exception) {
            return wp_send_json_error(['error' => $exception->getMessage()]);
        }
    } else {
        return wp_send_json_error(['error' => 'user not added to table']);
    }
}

add_action('wp_ajax_nopriv_foody_start_cardcom_pay_process', 'foody_start_cardcom_pay_process');
add_action('wp_ajax_foody_start_cardcom_pay_process', 'foody_start_cardcom_pay_process');


function update_course_member_by_credit_low_profile_code_and_cloumns($id, $table_columns)
{
    global $wpdb;

    $member_data = get_columns_data_by_paymentMethodId($id, ['member_id', 'status'], true);
    $member_id = isset($member_data->member_id) ? $member_data->member_id : $member_data;
    $trans_status = isset($member_data->status) ? $member_data->status : false;

    if ($member_id && $trans_status && $trans_status == 'pending') {
        $table_name = $wpdb->prefix . 'foody_courses_members';

        $update_query = "UPDATE {$table_name} SET ";

        foreach ($table_columns as $table_column => $column_value) {
            $update_query .= $table_column . "= " . "'" . $column_value . "'" . ',';
        }

        if (substr($update_query, -1) == ',') {
            $update_query = substr($update_query, 0, -1);
        }

        $update_query .= " WHERE member_id=" . $member_id;

        return $wpdb->query($update_query);
    }
    return false;
}

function get_cardcom_credentials()
{

    $credentials = false;
    $terminal_number = get_option('foody_terminal_number_for_cardcom_api');
    $terminal_number = !empty($terminal_number) ? $terminal_number : false; # Company terminal

    $user_name = get_option('foody_username_for_cardcom_api');
    $user_name = !empty($user_name) ? $user_name : false;   # API User

    $password = get_option('foody_password_for_cardcom_api');
    $password = !empty($password) ? $password : false;

    if ($terminal_number && $user_name && $password) {
        $credentials = ['terminal_number' => $terminal_number, 'user_name' => $user_name, 'password' => $password];
    }

    return $credentials;
}

function foody_cardcom_refund_process()
{
    $internal_deal_number = $_POST['internalDealNumber'];
    $member_id = $_POST['memberID'];
    $member_data = get_member_data_for_finish_process($_POST['internalDealNumber'], false, 'transaction_id');

    $cardcom_credentials = get_cardcom_credentials();

    if ($cardcom_credentials !== false) {
        $var = null;
        $request_url = 'https://secure.cardcom.solutions/Interface/CancelDeal.aspx?terminalnumber=' .
            $cardcom_credentials['terminal_number'] .
            '&name=' . $cardcom_credentials['user_name'] . '&pass=' .
            $cardcom_credentials['password'] .
            '&internalDealNumber=' . $internal_deal_number;

        try {
            $result = cardcom_do_curl($var, $request_url, 'GET');
            parse_str($result, $responseArray);

            if ($responseArray['ResponseCode'] == "0") {
                // update member in table with transaction id
                update_course_member_by_id_and_cloumns($member_id, ['status' => 'refunded', 'transaction_id' => $responseArray['InternalDealNumber']]);
                // remove member user from course at Rav Messer
                Rav_Messer_API_Handler::remove_member_from_rav_messer_list([
                    'member_email' => $member_data->member_email,
                    'course_name' => $member_data->course_name,
                    'name' => $member_data->first_name . ' ' . $member_data->last_name,
                    'phone' => $member_data->phone,
                ]);

                // send refund invoice
                foody_create_and_send_refund_invoice($member_data);
                return wp_send_json_success(['msg' => __('העסקה עם מזהה ' . $member_id . ' בוטלה')]);
            } else {
                wp_send_json_error(array(
                    'error' => __('הזיכוי נכשל')
                ));
            }
        } catch (Exception $exception) {
            return wp_send_json_error(['error' => $exception->getMessage()]);
        }
    } else {
        wp_send_json_error(['error' => __('חסרים פרטי הגישה לקארדקום')]);
    }
}

add_action('wp_ajax_nopriv_foody_cardcom_refund_process', 'foody_cardcom_refund_process');
add_action('wp_ajax_foody_cardcom_refund_process', 'foody_cardcom_refund_process');


function generate_dynamic_cardcom_form($added_id, $member_data, $thank_you_page)
{
    $cardcom_credentials = get_cardcom_credentials();
    $coupon_code = !empty($member_data['coupon']) ? $member_data['coupon'] : false;
    $cancellation_page = $coupon_code ? get_home_url() . '/' . _('ביטול-תהליך-רכישה') . '?course_id=' . $member_data['course_id'] . '&coupon=' . $coupon_code :
        get_home_url() . '/' . _('ביטול-תהליך-רכישה') . '?course_id=' . $member_data['course_id'];

    $min_num_of_payments = "1";
    $max_num_of_payments = "10";
    $min_price_for_payments = 199;

    if ($cardcom_credentials !== false) {
        $IsIframe = true;   # Iframe or Redirect
        $Operation = 1;  # = 1 - Bill Only , 2- Bill And Create Token , 3 - Token Only , 4 - Suspended Deal (Order).

        $vars = array();
        $vars['TerminalNumber'] = $cardcom_credentials['terminal_number'];
        $vars['UserName'] = $cardcom_credentials['user_name'];
        $vars["APILevel"] = "10"; // req
        $vars['codepage'] = '65001'; // unicode
        $vars["Operation"] = $Operation;

        $vars["Language"] = 'he';   // page languge he- hebrew , en - english , ru , ar
        $vars["CoinID"] = '1'; // billing coin , 1- NIS , 2- USD other , article :  http://kb.cardcom.co.il/article/AA-00247/0
        $vars["SumToBill"] = $member_data['price'];// Sum To Bill
        $vars['ProductName'] = $member_data['course_name']; // Product Name , will how if no invoice will be created.


        $vars['SuccessRedirectUrl'] = $thank_you_page; // Success Page
        $vars['ErrorRedirectUrl'] = $thank_you_page; // Error Page

        // Other Optional vars :

        $vars["CancelType"] = "2"; # show Cancel button on start ,
        $vars["CancelUrl"] = $cancellation_page;
        $vars['IndicatorUrl'] = get_home_url() . '/app/themes/Foody/functions/ajax/cardcom-response-listener.php'; // Indicator Url \ Notify URL . after use -  http://kb.cardcom.co.il/article/AA-00240/0

        $vars["ReturnValue"] = $added_id; // Optional , ,recommended , value that will be return and save in CardCom system
        $vars["MinNumOfPayments"] = $min_num_of_payments; // max num of payments to show  to the user
        if($member_data['price'] >= 199) {
            $vars["MaxNumOfPayments"] = $max_num_of_payments;
        } else {
            $vars["MaxNumOfPayments"] = $min_num_of_payments; // max num of payments to show  to the user
        }

        $vars["ShowInvoiceHead"] = "false"; //  if show & edit Invoice Details on the page.
        $vars["InvoiceHeadOperation"] = "0"; //  0 = no create & show Invoice.  1 =(default)create Invoice.  2 = show Details Invoice but not create Invoice !

        // Send Data To Bill Gold Server
        try {
            $result = cardcom_do_curl($vars, 'https://secure.cardcom.solutions/Interface/LowProfile.aspx');
            parse_str($result, $responseArray);

            # Is Deal OK
            if ($responseArray['ResponseCode'] == "0") {
                # Iframe or  Redicet User :
                if ($IsIframe && isset($responseArray['LowProfileCode']) && isset($responseArray['url'])) {
                    return ['url' => $responseArray['url'], 'LowProfileCode' => $responseArray['LowProfileCode']];
                } else  // redirect
                {
                    header("Location:" . $responseArray['url']);
                }

            } # Show Error to developer only
            else {
                if (isset($responseArray['Description'])) {
                    throw new Exception($responseArray['Description']);
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    } else {
        throw new Exception(__('חסרים פרטי הגישה לקארדקום'));
    }
}

function check_cardcom_purchase_from_notifier($operation_response_params)
{
    $low_profile_code = !empty($operation_response_params['lowprofilecode']) ? $operation_response_params['lowprofilecode'] : false;
    $operation_response = !empty($operation_response_params['OperationResponse']) || $operation_response_params['OperationResponse'] === "0" ? $operation_response_params['OperationResponse'] : false;
    $internalDealNumber = !empty($operation_response_params['InternalDealNumber']) ? $operation_response_params['InternalDealNumber'] : false;

    if ($low_profile_code !== false && ($operation_response !== false || $operation_response_params['OperationResponse'] === '0')) {
        $member_data = get_member_data_for_finish_process($low_profile_code, true);
        if (!empty($member_data)) {
            if (isset($member_data['status']) && $member_data['status'] == 'pending') {
                if (($operation_response == '0' || $operation_response === 0)) {

                    $coupon_details = get_coupon_data_by_name($member_data['coupon']);

                    if ($internalDealNumber) {
                        $paid = update_course_member_by_id_and_cloumns($member_data['id'], ['transaction_id' => $internalDealNumber, 'status' => 'paid']);
                    } else {
                        $paid = update_course_member_by_id_and_cloumns($member_data['id'], ['status' => 'paid']);
                    }

                    // update coupon to used
                    update_coupon_to_used($coupon_details);

                    // send mail to zappier
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
                        ]);

                    foody_create_and_send_purchase_invoice([
                        'client_email' => $member_data['email'],
                        'name' => $member_data['first_name'] . ' ' . $member_data['last_name'],
                        'phone' => $member_data['phone'],
                        'payment_method' => $member_data['payment_method']
                    ], $member_data['course_name'], $member_data['price']);
                    if ($paid) {
                        return true;
                    } else {
                        return false;
                    }
                } else { # some error
                    //rejected => cancel
                    $canceled = update_course_member_by_id_and_cloumns($member_data['id'], ['status' => 'canceled']);
                    if ($canceled) {
                        $coupon_details = get_coupon_data_by_name($member_data['coupon']);
                        if (isset($coupon_details['type'])) {
                            if ($coupon_details['type'] == 'unique') {
                                $coupon_code_array = explode('_', $coupon_details['coupon_code']);
                                update_unique_coupon_to_free($coupon_details['id'], $coupon_code_array[1]);
                            } else {
                                update_general_coupon_to_free($coupon_details['id']);
                            }
                        }
                        return true;
                    } else {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
    return false;
}

//Todo : what happens when user didnt finish purchase and didnt canceled
function check_cardcom_purchase($low_profile_code)
{
    $member_data = get_member_data_for_finish_process($low_profile_code, true);
    $cardcom_credentials = get_cardcom_credentials();
    if ($cardcom_credentials !== false) {

        #Get Deal information
        $vars = array(
            'TerminalNumber' => $cardcom_credentials['terminal_number'],
            'LowProfileCode' => $low_profile_code,
            'UserName' => $cardcom_credentials['user_name']
        );
        $coupon_details = get_coupon_data_by_name($member_data['coupon']);
        if (isset($member_data['status']) && $member_data['status'] == 'pending') {
            try {
                $result = cardcom_do_curl($vars, 'https://secure.cardcom.solutions/Interface/BillGoldGetLowProfileIndicator.aspx');
                $output = array();
                parse_str($result, $output);

                if ($output['ResponseCode'] == '0' && $output['OperationResponse'] == '0' && isset($output['InternalDealNumber'])
                    && isset($member_data['id']) && $output['DealResponse'] == 0) { #  Found the  LowProfile , validate is deal was OK!
                    #  if $output['DealResponse']  == 0
                    #  if $output['TokenResponse']  == 0
                    #  if $output['InvoiceResponseCode']  == 0
                    #  if $output['InvoiceResponseCode']  == 0
                    #  See : http://kb.cardcom.co.il/article/AA-00240/51/ for more information


                    // update table to paid and transaction_id
                    update_course_member_by_id_and_cloumns($member_data['id'], ['transaction_id' => $output['InternalDealNumber'], 'status' => 'paid']);

                    // update coupon to used
                    update_coupon_to_used($coupon_details);

                    // send mail to zappier
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
                        ]);

                    foody_create_and_send_purchase_invoice([
                        'client_email' => $member_data['email'],
                        'name' => $member_data['first_name'] . ' ' . $member_data['last_name'],
                        'phone' => $member_data['phone'],
                        'payment_method' => $member_data['payment_method']
                    ], $member_data['course_name'], $member_data['price']);

                    return 'transaction completed with Internal Deal Number: ' . $output['InternalDealNumber'];

                } else { # some error , send email to developer
                    //rejected => cancel
                    $canceled = update_course_member_by_id_and_cloumns($member_data['member_id'], ['status' => 'canceled']);
                    if ($canceled) {
                        $coupon_details = get_coupon_data_by_name($member_data['coupon']);
                        if (isset($coupon_details['type'])) {
                            if ($coupon_details['type'] == 'unique') {
                                $coupon_code_array = explode('_', $coupon_details['coupon_code']);
                                update_unique_coupon_to_free($coupon_details['id'], $coupon_code_array[1]);
                            } else {
                                update_general_coupon_to_free($coupon_details['id']);
                            }
                        }
                    }
                }
            } catch (Exception $exception) {
                // todo
            }
        }
    } else {
        // error credentials
        $admin_email = get_option('foody_email_for_courses_invoices');
        if (!empty($to)) {
            $subject = 'CardCom Error';
            $body = $mail_body = '<p> error: ' . __('חסרים פרטי הגישה לקארדקום') . '</p>';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail($to, $subject, $body, $headers);
            return wp_mail($admin_email, $subject, $body, $headers);
        } else {
            return false;
        }
    }
}

function cardcom_do_curl($vars, $PostVarsURL, $method = 'post')
{
    if ($method == 'post') {
        $urlencoded = http_build_query($vars);
    }

    #init curl connection
    if (function_exists("curl_init")) {
        $CR = curl_init();
        curl_setopt($CR, CURLOPT_URL, $PostVarsURL);
        if ($method == 'post') {
            curl_setopt($CR, CURLOPT_POST, 1);
            curl_setopt($CR, CURLOPT_POSTFIELDS, $urlencoded);
        } else {
            curl_setopt($CR, CURLOPT_HTTPGET, 1);
        }
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        #actual curl execution perfom
        $r = curl_exec($CR);
        $error = curl_error($CR);
        # some error , send email to developer
        if (!empty($error)) {
            $error_handler = new Bit_API_Error_handler($error);
            $error_handler->throw_new_error();
            die();
        }
        curl_close($CR);
        return $r;
    } else {
        echo "No curl_init";
        die();
    }
}

function get_member_data_for_finish_process($payment_initiation_id, $is_credit_card = false, $credit_search_by = 'credit_low_profile_code')
{
    $member_data = [];
    $member_results = get_columns_data_by_paymentMethodId($payment_initiation_id, ['*'], $is_credit_card, $credit_search_by);
    if (!empty($member_results)) {
        if ($is_credit_card) {
            $member_data = [
                'id' => $member_results->member_id,
                'email' => $member_results->member_email,
                'phone' => $member_results->phone,
                'first_name' => $member_results->first_name,
                'last_name' => $member_results->last_name,
                'course_name' => $member_results->course_name,
                'course_id' => $member_results->course_id,
                'price' => $member_results->price_paid,
                'payment_method' => $member_results->payment_method,
                'enable_marketing' => $member_results->marketing_status == 1 ? 'true' : 'false',
                'coupon' => $member_results->coupon,
                'status' => $member_results->status
            ];
        } else {
            $member_data = $member_results;
        }
    }
    return $member_data;
}