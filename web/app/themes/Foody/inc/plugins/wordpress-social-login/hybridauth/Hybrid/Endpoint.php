<?php
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2014, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */

/**
 * Hybrid_Endpoint class
 *
 * Hybrid_Endpoint class provides a simple way to handle the OpenID and OAuth endpoint.
 */
class Hybrid_Endpoint {
	public static $request = null;
	public static $initDone = false;

	/**
	 * Process the current request
	 *
	 * $request - The current request parameters. Leave as NULL to default to use $_REQUEST.
	 */
	public static function process( $request = null ) {
		// Setup request variable
		Hybrid_Endpoint::$request = $request;

		if ( is_null( Hybrid_Endpoint::$request ) ) {
			// Fix a strange behavior when some provider call back ha endpoint
			// with /index.php?hauth.done={provider}?{args}... 
			// >here we need to recreate the $_REQUEST
			if ( strrpos( $_SERVER["QUERY_STRING"], '?' ) ) {
				$_SERVER["QUERY_STRING"] = str_replace( "?", "&", $_SERVER["QUERY_STRING"] );

				parse_str( $_SERVER["QUERY_STRING"], $_REQUEST );
			}

			Hybrid_Endpoint::$request = $_REQUEST;
		}

		// If openid_policy requested, we return our policy document
		if ( isset( Hybrid_Endpoint::$request["get"] ) && Hybrid_Endpoint::$request["get"] == "openid_policy" ) {
			Hybrid_Endpoint::processOpenidPolicy();
		}

		// If openid_xrds requested, we return our XRDS document
		if ( isset( Hybrid_Endpoint::$request["get"] ) && Hybrid_Endpoint::$request["get"] == "openid_xrds" ) {
			Hybrid_Endpoint::processOpenidXRDS();
		}

		// If we get a hauth.start
		if ( isset( Hybrid_Endpoint::$request["hauth_start"] ) && Hybrid_Endpoint::$request["hauth_start"] ) {
			Hybrid_Endpoint::processAuthStart();
		} // Else if hauth.done
		elseif ( isset( Hybrid_Endpoint::$request["hauth_done"] ) && Hybrid_Endpoint::$request["hauth_done"] ) {
			Hybrid_Endpoint::processAuthDone();
		} // Else we advertise our XRDS document, something supposed to be done from the Realm URL page
		else {
			Hybrid_Endpoint::processOpenidRealm();
		}
	}

	/**
	 * Process OpenID policy request
	 */
	public static function processOpenidPolicy() {
		$output = file_get_contents( dirname( __FILE__ ) . "/resources/openid_policy.html" );
		print $output;
		die();
	}

	/**
	 * Process OpenID XRDS request
	 */
	public static function processOpenidXRDS() {
		header( "Content-Type: application/xrds+xml" );

		$output = str_replace
		(
			"{RETURN_TO_URL}",
			str_replace(
				array( "<", ">", "\"", "'", "&" ), array( "&lt;", "&gt;", "&quot;", "&apos;", "&amp;" ),
				Hybrid_Auth::getCurrentUrl( false )
			),
			file_get_contents( dirname( __FILE__ ) . "/resources/openid_xrds.xml" )
		);
		print $output;
		die();
	}

	/**
	 * Process OpenID realm request
	 */
	public static function processOpenidRealm() {
		$output = str_replace
		(
			"{X_XRDS_LOCATION}",
			htmlentities( Hybrid_Auth::getCurrentUrl( false ), ENT_QUOTES, 'UTF-8' ) . "?get=openid_xrds&v=" . Hybrid_Auth::$version,
			file_get_contents( dirname( __FILE__ ) . "/resources/openid_realm.html" )
		);
		print $output;
		die();
	}

	/**
	 * define:endpoint step 3.
	 */
	public static function processAuthStart() {
		Hybrid_Endpoint::authInit();

		$provider_id = trim( strip_tags( Hybrid_Endpoint::$request["hauth_start"] ) );

		# check if page accessed directly
		if ( ! Hybrid_Auth::storage()->get( "hauth_session.$provider_id.hauth_endpoint" ) ) {
			throw new Hybrid_Exception( "You cannot access this page directly." );
		}

		# define:hybrid.endpoint.php step 2.
		$hauth = Hybrid_Auth::setup( $provider_id );

		# if REQUESTed hauth_idprovider is wrong, session not created, etc. 
		if ( ! $hauth ) {
			throw new Hybrid_Exception( "Invalid parameter! Please return to the login page and try again." );
		}

		try {
			$hauth->adapter->loginBegin();
		} catch ( Exception $e ) {
			Hybrid_Error::setError( $e->getMessage(), $e->getCode() );

			$hauth->returnToCallbackUrl();
		}

		die();
	}

	/**
	 * define:endpoint step 3.1 and 3.2
	 */
	public static function processAuthDone() {
		Hybrid_Endpoint::authInit();

		$provider_id = trim( strip_tags( Hybrid_Endpoint::$request["hauth_done"] ) );

		# check if page accessed directly
		if ( ! Hybrid_Auth::storage()->get( "hauth_session.$provider_id.hauth_endpoint" ) ) {
			throw new Hybrid_Exception( "You cannot access this page directly." );
		}

		$hauth = Hybrid_Auth::setup( $provider_id );

		if ( ! $hauth ) {
			$hauth->adapter->setUserUnconnected();

			throw new Hybrid_Exception( "Invalid parameter! Please return to the login page and try again." );
		}

		try {
			$hauth->adapter->loginFinish();
		} catch ( Exception $e ) {
			Hybrid_Error::setError( $e->getMessage(), $e->getCode() );

			$hauth->adapter->setUserUnconnected();
		}

		$hauth->returnToCallbackUrl();
		die();
	}

	public static function authInit() {
		if ( ! Hybrid_Endpoint::$initDone ) {
			Hybrid_Endpoint::$initDone = true;

			# Init Hybrid_Auth
			try {
				$storage = new Hybrid_Storage();

				// Check if Hybrid_Auth session already exist
				if ( ! $storage->config( "CONFIG" ) ) {
					throw new Hybrid_Exception( "You cannot access this page directly." );
				}

				Hybrid_Auth::initialize( $storage->config( "CONFIG" ) );
			} catch ( Exception $e ) {
				throw new Hybrid_Exception( "Oophs. Error!" );
			}
		}
	}
}
