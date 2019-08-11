<?php

/**
 * Plugin updater manager class.
 *
 * Manages Plugin license key checks on Plugin options page and instantiates the EDD Plugin updater class.
 * Abstract class so can't be instantiated directly. Instead extend class via the main Plugin class.
 */
class EDD_Plugin_Updater_Manager {

	/* Declare class type properties. */
	protected $_plugin_args;
	protected $_plugin_updater_class; // handle to EDD updater object
	protected $_plugin_slug = null;

	/**
	 * Plugin updater manager class constructor.
	 */
	public function __construct( $args ) {

		$this->_plugin_args = $args;
		$this->_plugin_slug = $this->_plugin_args['plugin_name_slug'];

		add_action( 'admin_init', array( &$this, 'instantiate_updater_class' ), 0 );
		add_action( 'admin_init', array( &$this, 'register_plugin_option' ) );
		add_action( 'admin_init', array( $this, 'license_action' ) );
		add_action( 'update_option_' . $this->_plugin_slug . '_license_key', array(
			$this,
			'activate_license'
		), 10, 2 );
		add_action( 'admin_notices', array( $this, 'display_expired_admin_notice' ) );
		register_activation_hook( $this->_plugin_args['plugin_root'], array( $this, 'after_plugin_activated' ) );
	}

