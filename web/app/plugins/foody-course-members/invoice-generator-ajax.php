<?php

function foody_create_and_send_invoice(){
    $token = get_api_token();
    if($token != false){
        // generate invoice
        $v = 'nice';
    }
}
add_action('wp_ajax_nopriv_foody_create_and_send_invoice', 'foody_create_and_send_invoice');
add_action('wp_ajax_foody_create_and_send_invoice', 'foody_create_and_send_invoice');

function get_api_token(){
    $client_id = get_option( 'foody_client_id_for_invoice', false );
    $client_secret = get_option( 'foody_client_secret_for_invoice', false );

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
        CURLOPT_POSTFIELDS =>"{\n  \"id\": \"". $client_id ."\",\n  \"secret\": \"". $client_secret ."\"\n}\n",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "X-Authorization-Bearer: JWT",
            "Content-Type: text/plain"
        ),
    ));

    $response = curl_exec($curl);
    $response_json = json_decode ($response);
    curl_close($curl);

    return isset($response_json->token) ? $response_json->token : false;
}