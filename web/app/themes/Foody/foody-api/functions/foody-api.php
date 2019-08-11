<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/26/19
 * Time: 5:04 PM
 */


add_filter( 'rest_index', 'foody_filter_rest_index', 101 );

function foody_filter_rest_index( \WP_REST_Response $response ) {
	$data             = $response->get_data();
	$valid_namespaces = [
		'foody-api',
		'jwt-auth/v1'
	];

	$data['namespaces'] = array_filter( $data['namespaces'], function ( $namespace ) use ( $valid_namespaces ) {
		return in_array( $namespace, $valid_namespaces );
	} );

	$data['namespaces'] = array_values( $data['namespaces'] );
	$data['routes']     = array_filter( $data['routes'], function ( $route ) use ( $valid_namespaces ) {
		return in_array( $route['namespace'], $valid_namespaces );
	} );

	$data['pages'] = [];
	$response->set_data( $data );


	return $response;
}

add_action( 'wp_head', 'foody_add_restplain_style' );
function foody_add_restplain_style() {
	?>
    <style id="foody-rest">
        #restsplain {
            direction: ltr;
            text-align: left;
        }

        #restsplain * {
            direction: ltr;
            text-align: left;
        }
    </style>
	<?php
}

add_action( 'template_redirect', 'foody_api_docs_redirect' );
/**
 * Redirect unauthorized users from docs
 */
function foody_api_docs_redirect() {
	if ( preg_match( '/^\/api-docs\//', $_SERVER['REQUEST_URI'] ) ) {

		if ( ! is_user_logged_in() ) {
			if ( empty( $the_roles ) ) {
				wp_redirect( home_url( '' ) );
				exit;
			}
		}

		$user        = wp_get_current_user();
		$valid_roles = [ 'administrator', 'foody_api_user' ];

		$the_roles = array_intersect( $valid_roles, $user->roles );

		// The current user does not have any of the 'valid' roles.
		if ( empty( $the_roles ) ) {
			wp_redirect( home_url( '' ) );
			exit;
		}
	}
}