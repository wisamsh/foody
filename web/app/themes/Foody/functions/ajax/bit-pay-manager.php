<?php
function foody_start_bit_pay_process(){

    $token = get_token();

}
add_action('wp_ajax_foody_nopriv_start_bit_pay_process', 'foody_start_bit_pay_process');
add_action('wp_ajax_foody_start_bit_pay_process', 'foody_start_bit_pay_process');

function get_token(){
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
        CURLOPT_POSTFIELDS => "client_id=80720a9e-169a-44ad-8b20-de18ab7045a9&client_secret=RgHyzCT5tMR_Y*9%7BW28l8Q%294&response_type=token&scope=bit_payment",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: x-www-form-urlencoded",
            "Ocp-Apim-Subscription-Key: 0177d856c5384317ad23222e169a90a6",
            "Content-Type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}