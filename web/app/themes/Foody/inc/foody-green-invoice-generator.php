<?php

function foody_create_and_send_purchase_invoice($client_obj, $course_name, $price)
{
    $token = Green_Invoice_Token_Manager::get_token();
    if ($token != false) {
        // generate invoice
        $response = generate_new_invoice($token, $client_obj, $course_name, $price);
    }
}

function foody_create_and_send_refund_invoice($client_obj)
{
    $client_email = isset($client_obj->member_email) && !empty($client_obj->member_email) ? $client_obj->member_email : false;
    $client_name = isset($client_obj->first_name) && isset($client_obj->last_name) && !empty($client_obj->first_name) && !empty($client_obj->last_name) ? $client_obj->first_name . ' ' . $client_obj->last_name : false;
    $client_phone = isset($client_obj->phone) && !empty($client_obj->phone) ? $client_obj->phone : false;
    $price = isset($client_obj->price_paid) && !empty($client_obj->price_paid) ? $client_obj->price_paid : false;
    $course_name = isset($client_obj->course_name) && !empty($client_obj->course_name) ? $client_obj->course_name : false;

    if($client_email && $client_name && $client_phone && $price && $course_name) {
        $token = Green_Invoice_Token_Manager::get_token();
        if ($token != false) {
            // generate invoice
            $response = generate_new_invoice($token, [
                'client_email' => $client_email,
                'name' => $client_name,
                'phone' => $client_phone
            ], $course_name, $price, true);
        }
    }
}

function generate_new_invoice($token, $client_obj, $course_name, $price, $is_refund = false)
{
    $request_body = get_invoice_request_body($client_obj, $course_name, $price, $is_refund);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://sandbox.d.greeninvoice.co.il/api/v1/documents",
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
            "Content-Type: application/json",
            "Content-Type: text/plain"
        ),
    ));

    $response = curl_exec($curl);
    $response_json = json_decode($response);


    curl_close($curl);
    return $response_json;
}


function get_invoice_request_body($client_obj, $course_name, $price, $is_refund = false)
{
    $admin_email = get_option('admin_email');
    $invoice_type = $is_refund ? 330 : 320;
    $current_date = date("Y-m-d", time());

    $requset_body = [
        "description" => 'InvoiceReceipt_No_',
        "remarks" => "",
        "footer" => "",
        "emailContent" => $admin_email,
        "type" => $invoice_type,
        "date" => $current_date,
        "dueDate" => "",
        "lang" => "he",
        "currency" => "ILS",
        "vatType" => 0,
        "discount" => "",
        "rounding" => false,
        "signed" => true,
        "attachment" => true,
        "maxPayments" => 1,
        "paymentRequestData" => "",
        "client" => [
            "name" => $client_obj['name'],
            "emails" => [
                $client_obj['client_email']
            ],
            "address" => "",
            "city" => "",
            "zip" => "",
            "country" => "IL",
            "phone" => $client_obj['phone'],
            "fax" => "",
            "mobile" => "",
            "add" => false,
            "self" => false
        ],
        "income" => [
            [
                "catalogNum" => "",
                "description" => $course_name,
                "quantity" => 1,
                "price" => $price,
                "currency" => "ILS",
                "currencyRate" => 1,
                "itemId" => "",
                "vatType" => 1
            ]
        ],
        "payment" => [
            [
                "date" => $current_date,
                "type" => 10,
                "price" => $price,
                "currency" => "ILS",
                "currencyRate" => 1,
                "bankName" => "",
                "bankBranch" => "",
                "bankAccount" => "",
                "chequeNum" => "",
                "accountId" => "",
                "transactionId" => "",
                "appType" => "1",
                "subType" => "",
                "cardType" => "",
                "cardNum" => "",
                "dealType" => 1,
                "numPayments" => 1,
                "firstPayment" => $price
            ]
        ]
    ];

    return json_encode($requset_body);

}

class Green_Invoice_Token_Manager
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
        $token_json = self::get_token_call();

        if ($token_json && isset($token_json->expires) && isset($token_json->token)) {
            self::$token = $token_json->token;
            self::$token_expiration_time = date("m/d/Y h:i:s a", time() + $token_json->expires);
        } else {
            // handle error
        }
        return self::$token;
    }

    private static function get_token_call()
    {
        $client_id = get_option('foody_client_id_for_invoice', false);
        $client_secret = get_option('foody_client_secret_for_invoice', false);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sandbox.d.greeninvoice.co.il/api/v1/account/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"id\": \"" . $client_id . "\",\n  \"secret\": \"" . $client_secret . "\"\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "X-Authorization-Bearer: JWT",
                "Content-Type: text/plain"
            ),
        ));

        $response = curl_exec($curl);
        $response_json = json_decode($response);
        curl_close($curl);

        return $response_json;
    }
}