<?php
namespace Pushengage\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NonceChecker {
	/**
	 * Check nonce validity
	 *
	 * @return void
	 */
	public static function check() {
		if ( ! check_ajax_referer( 'pushengage-nonce', '_wpnonce', false ) ) {
			$error['message'] = __( 'Invalid security token sent.', 'pushengage' );
			$error['code'] = 'invalid_security_token';
			wp_send_json_error( $error, 401 );
		}
	}

	/**
	 * Create nonce
	 *
	 * @since 4.0.5
	 *
	 * @return void
	 */
	public static function create_nonce() {
		return wp_create_nonce( 'pushengage-nonce' );
	}
}
