<?php
/**
 * Note: This file is intended to be publicly accessible.
 * Reference: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API/Using_Service_Workers
 */

header( 'Service-Worker-Allowed: /' );
header( 'Content-Type: application/javascript' );
header( 'X-Robots-Tag: none' );

$subdomain = 'demo';
if ( array_key_exists( 'domain', $_GET ) ) {
	$subdomain = urlencode( $_GET['domain'] );
}

echo "importScripts('https://" . $subdomain . ".pushengage.com/service-worker.js?ver=2.3.0');";