	// Show admin notice to renew license if expired.
	public function display_expired_admin_notice() {
		$admin_page = get_current_screen()->base;
		$status     = get_option( $this->_plugin_slug . '_license_key_status', '0' );
		$res        = strpos( $admin_page, $this->_plugin_args['menu_slug'] );

		//$response = $this->get_api_response( 'check_license' );
		//$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $res ) {
			// echo "status: " . $status . '<br>';
			// echo $admin_page . '<br>';
			// echo $this->_plugin_args['menu_slug'] . '<br>';
			// echo "res: " . $res . '<br>';

			if ( $status === 'expired' ) :
				?>
                <div style="display: grid;grid-template-columns:1fr 100px;align-items:center;justify-content:center;"
                     class="error settings-error notice">
                    <div style="margin-right: 10px;"><p><strong>License Key Expired</strong> - You currently don't have
                            access to plugin updates. To help keep your site secure, and be able to use new features, we
                            <b>highly recommend</b> keeping your plugin up to date.</p></div>
                    <div>
                        <a style="text-align:center;display:block;margin:0 auto !important;"
                           title="Open your wpgoplugins.com account to renew license (new window)"
                           class="renew button-secondary" href="<?php echo esc_url( $this->get_renewal_link() ); ?>"
                           target="_blank"><?php _e( 'Renew Now' ); ?></a>
                    </div>
                </div>
			<?php
			endif;
		}
	}

	/**
	 * Instantiate Plugin updater class.
	 */
	public function instantiate_updater_class() {

		// @todo remove this completely? we want notifications to show up even if now valid key is entered (but update fail as there isn't proper permission)
		/* If there is no valid license key status, don't allow updates. */
		//if ( get_option( $this->_plugin_slug . '_license_key_status', false) != 'valid' ) {
		//	return;
		//}

		/* Load EDD Plugin updater class. */
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			require_once( $this->_plugin_args['plugin_dir_path'] . 'classes/updater/EDD_SL_Plugin_Updater.php' );
		}

		$plugin_data = get_plugin_data( $this->_plugin_args['plugin_root'] );

		// setup the updater
		$this->_plugin_updater_class = new EDD_SL_Plugin_Updater( $this->_plugin_args['edd_store_url'], $this->_plugin_args['plugin_root'], array(
			'version'   => $plugin_data['Version'], // the current Plugin version we are running
			'license'   => $this->get_license_key(),
			'item_name' => $this->_plugin_args['edd_item_name'],
			'item_id'   => $this->_plugin_args['edd_item_id'],
			'author'    => "David Gwyer"
		) );
	}

	/**
	 * Retrieve license key.
	 */
	public function get_license_key() {
		// don't trim null byte characters. e.g. '0'
		return trim( get_option( $this->_plugin_slug . '_license_key' ), ' \t\n\r' );
	}

	/**
	 * Register Plugin options with Settings API.
	 */
	public function register_plugin_option() {

		// remember - the license key has it's own db entry.

		/* Register auto updates plugin options. */
		register_setting(
			$this->_plugin_args['options_group'],
			$this->_plugin_slug . '_license_key',
			array( $this, 'sanitize_license' )
		);

		/* Register auto updates plugin option fields. */
		add_settings_field(
			$this->_plugin_args['edd_auto_updates_option'],
			__( 'Plugin License Key' ),
			array( $this, 'render_license_key_field' ),
			$this->_plugin_args['menu_slug'],
			$this->_plugin_args['options_section']
		);
	}

	/**
	 * Add custom form field for license key.
	 */
	public function render_license_key_field() {

		$license_key = $this->get_license_key();
		$renew_link  = '<a title="Open your wpgoplugins.com account to renew license (new window)" class="renew button-secondary" href="' . esc_url( $this->get_renewal_link() ) . '" target="_blank">' . __( 'Renew' ) . '</a>';

		// Checks license status to display under license key - allows '0', '00' etc.
		if ( $license_key != '0' && ! $license_key ) {
			$message = sprintf(
				__( 'Don\'t have your license key to hand? Retrieve it now via your wpgoplugins.com %1$saccount page%2$s.' ),
				'<a href="' . esc_url( $this->_plugin_args['edd_store_url_account_page'] ) . '" target="_blank">',
				'</a>'
			);
		} else {
			//delete_transient( $this->_plugin_slug . '_license_message' ); // uncomment this to force license message to update on each page load
			if ( ! get_transient( $this->_plugin_slug . '_license_message', false ) ) {
				$this->check_license();
			}
			$message = get_transient( $this->_plugin_slug . '_license_message' );
		}
		$status = get_option( $this->_plugin_slug . '_license_key_status', '0' );
		//echo "status: [" . $status . "]"; // only uncomment for testing

		if ( $status == 'valid' ) {
			$key_col = 'green';
		} else {
			if ( $license_key != '0' && ! $license_key ) {
				$key_col = 'inherit';
			} else {
				$key_col = 'red';
			}
		}
		?>

        <style>
            .license-key {
                color: <?php echo $key_col; ?>;
                width: 18px;
                height: 18px;
                font-size: 18px;
                position: absolute;
                margin: 6px 0 0 5px;
                vertical-align: text-top;
            }

            .license-key-input {
                color: <?php echo $key_col; ?> !important;
                padding-left: 25px;
            }

            .renew {
                margin-right: 5px !important;
                text-decoration: none !important;
            }

            .refresh:hover span {
                color: #555;
            }

            .refresh {
                position: absolute;
                margin: 7px 0 0 -32px;
            }
        </style>
        <span class="left license-key dashicons dashicons-admin-network"></span><input
                class="license-key-input regular-text" id="<?php echo $this->_plugin_slug; ?>_license_key"
                name="<?php echo $this->_plugin_slug; ?>_license_key" type="text" placeholder="Enter license key..."
                value="<?php echo esc_attr( $license_key ); ?>"/>
		<?php
		if ( ! empty( $license_key ) ) :
			echo '<button style="color:#bbb;border:0;background:transparent;outline:none;" class="refresh" onclick="this.form.submit();" title="Refresh license key status" name="' . $this->_plugin_slug . '_license_refresh"><span style="width:14px;height:14px;font-size:14px;" class="dashicons dashicons-image-rotate"></span></button>';
			wp_nonce_field( $this->_plugin_slug . '_nonce', $this->_plugin_slug . '_nonce' );
			if ( 'valid' == $status ) {
				echo '<span><input type="submit" class="button-secondary" name="' . $this->_plugin_slug . '_license_deactivate" value="' . esc_attr__( 'Deactivate' ) . '"/></span>';
			} else {
				if ( 'expired' == $status ) {
					echo '<span>' . $renew_link . '</span>';
				}
				echo '<span><input type="submit" class="button-secondary" name="' . $this->_plugin_slug . '_license_activate" value="' . esc_attr__( 'Activate' ) . '"/></span>';
			}
		endif;
		?>
        <p class="description"><?php echo $message; ?></p>
		<?php
	}

	/**
	 * Checks if a license action was submitted.
	 */
	public function license_action() {

		if ( isset( $_POST[ $this->_plugin_slug . '_license_activate' ] ) ) {
			if ( check_admin_referer( $this->_plugin_slug . '_nonce', $this->_plugin_slug . '_nonce' ) ) {
				$response = $this->get_api_response( 'activate_license' );
				set_transient( $this->_plugin_slug . '_license_message', $this->get_license_message( $response, 'activate' ), DAY_IN_SECONDS );
			}
		}

		if ( isset( $_POST[ $this->_plugin_slug . '_license_deactivate' ] ) ) {
			if ( check_admin_referer( $this->_plugin_slug . '_nonce', $this->_plugin_slug . '_nonce' ) ) {
				$response = $this->get_api_response( 'deactivate_license' );
				set_transient( $this->_plugin_slug . '_license_message', $this->get_license_message( $response, 'deactivate' ), DAY_IN_SECONDS );
			}
		}

		if ( isset( $_POST[ $this->_plugin_slug . '_license_refresh' ] ) ) {
			if ( check_admin_referer( $this->_plugin_slug . '_nonce', $this->_plugin_slug . '_nonce' ) ) {
				$this->check_license();
			}
		}
	}

	public function check_license() {

		$response = $this->get_api_response( 'check_license' );
		set_transient( $this->_plugin_slug . '_license_message', $this->get_license_message( $response, 'check' ), DAY_IN_SECONDS );
	}

	/**
	 * Updates the license status message.
	 */
	public function get_license_message( $response, $action ) {

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
			update_option( $this->_plugin_slug . '_license_key_status', 'unknown_error' );

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// echo "<pre>";
			// print_r($license_data);
			// echo "</pre>";

			$expires       = date_i18n( 'F jS, Y', strtotime( $license_data->expires, current_time( 'timestamp' ) ) );
			$site_count    = $license_data->site_count;
			$license_limit = $license_data->license_limit;

			// If unlimited
			if ( 0 == $license_limit ) {
				$license_limit = __( 'unlimited' );
			}

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {
					case 'expired' :
						$message = sprintf( __( 'Your license key expired on %s.' ), '<b>' . $expires . '</b>' );
						update_option( $this->_plugin_slug . '_license_key_status', 'expired' );
						break;
					case 'disabled' :
					case 'revoked' :
						$message = __( 'Your license key has been disabled.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'disabled' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'missing' );
						break;
					case 'inactive' :
						// this might not be needed as testing shows when $license_data->license === 'inactive' $license_data->success === true
						$message = __( 'Inactive license. No sites have been activated for this license key.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'inactive' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'invalid' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $this->_plugin_args['edd_item_name'] );
						update_option( $this->_plugin_slug . '_license_key_status', 'item_name_mismatch' );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'no_activations_left' );
						break;
					default :
						$message = __( 'An error occurred, please try again.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'generic_error' );
						break;
				}
			} else {

				switch ( $license_data->license ) {
					case 'expired' :
						$message = sprintf( __( 'Your license key expired on %s.' ), '<b>' . $expires . '</b>' );
						update_option( $this->_plugin_slug . '_license_key_status', 'expired' );
						break;
					case 'valid' :
						$status = 'valid';
						if ( $action == 'check' ) {
							if ( false === $license_data->client_site_active ) {
								$status  = 'deactivated';
								$message = sprintf( __( 'License key not active for %1$s - plugin updates currently unavailable.' ), '<b>' . $license_data->client_url . '</b>' );
							} else {
								$message = sprintf( __( 'License key active. Expires %1$s, %2$s/%3$s sites activated.' ), '<b>' . $expires . '</b>', $site_count, $license_limit );
							}
						}
						if ( $action == 'activate' ) {
							$message = __( 'License key activated. Thanks for your continued support - you\'re amazing!' );
						}
						update_option( $this->_plugin_slug . '_license_key_status', $status );
						break;
					case 'deactivated' :
						$message = __( 'License key deactivated for this site. Plugin updates currently unavailable.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'deactivated' );
						break;
					case 'disabled' :
					case 'revoked' :
						$message = __( 'Your license key has been disabled.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'disabled' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'missing' );
						break;
					case 'inactive' :
						if ( $action == 'check' ) {
							$message = sprintf( __( 'Inactive license. This site (%1$s) hasn\'t been activated for the license key above.' ), '<b>' . $license_data->client_url . '</b>' );
						} else {
							$message = __( 'Inactive license. This site hasn\'t been activated for the license key above.' );
						}
						update_option( $this->_plugin_slug . '_license_key_status', 'inactive' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'invalid' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $this->_plugin_args['edd_item_name'] );
						update_option( $this->_plugin_slug . '_license_key_status', 'item_name_mismatch' );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.' );
						update_option( $this->_plugin_slug . '_license_key_status', 'no_activations_left' );
						break;
					default :
						$message = __( 'License key status: ' . $license_data->license );
						update_option( $this->_plugin_slug . '_license_key_status', 'generic_message [' . $license_data->license . ']' );
						break;
				}
			}

			return $message;
		}
	}

	/**
	 * Makes a call to the API.
	 */
	public function get_api_response( $edd_action ) {

		// Retrieve license key from the database.
		$license = $this->get_license_key();

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => $edd_action,
			'license'    => $license,
			'item_name'  => urlencode( $this->_plugin_args['edd_item_name'] ),
			'item_id'    => $this->_plugin_args['edd_item_id']
		);

		// Call the custom API.
		$response = wp_remote_post(
			$this->_plugin_args['edd_store_url'],
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		return $response;
	}

	/**
	 * Constructs a renewal link
	 */
	public function get_renewal_link() {

		// If download_id was passed in the config, a renewal link can be constructed
		$license_key = $this->get_license_key();
		if ( '' != $this->_plugin_args['edd_item_id'] && $license_key ) {
			$url = esc_url( $this->_plugin_args['edd_store_url'] );
			$url .= '/checkout/?edd_license_key=' . $license_key . '&download_id=' . $this->_plugin_args['edd_item_id'];

			return $url;
		}

		// Otherwise return the remote api url
		return $this->_plugin_args['edd_store_url_account_page'];
	}

	/**
	 * Sanitizes the license key.
	 *
	 * @param string $new License key that was submitted.
	 *
	 * @return string $new Sanitized license key.
	 */
	public function sanitize_license( $new ) {

		$old = $this->get_license_key();

		if ( $old && $old != $new ) {
			// New license has been entered, so must reactivate
			delete_option( $this->_plugin_slug . '_license_key_status' );
			delete_transient( $this->_plugin_slug . '_license_message' );
		}

		// sanitize license key
		return wp_filter_nohtml_kses( $new );
	}

	/**
	 * Delete plugin license key status and message options directly after plugin activation to force fresh license key check.
	 */
	public function after_plugin_activated() {

		$this->check_license();
		//delete_option( $this->_plugin_slug . '_license_key_status' );
		//delete_transient( $this->_plugin_slug . '_license_message' );
	}
}