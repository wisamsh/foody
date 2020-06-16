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


function foody_cardcom_refund_process()
{
    $internal_deal_number = $_POST['internalDealNumber'];
    $member_id = $_POST['memberID'];
    $TerminalNumber = 1000; # Company terminal
    $UserName = 'barak9611';   # API User
    $password = 'c1234567!';

    $var = null;
    $request_url = 'https://secure.cardcom.solutions/Interface/LowProfile.aspx?terminalnumber=' . $TerminalNumber . '&name=' . $UserName . '&pass=' . $password . '&internalDealNumber=' . $internal_deal_number;

    try {
        $result = cardcom_do_curl($var, $request_url, 'GET');
        parse_str($result, $responseArray);

        // update member in table with transaction id
        update_course_member_by_id_and_cloumns($member_id, ['status' => 'refunded']);

        if($responseArray['ResponseCode'] == "0") {
            return wp_send_json_success(['msg' => __('העסקה עם מזהה ' . $member_id . ' בוטלה')]);
        }
        else{
            wp_send_json_error(array(
                'error' => __('הזיכוי נכשל')
            ));
        }
    } catch (Exception $exception) {
        return wp_send_json_error(['error' => $exception->getMessage()]);
    }
}

add_action('wp_ajax_nopriv_foody_cardcom_refund_process', 'foody_cardcom_refund_process');
add_action('wp_ajax_foody_cardcom_refund_process', 'foody_cardcom_refund_process');


function generate_dynamic_cardcom_form($added_id, $member_data, $thank_you_page)
{
    $TerminalNumber = 1000; # Company terminal
    $UserName = 'barak9611';   # API User
    $IsIframe = true;   # Iframe or Redirect
    $Operation = 1;  # = 1 - Bill Only , 2- Bill And Create Token , 3 - Token Only , 4 - Suspended Deal (Order).

    $vars = array();
    $vars['TerminalNumber'] = $TerminalNumber;
    $vars['UserName'] = $UserName;
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
    $vars["CancelUrl"] = $thank_you_page; //todo: think how to show in thank-you page that canceled
    $vars['IndicatorUrl'] = get_home_url() . '/app/themes/Foody/functions/ajax/cardcom-response-listener.php'; // Indicator Url \ Notify URL . after use -  http://kb.cardcom.co.il/article/AA-00240/0

    $vars["ReturnValue"] = $added_id; // Optional , ,recommended , value that will be return and save in CardCom system
    $vars["MaxNumOfPayments"] = "1"; // max num of payments to show  to the user

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
}

//Todo : what happens when user didnt finish purchase and didnt canceled
function check_cardcom_purchase($low_profile_code)
{
    $member_data = get_member_data_for_finish_process($low_profile_code, true);
    $TerminalNumber = 1000; # Company terminal
    $UserName = 'barak9611';   # API User

    #Get Deal information
    $vars = array(
        'TerminalNumber' => $TerminalNumber,
        'LowProfileCode' => $low_profile_code,
        'UserName' => $UserName
    );

    if (isset($member_data['status']) && $member_data['status'] == 'pending') {
        try {
            $result = cardcom_do_curl($vars, 'https://secure.cardcom.solutions/Interface/BillGoldGetLowProfileIndicator.aspx');
            $output = array();
            parse_str($result, $output);

            if ($output['ResponseCode'] == '0' && $output['OperationResponse'] == '0' && isset($output['InternalDealNumber'])
                && isset($member_data['id'])) { #  Found the  LowProfile , validate is deal was OK!
                #  if $output['DealResponse']  == 0
                #  if $output['TokenResponse']  == 0
                #  if $output['InvoiceResponseCode']  == 0
                #  if $output['InvoiceResponseCode']  == 0
                #  See : http://kb.cardcom.co.il/article/AA-00240/51/ for more information


                // update table to paid and transaction_id
                update_course_member_by_id_and_cloumns($member_data['id'], ['transaction_id' => $output['InternalDealNumber'], 'status' => 'paid']);

                // update table to paid and transaction_id
                $coupon_details = get_coupon_data_by_name($member_data['coupon']);
                update_coupon_to_used($coupon_details);
                return 'result: ' . $output['InternalDealNumber'];

            } else { # some error , send email to developer
                // todo
                echo 'Error : ' . $result;
            }
        } catch (Exception $exception) {
            // todo
        }
    } else {
        return 'result: member is no longer pending';
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