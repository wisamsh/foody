<?php
namespace Pushengage;

use Pushengage\HttpClient;
use Pushengage\EnqueueAssets;
use Pushengage\Utils\Options;
use Pushengage\Utils\ArrayHelper;
use Pushengage\Utils\Constants;
use Pushengage\Utils\Helpers;
use Pushengage\Utils\PostMetaFormatter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Core {

	/**
	 * Custom size for getting post image (get_post_image)
	 *
	 * @var array
	 */
	private $image_size = array( 364, 180 );

	/**
	 * Class constructor
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register & initialize core class
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_hooks() {

		$pushengage_settings = Options::get_site_settings();

		if ( ! empty( $pushengage_settings ) ) {
			add_action( 'init', array( $this, 'maybe_output_sw_file' ) );

			// Enqueue pushengage sdk init script only on frontend.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_pushengage_sdk_init_script' ) );

			/**
			 * Injects the segment addition scripts in the frontend for the category based segmentation.
			 *
			 * @since 4.0.0
			 *
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_category_segmentation_script' ) );

			// register action hook to send notification on post get published.
			add_action( 'transition_post_status', array( $this, 'send_pe_push_notifications' ), 10, 3 );
		}

		// admin level actions.
		if ( is_admin() ) {
			add_action( 'init', array( $this, 'init_admin_options' ) );
		}
	}

	/**
	 * Enqueue pushengage web sdk init script
	 *
	 * @since 4.0.7
	 *
	 * @return void
	 */
	public function enqueue_pushengage_sdk_init_script() {
		$pushengage_settings = Options::get_site_settings();

		if ( empty( $pushengage_settings ) ) {
			return;
		}

		$api_key  = ! empty( $pushengage_settings['api_key'] ) ? $pushengage_settings['api_key'] : null;
		$site_key = ! empty( $pushengage_settings['site_key'] ) ? $pushengage_settings['site_key'] : null;

		if ( ! empty( $api_key ) && ! empty( $site_key ) ) {
			$web_sdk_url = PUSHENGAGE_CLIENT_JS_URL . 'sdks/pushengage-web-sdk.js';
			$script = "(function(w, d) {
				w.PushEngage = w.PushEngage || [];
				w._peq = w._peq || [];
				PushEngage.push(['init', {
					appId: '" . $site_key . "'
				}]);
				var e = d.createElement('script');
				e.src = '" . $web_sdk_url . "';
				e.async = true;
				e.type = 'text/javascript';
				d.head.appendChild(e);
			  })(window, document);";
		} elseif ( function_exists( 'wp_add_inline_script' ) ) {
			$script = 'console.error("You haven’t finished setting up your site with PushEngage. Please connect your account!!");';
		}

		wp_register_script( 'pushengage-sdk-init', '' );
		wp_enqueue_script( 'pushengage-sdk-init' );
		wp_add_inline_script( 'pushengage-sdk-init', $script );

		// wp-rocket filter hook to exclude inline PushEngage sdk init script code.
		add_filter(
			'rocket_excluded_inline_js_content',
			function ( $excluded_inline_js ) {
				if ( gettype( $excluded_inline_js ) === 'array' ) {
					$excluded_inline_js[] = '_peq';
					$excluded_inline_js[] = 'PushEngage';
				}

				return $excluded_inline_js;
			}
		);
	}

	/**
	 * Dynamically generate service worker file.
	 *
	 * @since 4.0.6
	 *
	 * @return void
	 */
	public function maybe_output_sw_file() {
		$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;

		if ( empty( $request ) ) {
			return;
		}

		$request_path = explode( '?', $request )[0];
		$sw_rel_link = wp_make_link_relative( PUSHENGAGE_PLUGIN_URL . 'packages/service-worker.js' );

		if ( $request_path !== $sw_rel_link ) {
			return;
		}

		$pushengage_settings = Options::get_site_settings();
		$subdomain = $pushengage_settings['site_subdomain'];
		// app id is same as site key
		$app_id = $pushengage_settings['site_key'];

		if ( array_key_exists( 'domain', $_GET ) && ! empty( $_GET['domain'] ) ) {
			$subdomain = urlencode( $_GET['domain'] );
		}

		if ( array_key_exists( 'appId', $_GET ) && ! empty( $_GET['appId'] ) ) {
			$app_id = sanitize_key( $_GET['appId'] );
		}

		header_remove( 'x-powered-by' );
		header( 'Content-Type: application/javascript' );
		header( 'X-Robots-Tag: none' );

		// If app id is present then use the app id to generate the service
		// worker file else use the subdomain
		if ( ! empty( $app_id ) ) {
			$pushengage_sw_url = PUSHENGAGE_CLIENT_JS_URL . 'sdks/service-worker.js';
			echo "var PUSHENGAGE_APP_ID = '" . $app_id . "';";
			echo "importScripts('" . $pushengage_sw_url . "');";
		} elseif ( ! empty( $subdomain ) ) {
			echo "importScripts('https://" . $subdomain . ".pushengage.com/service-worker.js?ver=2.3.0');";
		} else {
			echo "console.error('You haven't finished setting up your site with PushEngage. Please connect your account!!')";
		}

		die();
	}


	/**
	* If user is logged in show then pushengage options in the create post/page
	*
	* @since 4.0.0
	*
	* @return void
	*/
	public function init_admin_options() {
		if ( ! is_user_logged_in() || false === $this->is_pushengage_active() ) {
			return;
		}

		add_action( 'add_meta_boxes', array( $this, 'add_pushengage_settings_metabox' ) );

		// action hook, to handle the case of draft and schedule post to send notification.
		add_action( 'save_post', array( $this, 'save_pushengage_post_meta_data' ) );
	}

	/**
	 * Enqueue segment addition scripts in frontend
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function enqueue_category_segmentation_script() {
		// return early if current page in not single post, attachment, page, custom post types
		if ( ! is_singular() ) {
			return;
		}

		$pushengage_settings   = Options::get_site_settings();
		$category_segmentation = '';

		if ( ! empty( $pushengage_settings['category_segmentation'] ) ) {
			$category_segmentation = json_decode( $pushengage_settings['category_segmentation'] );
		}

		if ( empty( $category_segmentation ) || empty( $category_segmentation->settings ) ) {
			return;
		}

		// Array of segment_id and segment_name that should be added to the user visiting the current page
		$add_segment = array();

		foreach ( $category_segmentation->settings   as $category_segmentation_setting ) {
			if ( ! empty( $category_segmentation_setting->category_name ) && has_category( $category_segmentation_setting->category_name ) ) {
				foreach ( $category_segmentation_setting->segment_mapping as $id => $name ) {
					$add_segment[ $id ] = $name;
				}
			}
		}

		if ( empty( $add_segment ) ) {
			return;
		}

		wp_enqueue_script(
			'pushengage-category-segment',
			PUSHENGAGE_PLUGIN_URL . 'assets/js/category-segmentation.js',
			array( 'pushengage-sdk-init' ),
			PUSHENGAGE_VERSION,
			true
		);
		wp_localize_script(
			'pushengage-category-segment',
			'pushengageCategorySegment',
			array(
				'addSegment' => $add_segment,
			)
		);
	}

	/**
	 * saving pushengage meta data along with the posts to handle draft and scheduled posts
	 *
	 * @since 4.0.0
	 *
	 * @param number $post_id
	 *
	 * @return void
	 */
	public function save_pushengage_post_meta_data( $post_id ) {
		// Ignore auto saving
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_posts', $post_id ) ) {
			return;
		}

		// Verify the nonce
		if ( ! isset( $_POST['pushengage_post_editor_nonce'] ) ) {
			return;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['pushengage_post_editor_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'pushengage_post_editor_nonce_action' ) ) {
			return;
		}

		$new_push_options = array();
		$old_push_options = Helpers::get_push_options_post_meta( $post_id );

		// Making sure 'pe_timestamp' property does not removed on each save operation
		if ( ! empty( $old_push_options['pe_timestamp'] ) ) {
			$new_push_options['pe_timestamp'] = $old_push_options['pe_timestamp'];
		}

		if ( isset( $_POST['pe_wp_send_post_checkbox'] ) ) {
			$new_push_options['pe_wp_send_post_checkbox'] = 1;
		}

		$formatted_post_meta = PostMetaFormatter::format( $_POST );
		$new_push_options    = array_merge( $new_push_options, $formatted_post_meta );

		// If new meta data is not empty then update the post meta with new details
		if ( ! empty( $new_push_options ) ) {
			update_post_meta( $post_id, 'pe_push_options', $new_push_options );
		} elseif ( ! empty( $old_push_options ) ) {
			// If new meta data is empty but old stored meta is not empty then
			// delete the post meta, so that we do not send notification for this post
			// based on old meta data
			delete_post_meta( $post_id, 'pe_push_options' );
		}
	}

	/**
	 * send push notifications on publishing of the post or page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function send_pe_push_notifications( $new_status, $old_status, $post ) {
		// Do not send notification if the post is not published or if the plugin is not active
		if ( empty( $post ) || 'publish' !== $new_status || false === $this->is_pushengage_active() ) {
			return;
		}

		// When submitting a post update or an new post in Gutenberg editor transition_post_status runs twice.
		// One REST request without $_POST data followed by a second pass with $_POST data.
		//
		// The second pass of transition_post_status happens only if there is a plugin that adds meta boxes. Since
		// our plugin adds metabox to the post editor, we need to make sure that we do not send notification on
		// the first pass.
		//
		// https://github.com/WordPress/gutenberg/issues/15094
		//
		// Do not send notification in the first pass of transition_post_status
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Do not send notification if the post is being restored from trash
		if ( $this->was_post_restored_from_trash( $old_status, $new_status ) ) {
			return;
		}

		// Do not send notification if the post type if not one of the allowed post types
		// for auto push
		if ( ! in_array( $post->post_type, Options::get_allowed_post_types_for_auto_push(), true ) ) {
			return;
		}

		// Do not sent notification for non-public post types.
		if ( function_exists( 'is_post_type_viewable' ) && ! is_post_type_viewable( $post->post_type ) ) {
			return;
		}

		// Do not send the notification if the filter returns "false"
		if ( has_filter( 'pushengage_auto_push_include_post' ) &&
			apply_filters( 'pushengage_auto_push_include_post', $new_status, $old_status, $post ) === false
		) {
			return;
		}

		// Do not send notification if the filter returns "true"
		if ( has_filter( 'pushengage_auto_push_exclude_post' ) &&
			apply_filters( 'pushengage_auto_push_exclude_post', $new_status, $old_status, $post ) === true
		) {
			return;
		}

		$pushengage_settings = Options::get_site_settings();
		$api_key             = $pushengage_settings['api_key'];
		$site_id             = $pushengage_settings['site_id'];
		$push_options        = Helpers::get_push_options_post_meta( $post->ID );

		// If a notification was already sent within 5 min then do not send another notification
		// meta tag 'pe_timestamp' is used to prevent multiple send within 5 minutes.
		// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		if ( ! empty( $push_options['pe_timestamp'] ) && $push_options['pe_timestamp'] >= date( 'Y-m-d H:i:s' ) ) {
			return;
		}

		// check if, pushengage send notification check box is checked.
		$is_instant_send = $this->is_sending_instant_post();
		$is_scheduled_send = $this->is_sending_scheduled_post( $new_status, $old_status, $post, $push_options );

		if ( ! $is_instant_send && ! $is_scheduled_send ) {
			return;
		}

		$site_info = HttpClient::get_site_info( $api_key );
		if ( empty( $site_info ) ) {
			// TODO:: Display error message to user
			return;
		}

		if ( $is_scheduled_send ) {
			$data = $this->format_schedule_notification_data( $post->ID, $site_info, $push_options );
		} else {
			$data = $this->format_notification_data( $post->ID, $site_info );
		}

		$metadata = array(
			'post_id'    => $post->ID,
			'old_status' => $old_status,
			'new_status' => $new_status,
			'send_type'  => $is_scheduled_send ? 'scheduled' : 'instant',
			'user_id'    => get_current_user_id(),
			'last_sent'  => isset( $push_options['pe_timestamp'] ) ? $push_options['pe_timestamp'] : '0',
		);

		$response = HttpClient::send_push_notification( $api_key, $site_id, $data, $metadata );

		if ( empty( $response ) ) {
			// An error occurred while sending the notification
			// TODO:: Display error message to user
			return;
		}

		// Add or update post metadata "pe_timestamp"  by 5 minutes, whenever
		// notification sent. so that we do not send another notification for
		// the same post in next 5 minutes.
		// phpcs:ignore
		$push_options['pe_timestamp'] = date( 'Y-m-d H:i:s', time() + 5 * MINUTE_IN_SECONDS );

		// When a post is published/updated from post editor then the order of
		// on_save_post and transition_post_status filter is inconsistent.
		// Sometimes on_save_post filter runs first and sometimes
		// transition_post_status runs first.
		//
		// After sending the notification we need to reset the
		// "pe_scheduled_send_post_checkbox" and "pe_wp_send_post_checkbox".
		// So that if the post is updated via another plugin or cron in future
		// we do not send the notification again. If user again edit the post
		// in the  editor then he will check the checkbox again and we will
		// send the notification.
		if ( $is_scheduled_send ) {
			unset( $push_options['pe_scheduled_send_post_checkbox'] );
			unset( $push_options['pe_wp_send_post_checkbox'] );
		}

		if ( $is_instant_send ) {
			unset( $_POST['pe_wp_send_post_checkbox'] );
		}

		update_post_meta( $post->ID, 'pe_push_options', $push_options );

	}

	/**
	 * Checks if we need to send notification when a scheduled post is being published
	 *
	 * @since 4.0.0
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 * @param array $push_options PushEngage post metabox options
	 */
	public function is_sending_scheduled_post( $new_status, $old_status, $post, $push_options ) {
		$send_notification = false;

		if ( 'publish' === $new_status && 'future' === $old_status && defined( 'DOING_CRON' ) && DOING_CRON ) {
			/*
			* Backward compatibility
			* plugin version less than version 4.0.0 were using the keys '_pe_override'
			* for checkbox field status and 'pe_override_scheduled'  for schedule post status
			*/
			if (
				get_post_meta( $post->ID, 'pe_override_scheduled', true ) &&
				get_post_meta( $post->ID, '_pe_override', true )
			) {
				$send_notification = true;
				delete_post_meta( $post->ID, 'pe_override_scheduled' );
				delete_post_meta( $post->ID, '_pe_override' );
			}

			if (
				! empty( $push_options['pe_scheduled_send_post_checkbox'] ) ||
				! empty( $push_options['pe_wp_send_post_checkbox'] )
			) {
				$send_notification = true;
			}
		}

		return $send_notification;
	}

	/**
	 * Check if, pushengage send notification check box is checked
	 * and current user have capability to publish post.
	 *
	 * @since 4.0.0
	 *
	 * @param array $data
	 *
	 * @return boolean
	 */
	public function is_sending_instant_post() {
		$send_notification = false;

		// 1. Check that when this post was created or updated, the PushEngage meta box in the editor screen was present
		// and checked
		// 2. verify the nonce field
		// 3. check if the current user has the capability to publish posts
		if (
			! empty( $_POST['pe_wp_send_post_checkbox'] )
			&& isset( $_POST['pushengage_post_editor_nonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pushengage_post_editor_nonce'] ) ), 'pushengage_post_editor_nonce_action' )
			&& current_user_can( 'publish_posts' )
		) {
			$send_notification = true;
		}

		return $send_notification;
	}


	/**
	 * Add meta boxes for pushengage setting options
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_pushengage_settings_metabox() {
		$screens = Options::get_allowed_post_types_for_auto_push();
		if ( empty( $screens ) ) {
			return;
		}

		add_meta_box(
			'pushengage-settings',
			esc_html__( 'PushEngage Push Notification Settings', 'pushengage' ),
			array( $this, 'render_post_editor_metabox' ),
			$screens,
			'normal',
			apply_filters( 'pushengage_post_settings_metabox_priority', 'high' )
		);
	}

	/**
	 * Renders the container UI for PushEngage Post Editor metabox
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_post_editor_metabox( $post ) {
		wp_enqueue_media();
		EnqueueAssets::enqueue_pushengage_scripts();
		EnqueueAssets::localize_script( $post->ID, true );
		Pushengage::output_view( 'post-editor-metabox.php' );
	}

	/**
	 * check pushengage plugin is active ot not.
	 *
	 * @since 4.0.0
	 *
	 */
	public function is_pushengage_active() {
		return Options::has_credentials();
	}

	/**
	 * Get utm params payload for sending push notification to the PushEngage API
	 * server. All field are optional and empty keys are not allowed
	 * @since 4.0.0
	 *
	 * @param array|null $push_options
	 * @param boolean $is_scheduled_post
	 *
	 * @return array|null
	 */
	public function get_utm_params( $push_options = null, $is_scheduled_post = false ) {
		$utm_params = array();
		$allowed_utm_keys_max_len = array(
			'pe_wp_utm_source'   => Constants::NOTIFICATION_UTM_SOURCE_MAX_LEN,
			'pe_wp_utm_medium'   => Constants::NOTIFICATION_UTM_MEDIUM_MAX_LEN,
			'pe_wp_utm_campaign' => Constants::NOTIFICATION_UTM_CAMPAIGN_MAX_LEN,
			'pe_wp_utm_term'     => Constants::NOTIFICATION_UTM_TERM_MAX_LEN,
			'pe_wp_utm_content'  => Constants::NOTIFICATION_UTM_CONTENT_MAX_LEN,
		);

		if ( $is_scheduled_post && ! empty( $push_options['pe_wp_utm_params_enabled'] ) ) {
			$utm_params = array();
			// Decode the entity to avoid double encoding and limit the string.
			foreach ( $allowed_utm_keys_max_len as $key => $max_len ) {
				$val = isset( $push_options[ $key ] ) ? Helpers::decode_entities( $push_options[ $key ] ) : '';
				$val = substr( $val, 0, $max_len );

				if ( ! empty( $val ) ) {
					// Remove the prefix 'pe_wp_' from the key before sending to PushEngage API server
					$utm_key_name = substr( $key, strlen( 'pe_wp_' ) );
					$utm_params[ $utm_key_name ] = $val;
				}
			}
		} elseif ( ! empty( $_POST['pe_wp_utm_params_enabled'] ) ) {
			foreach ( $allowed_utm_keys_max_len as $key => $max_len ) {
				$val = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
				$val = substr( Helpers::decode_entities( $val ), 0, $max_len );

				if ( ! empty( $val ) ) {
					// Remove the prefix 'pe_wp_' from the key before sending to PUSHENGAGE API server
					$utm_key_name = substr( $key, strlen( 'pe_wp_' ) );
					$utm_params[ $utm_key_name ] = $val;
				}
			}
		}

		if ( ! empty( $utm_params ) ) {
			$utm_params['enabled'] = true;
		}
		return $utm_params;
	}


	/**
	 * Get notification title
	 *
	 * @since 4.0.0
	 *
	 * @param number $post_id
	 * @param object $push_options
	 * @param boolean $is_scheduled_post
	 *
	 * @return string
	 */
	public function get_notification_title( $post_id, $push_options, $is_scheduled_post = false ) {
		$title = '';

		if ( ! empty( $post_id ) ) {
			$title = get_the_title( $post_id );
			$title = wp_strip_all_tags( $title );
		}

		// If post title is empty then use blog name as notification title
		if ( empty( $title ) ) {
			$title = get_bloginfo( 'name' );
		}

		// In the case of scheduled post, get custom title from 'pe_push_options' metadata
		if ( $is_scheduled_post && ! empty( $push_options['pe_wp_custom_title'] ) ) {
			$title = $push_options['pe_wp_custom_title'];
		} elseif ( ! empty( $_POST['pe_wp_custom_title'] ) ) {
			// In the case of instant post, get custom title from 'pe_wp_custom_title' $_POST
			$title = sanitize_text_field( wp_unslash( $_POST['pe_wp_custom_title'] ) );
		}

		return substr( Helpers::decode_entities( $title ), 0, Constants::NOTIFICATION_TITLE_MAX_LEN );
	}


	/**
	* Get notification message
	*
	* @since 4.0.0
	*
	* @param number $post_id
	* @param object $push_options
	* @param boolean $is_scheduled_post
	*
	* @return string
	*/
	public function get_notification_message( $post_id, $push_options, $is_scheduled_post = false ) {
		$message = '';

		if ( ! empty( $post_id ) ) {
			// If post has excerpt, then use it as notification message
			// Otherwise use post content as notification message
			if ( has_excerpt( $post_id ) ) {
				$message = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $post_id ) );
			} else {
				$message = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
			}
			$message = wp_strip_all_tags( $message );
		}

		// if notification message is empty then use the blog name as notification message
		if ( empty( $message ) ) {
			$message = get_bloginfo( 'name' );
		}

		// In the case of scheduled post, get custom message from 'pe_push_options' metadata
		// In the case of instant post, get custom title from 'pe_wp_custom_message' $_POST
		if ( $is_scheduled_post ) {
			if ( ! empty( $push_options['pe_wp_custom_message'] ) ) {
				$message = $push_options['pe_wp_custom_message'];
			} else {
				// Backward compatibility
				// plugin version less than version 4.0.0 has custom message saved in the
				// meta property '_pushengage_custom_text'
				$custom_msg = get_post_meta( $post_id, '_pushengage_custom_text', true );
				if ( ! empty( $custom_msg ) ) {
					$message = $custom_msg;
				}
			}
		} elseif ( ! empty( $_POST['pe_wp_custom_message'] ) ) {
			$message = sanitize_text_field( wp_unslash( $_POST['pe_wp_custom_message'] ) );
		}

		return substr( Helpers::decode_entities( $message ), 0, Constants::NOTIFICATION_MESSAGE_MAX_LEN );
	}

	/**
	* Get image from post
	*
	* @since 4.0.0
	*
	* @param string|int[] $size
	* @param number $post_id
	*
	* @return string
	*/
	public function get_post_image( $size, $post_id ) {
		$image_url = '';

		if ( has_post_thumbnail( $post_id ) ) {
			$raw_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
			if ( ! empty( $raw_image ) ) {
				$image_url = ! empty( $raw_image[0] ) ? $raw_image[0] : '';
			}
		}

		return $image_url;
	}

	/**
	* Get multi action button payload from $_POST request
	*
	* @param string $post_url The permalink url of the post
	* @return object
	*/
	public function get_multi_action_button( $post_url ) {
		$actions = array();

		for ( $index = 1; $index <= 2; $index++ ) {
			$label_key = 'pe_wp_btn' . $index . '_title';

			$label = isset( $_POST[ $label_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $label_key ] ) ) : '';
			$label = substr( Helpers::decode_entities( $label ), 0, Constants::NOTIFICATION_ACTION_LABEL_MAX_LEN );

			if ( ! empty( $label ) ) {
				// If action button URL is not given then use the post url as action URL
				// Only use the action button if the URL is valid for notification
				// action button URL expected by the API server
				$url_key = 'pe_wp_btn' . $index . '_url';
				$url = isset( $_POST[ $url_key ] ) ? esc_url_raw( $_POST[ $url_key ] ) : '';
				$url  = empty( $url ) ? $post_url : $url;

				if ( Helpers::is_http_or_https_url( $url, Constants::NOTIFICATION_ACTION_URL_MAX_LEN ) ) {
					$action = array(
						'label' => $label,
						'url'   => $url,
					);

					$img_url_key = 'pe_wp_btn' . $index . '_image_url';
					$img_url = isset( $_POST[ $img_url_key ] ) ? esc_url_raw( $_POST[ $img_url_key ] ) : '';
					if ( Helpers::is_http_or_https_url( $img_url, Constants::NOTIFICATION_ACTION_IMG_URL_MAX_LEN ) ) {
						$action['image_url'] = $img_url;
					}

					$actions[] = $action;
				}
			}
		}

		return $actions;
	}

	/**
	* Get multi action button payload from post meta data
	*
	* @since 4.0.0
	*
	* @param string $post_url The permalink URL of the post.
	* @param array $push_options
	*
	* @return object
	*/
	public function get_multi_action_button_from_post_meta( $post_url, $push_options ) {
		$actions = array();

		if ( empty( $push_options ) ) {
			return $actions;
		}

		for ( $index = 1; $index <= 2; $index++ ) {
			$label_key = 'pe_wp_btn' . $index . '_title';

			$label = isset( $push_options[ $label_key ] ) ? $push_options[ $label_key ] : '';
			$label = substr( Helpers::decode_entities( $label ), 0, Constants::NOTIFICATION_ACTION_LABEL_MAX_LEN );

			if ( ! empty( $label ) ) {
				// If action button URL is not given then use the post url for action URL
				//
				// Only use the action button if the URL is valid for notification action button URL as
				// expected by the API server
				$url_key = 'pe_wp_btn' . $index . '_url';
				$url = isset( $push_options[ $url_key ] ) ? esc_url_raw( $push_options[ $url_key ] ) : '';
				$url = empty( $url ) ? $post_url : $url;
				if ( Helpers::is_http_or_https_url( $url, Constants::NOTIFICATION_ACTION_URL_MAX_LEN ) ) {
					$action = array(
						'label' => $label,
						'url'   => $url,
					);

					$img_url_key = 'pe_wp_btn' . $index . '_image_url';
					$img_url = isset( $push_options[ $img_url_key ] ) ? esc_url_raw( $push_options[ $img_url_key ] ) : '';
					if ( Helpers::is_http_or_https_url( $img_url, Constants::NOTIFICATION_ACTION_IMG_URL_MAX_LEN ) ) {
						$action['image_url'] = $img_url;
					}

					$actions[] = $action;
				}
			}
		}
		return $actions;
	}

	/**
	* Format notifications data for sending to PushEngage API server
	*
	* @since 4.0.0
	*
	* @param string $post_id
	* @param array $site_info
	*
	* @return object
	*/
	public function format_notification_data( $post_id, $site_info ) {
		$pushengage_settings = Options::get_site_settings();

		$multi_action_button    = ArrayHelper::get( $pushengage_settings, 'multi_action_button', false );
		$notification_icon_type = ArrayHelper::get( $pushengage_settings, 'notification_icon_type', 'featured_image' );
		$featured_large_image   = ArrayHelper::get( $pushengage_settings, 'featured_large_image', false );

		$data['notification_title']   = $this->get_notification_title( $post_id, null, false );
		$data['notification_message'] = $this->get_notification_message( $post_id, null, false );
		$data['notification_url']     = get_permalink( $post_id );

		$image_url = $this->get_image_url( $notification_icon_type, $post_id, $site_info );
		if ( ! empty( $image_url )
			&& Helpers::is_http_or_https_url( $image_url, Constants::NOTIFICATION_SMALL_IMG_URL_MAX_LEN )
		) {
			$data['notification_image'] = $image_url;
		}

		$large_image_url = $this->get_large_image_url( $post_id, $featured_large_image );
		// If custom big image has higher priority
		if ( ! empty( $_POST['pe_wp_big_image'] ) ) {
			$large_image_url = esc_url_raw( $_POST['pe_wp_big_image'] );
		}
		if ( ! empty( $large_image_url )
			&& Helpers::is_http_or_https_url( $large_image_url, Constants::NOTIFICATION_BIG_IMG_URL_MAX_LEN )
		) {
			$data['big_image'] = $large_image_url;
		}

		if ( $multi_action_button ) {
			$action_btn = $this->get_multi_action_button( $data['notification_url'] );
			if ( ! empty( $action_btn ) ) {
				$data['actions'] = $action_btn;
			}
		}

		if ( ! empty( $_POST['pe_wp_audience_group_ids'] ) && is_array( $_POST['pe_wp_audience_group_ids'] ) ) {
			$groups_id = array_values(
				array_filter(
					$_POST['pe_wp_audience_group_ids'],
					function( $val ) {
						$val = intval( $val );
						return $val > 0;
					}
				)
			);
			if ( ! empty( $groups_id ) ) {
				$data['notification_criteria']['audience']['groups'] = array_map( 'intval', $groups_id );
			}
		}

		$utm_params = $this->get_utm_params();
		// Check if utm params are present
		// If present then add to notification data else ignore
		if ( ! empty( $utm_params ) ) {
			$data['utm_params'] = $utm_params;
		}

        //phpcs:ignore
		$data['source'] = 'wordpress';
		$data['status'] = 'sent';

		return $data;
	}

	/**
	* Returns the formatted the notification data from a schedule post for
	* sending to PushEngage API server
	*
	* @since 4.0.0
	*
	* @param number $post_id
	* @param array $site_info
	* @param array $push_options
	*
	* @return object
	*/
	public function format_schedule_notification_data( $post_id, $site_info, $push_options ) {
		$pushengage_settings = Options::get_site_settings();

		$notification_icon_type = ArrayHelper::get( $pushengage_settings, 'notification_icon_type', 'featured_image' );
		$featured_large_image   = ArrayHelper::get( $pushengage_settings, 'featured_large_image', false );

		$data['notification_title']   = $this->get_notification_title( $post_id, $push_options, true );
		$data['notification_message'] = $this->get_notification_message( $post_id, $push_options, true );
		$data['notification_url']     = get_permalink( $post_id );

		$image_url = $this->get_image_url( $notification_icon_type, $post_id, $site_info );
		if ( ! empty( $image_url )
			&& Helpers::is_http_or_https_url( $image_url, Constants::NOTIFICATION_SMALL_IMG_URL_MAX_LEN )
		) {
			$data['notification_image'] = $image_url;
		}

		// If custom big image has higher priority
		$large_image_url = $this->get_large_image_url( $post_id, $featured_large_image );
		if ( ! empty( $push_options['pe_wp_big_image'] ) ) {
			$large_image_url = $push_options['pe_wp_big_image'];
		}
		if ( ! empty( $large_image_url ) &&
			Helpers::is_http_or_https_url( $large_image_url, Constants::NOTIFICATION_BIG_IMG_URL_MAX_LEN )
		) {
			$data['big_image'] = $large_image_url;
		}

		$action_btn = $this->get_multi_action_button_from_post_meta( $data['notification_url'], $push_options );
		if ( ! empty( $action_btn ) ) {
			$data['actions'] = $action_btn;
		}

		// Notification criteria
		if ( ! empty( $push_options['pe_wp_audience_group_ids'] ) ) {
			$data['notification_criteria']['audience']['groups'] = array_map( 'intval', $push_options['pe_wp_audience_group_ids'] );
		} else {
			/*
			*  Backward compatibility
			*  plugin version less than version 4.0.0 has segment selection option and selected segment
			*  ids were saved on meta property '_sedule_notification' separated by space. If
			*  `_sedule_notification` meta is present then transform that to raw notification criteria
			* object
			*/
			$segments_str = get_post_meta( $post_id, '_sedule_notification', true );
			if ( ! empty( $segments_str ) ) {
				$segments = explode( ' ', $segments_str );
				if ( ! empty( $segments ) ) {
					$segments                      = array_map( 'intval', $segments );
					$data['notification_criteria'] = array(
						'filter' => array(
							'value' => array(
								array(
									array(
										'field' => 'segments',
										'op'    => 'in',
										'value' => $segments,
									),
								),
							),
						),
					);
				}
			}
		}

		// UTM Params
		$utm_params = $this->get_utm_params( $push_options, true );
		// Check if utm params are present
		// If present then add to notification data else ignore
		if ( ! empty( $utm_params ) ) {
			$data['utm_params'] = $utm_params;
		}
        // phpcs:ignore
		$data['source'] = 'wordpress';
		$data['status'] = 'sent';

		return $data;
	}

	/**
	 * Get image url from post image or get site image
	 *
	 * @since 4.0.0
	 *
	 * @param string $notification_icon_type
	 * @param integer $post_id
	 * @param array $site_info
	 *
	 * @return string
	 */
	public function get_image_url( $notification_icon_type, $post_id, $site_info ) {
		$image_url = '';

		if ( 'featured_image' === $notification_icon_type && ! empty( $post_id ) ) {
			$post_image_url = $this->get_post_image( 'thumbnail', $post_id );

			if ( ! empty( $post_image_url ) ) {
				$image_url = $post_image_url;
			}
		}

		// if image url is missing then, put site_image as notification image
		if ( empty( $image_url ) && ! empty( $site_info ) ) {
			$image_url = ArrayHelper::get( $site_info, 'site.site_image', '' );
		}

		return $image_url;
	}

	/**
	 * Get large image url
	 *
	 * @since 4.0.0
	 *
	 * @param integer $post_id
	 * @param boolean $use_featured_img_as_large_img Weather to use post feature image as notification large image
	 *
	 * @return string
	 */
	public function get_large_image_url( $post_id, $use_featured_img_as_large_img ) {
		$large_image_url = '';

		if ( ! empty( $use_featured_img_as_large_img ) && ! empty( $post_id ) ) {
			$image_url = $this->get_post_image( $this->image_size, $post_id );

			if ( ! empty( $image_url ) ) {
				$large_image_url = $image_url;
			}
		}

		return $large_image_url;
	}

	/**
	 * Determines if a post is being restored form trash
	 *
	 * @param string $old_status
	 * @param string $new_status
	 * @return boolean
	 */
	public function was_post_restored_from_trash( $old_status, $new_status ) {
		return 'trash' === $old_status && 'publish' === $new_status;
	}
}


