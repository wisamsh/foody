<?php
/**
 * Template Name: Bit Api Tester
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */



get_header();



function bit_tester(){

$client_id = get_option('foody_client_id_for_bit');
        $client_secret = get_option('foody_client_secret_for_bit');
        $cert_path = get_certificate_data();
$subscription_key =get_option('foody_subscription_key_for_bit');
        $curl = curl_init();

        curl_setopt_array($curl, array(
          //  CURLOPT_URL => "https://api.bankhapoalim.co.il/bank/auth/clients/token",
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


curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 1);
$response = curl_exec($curl);

$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

        $response_json = json_decode($response);

        curl_close($curl);
        return $response;




    }


?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area" style="margin-top:100px;">
            <?php $rr = bit_tester();
print_r($rr);
?>


        </div><!-- #primary -->
    </div><!-- #main-content -->


<?php

get_footer();