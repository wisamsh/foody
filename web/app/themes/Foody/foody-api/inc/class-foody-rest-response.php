<?php


namespace FoodyAPI;


use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Foody_REST_Response extends WP_REST_Response {


	/**
	 * Foody_REST_Response constructor.
	 *
	 * @param null $data
	 * @param int $status
	 * @param array $headers
	 */
	public function __construct( $data = null, $status = 200, $headers = array() ) {
		parent::__construct( $data, $status, $headers );
		add_filter( 'rest_pre_serve_request', [ $this, 'pre_serve_request' ], 10, 4 );
	}

	public function jsonSerialize() {

		return json_encode( $this->data, JSON_UNESCAPED_UNICODE );
	}


	/**
	 * @param $result WP_REST_Response
	 * @param $request WP_REST_Request
	 * @param $server WP_REST_Server
	 *
	 * @return bool
	 */
	public function pre_serve_request( $served, $result, $request, $server ) {

		if ( ! $served ) {

			$route = $request->get_route();

			if ( strpos( $route, '/foody-api/' ) === 0 ) {

				$data = $result->get_data();

				$json = json_encode( $data, JSON_UNESCAPED_UNICODE );


				echo $json;
				$served = true;
			}
		}

		return $served;
	}

}