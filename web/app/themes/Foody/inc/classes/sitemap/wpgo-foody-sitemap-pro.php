<?php
/*
Plugin Name: Simple Sitemap Pro
Plugin URI: http://www.wpgoplugins.com
Description: An effective way to display a sitemap of content on your site. Rendered sitemap is fully responsive and looks great on all devices!
Version: 2.1
Author: David Gwyer
Author URI: http://www.wpgoplugins.com
*/

/*  Copyright 2009 David Gwyer (email : hello@wpgoplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Main Plugin class definition
class WPGO_Foody_Simple_Sitemap_Pro {

	/* Class properties. */
	protected $_plugin_options_class;
//	protected $_edd_updater_manager_class;
	protected $_thumb_size = 22; // pixels

	/* Main class constructor. */
	public function __construct() {

		$this->constants(); // define framework constants
		$this->bootstrap();    // load plugin classes

		$this->load_supported_features();

		/* Setup the plugin text domain and .mo file for translation. */
		add_action( 'plugins_loaded', array( &$this, 'localize_plugin' ) );
		add_filter( 'widget_text', 'do_shortcode' ); // make sitemap shortcode work in text widgets
	}

	/* Add shortcode files */
	public function load_supported_features() {

		// [simple-sitemap] shortcode
		require_once( 'foody-sitemap-shortcode.php' );
		new WPGO_Foody_Sitemap_Shortcode();

		// [foody-simple-sitemap-group] shortcode
		require_once( 'foody-sitemap-group-shortcode.php' );
		new WPGO_Foody_Sitemap_Group_Shortcode();

//		new WPGO_Foody_Sitemap_Menu_Shortcode();

		// [foody-simple-sitemap-tax] shortcode
		require_once( 'foody-sitemap-tax-shortcode.php' );
		new WPGO_Foody_Sitemap_Tax_Shortcode();

	}

	/**
	 * Defines plugin constants.
	 */
	public function constants() {

		/* Define main plugin name constants. */
		define( "WPGO_SIMPLE_SITEMAP_PRO_NAME", "Simple Sitemap Pro" );
		define( "WPGO_SIMPLE_SITEMAP_PRO_NAME_U", strtolower( str_replace( " ", "_", WPGO_SIMPLE_SITEMAP_PRO_NAME ) ) ); // underscored lower case plugin name
		define( "WPGO_SIMPLE_SITEMAP_PRO_NAME_H", strtolower( str_replace( " ", "-", WPGO_SIMPLE_SITEMAP_PRO_NAME ) ) ); // hyphenated lower case plugin name

		/* Define plugin options constants. */
		define( "WPGO_SIMPLE_SITEMAP_PRO_OPTIONS_DB_NAME", WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_plugin_options" );
		define( "WPGO_SIMPLE_SITEMAP_PRO_CUSTOMIZE_DB_NAME", WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_customize_options" );
	}

	/**
	 * Bootstrap core functionality.
	 */
	public function bootstrap() {

		// Load classes/resources
//		if ( ! class_exists( 'EDD_Plugin_Updater_Manager' ) ) // could be defined in other plugins too
//			require_once( plugin_dir_path( __FILE__ ) . 'classes/updater/EDD_Plugin_Updater_Manager.php' );
//
		require_once( plugin_dir_path( __FILE__ ) . 'classes/hooks.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'classes/plugin-options.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-wpgo-walker-page.php' );

		$plugin_options_args = array(
			'plugin_name'     => WPGO_SIMPLE_SITEMAP_PRO_NAME,
			'options_group'   => WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_plugin_options_group",
			'menu_slug'       => WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_admin_options_menu",
			'options_section' => 'simple_sitemap_pro_default',
			'options_db_name' => WPGO_SIMPLE_SITEMAP_PRO_OPTIONS_DB_NAME,
			// use this where possible, i.e. in non-static methods
			'plugin_root'     => __FILE__,
			// don't edit this
		);

		$update_manager_args = array(
			'edd_store_url'              => 'http://www.wpgoplugins.com',
			'edd_store_url_account_page' => 'https://wpgoplugins.com/my-account',
			'edd_item_name'              => 'Simple Sitemap Pro',
			// plugin name
			'edd_item_id'                => '805',
			'page_title'                 => 'Simple Sitemap Pro Options Page',
			'menu_title'                 => 'Simple Sitemap Pro',
			'options_page_title'         => 'Simple Sitemap Pro Plugin',
			'license_key_status_expiry'  => DAY_IN_SECONDS,
			'plugin_root'                => __FILE__,
			// don't edit this
			'plugin_name_slug'           => WPGO_SIMPLE_SITEMAP_PRO_NAME_H,
			// plugin name slug label (used mainly in options page)
			'plugin_dir_path'            => plugin_dir_path( __FILE__ ),
			'options_group'              => WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_plugin_options_group",
			'menu_slug'                  => WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_admin_options_menu",
			'plugin_options_path'        => admin_url() . "options-general.php?page=" . WPGO_SIMPLE_SITEMAP_PRO_NAME_U . "_admin_options_menu",
			'edd_auto_updates_option'    => 'edd_auto_updates_simple_sitemap_pro_option',
			'options_section'            => 'simple_sitemap_pro_default'
		);

		// Instantiate plugin classes
		$this->_plugin_options_class = new WPGO_Simple_Sitemap_Pro_Options( $plugin_options_args );
//		$this->_edd_updater_manager_class	= new EDD_Plugin_Updater_Manager($update_manager_args);
	}

	/**
	 * Add Plugin localization support.
	 *
	 * @since 0.2.0
	 */
	public function localize_plugin() {

		load_plugin_textdomain( 'wpgo-simple-sitemap-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	public static function get_the_title( $title_text, $permalink, $args, $parent_page = false, $parent_page_link = '1' ) {

		$links       = $args['links'];
		$title_open  = $args['title_open'];
		$title_close = $args['title_close'];
		$nofollow    = $args['nofollow'];
		if ( $nofollow === '1' ) {
			$nofollow = ' rel="nofollow"';
		} else {
			$nofollow = '';
		}

		if ( ! empty( $title_text ) ) {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $nofollow . '>' . wp_kses_post( $title_text ) . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $nofollow . '>' . wp_kses_post( $title_text ) . '</a>' . $title_close;
			} else {
				$title = $title_open . wp_kses_post( $title_text ) . $title_close;
			}
		} else {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $nofollow . '>' . '(no title)' . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $nofollow . '>' . '(no title)' . '</a>' . $title_close;
			} else {
				$title = $title_open . '(no title)' . $title_close;
			}
		}

		return $title;
	}

	// FUNCTIONS IMPORTED AND CUSTOMISED FROM WP CORE TO ADD REL NOFOLLOW TO INTERNAL LINKS
	public static function wp_rel_nofollow( $text ) {
		// This is a pre save filter, so text is already escaped.
		$text = stripslashes( $text );
		$text = preg_replace_callback( '|<a (.+?)>|i', array(
			'WPGO_Simple_Sitemap_Pro',
			'wp_rel_nofollow_callback'
		), $text );

		return wp_slash( $text );
	}

	public static function wp_rel_nofollow_callback( $matches ) {
		$text = $matches[1];
		$atts = shortcode_parse_atts( $matches[1] );
		$rel  = 'nofollow';

		// the code below was comment out as it prevents adding nofollow to external links
		/*if ( preg_match( '%href=["\'](' . preg_quote( set_url_scheme( home_url(), 'http' ) ) . ')%i', $text ) ||
		     preg_match( '%href=["\'](' . preg_quote( set_url_scheme( home_url(), 'https' ) ) . ')%i', $text )
		) {
			return "<a $text>";
		}*/

		if ( ! empty( $atts['rel'] ) ) {
			$parts = array_map( 'trim', explode( ' ', $atts['rel'] ) );
			if ( false === array_search( 'nofollow', $parts ) ) {
				$parts[] = 'nofollow';
			}
			$rel = implode( ' ', $parts );
			unset( $atts['rel'] );

			$html = '';
			foreach ( $atts as $name => $value ) {
				$html .= "{$name}=\"$value\" ";
			}
			$text = trim( $html );
		}

		return "<a $text rel=\"$rel\">";
	}

} /* End class definition */

/* Create Plugin class instance */
$wpgo_simple_sitemap_pro_plugin = new WPGO_Foody_Simple_Sitemap_Pro();