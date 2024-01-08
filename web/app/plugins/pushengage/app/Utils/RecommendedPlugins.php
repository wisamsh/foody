<?php
namespace Pushengage\Utils;

use Pushengage\Utils\PluginUpgraderSkin;
use Pushengage\Utils\PluginUpgraderSilent;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RecommendedPlugins {

	/**
	 * EDD plugin base URL
	 *
	 * @since 4.0.0
	 */
	const EDD_URL = 'easy-digital-downloads/easy-digital-downloads.php';
	const EDD_PRO_URL = 'easy-digital-downloads-pro/easy-digital-downloads.php';

	/**
	 * SEO Pack Pro Plugin Base URL
	 *
	 * @since 4.0.0
	 */
	const SEO_PACK_PRO_URL = 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php';

	/**
	 * An array of links to install the plugins from.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	public static $plugin_links = array(
		'optinmonster'    => 'https://downloads.wordpress.org/plugin/optinmonster.zip',
		'wpforms-lite'    => 'https://downloads.wordpress.org/plugin/wpforms-lite.zip',
		'wp-mail-smtp'    => 'https://downloads.wordpress.org/plugin/wp-mail-smtp.zip',
		'coming-soon'     => 'https://downloads.wordpress.org/plugin/coming-soon.zip',
		'rafflepress'     => 'https://downloads.wordpress.org/plugin/rafflepress.zip',
		'monsterinsights' => 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip',
		'aioseo'          => 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip',
		'affiliateWp'     => 'https://downloads.wordpress.org/plugin/affiliatewp-external-referral-links.zip',
		'edd'             => 'https://downloads.wordpress.org/plugin/easy-digital-downloads.zip',
	);

	/**
	 * Get list of addons
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	public static function get_addons() {
		$parsed_addons     = array();
		$installed_plugins = get_plugins();

		// OptinMonster.
		$parsed_addons['optinmonster'] = array(
			'active'    => class_exists( 'OMAPI' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-om.png',
			'title'     => 'OptinMonster',
			'excerpt'   => 'Instantly get more subscribers, leads, and sales with the #1 conversion optimization toolkit. Create high converting popups, announcement bars, spin a wheel, and more with smart targeting and personalization.',
			'installed' => array_key_exists( 'optinmonster/optin-monster-wp-api.php', $installed_plugins ),
			'basename'  => 'optinmonster/optin-monster-wp-api.php',
			'slug'      => 'optinmonster',
			'settings'  => admin_url( 'admin.php?page=optin-monster-dashboard' ),
		);

		// MonsterInsight
		$parsed_addons['monsterinsights'] = array(
			'active'    => function_exists( 'MonsterInsights' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-mi.png',
			'title'     => 'MonsterInsights',
			'excerpt'   => 'MonsterInsights makes it effortless to properly connect your WordPress site with Google Analytics, so you can start making data-driven decisions to grow your business.',
			'installed' => array_key_exists( 'google-analytics-for-wordpress/googleanalytics.php', $installed_plugins ),
			'basename'  => 'google-analytics-for-wordpress/googleanalytics.php',
			'slug'      => 'monsterinsights',
			'settings'  => admin_url( 'admin.php?page=monsterinsights_settings' ),
		);

		// WPForms.
		$parsed_addons['wpforms-lite'] = array(
			'active'    => function_exists( 'wpforms' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-wpforms.png',
			'title'     => 'WPForms',
			'excerpt'   => 'The best drag & drop WordPress form builder. Easily create beautiful contact forms, surveys, payment forms, and more with our 150+ form templates. Trusted by over 5 million websites as the best forms plugin.',
			'installed' => array_key_exists( 'wpforms-lite/wpforms.php', $installed_plugins ) || array_key_exists( 'wpforms/wpforms.php', $installed_plugins ),
			'basename'  => 'wpforms-lite/wpforms.php',
			'slug'      => 'wpforms-lite',
			'settings'  => admin_url( 'admin.php?page=wpforms-overview' ),
		);

		// AIOSEO.
		$is_aioseo_pro_installed = false;
		if ( array_key_exists( self::SEO_PACK_PRO_URL, $installed_plugins ) ) {
			$is_aioseo_pro_installed = true;
		}
		$parsed_addons['aioseo'] = array(
			'active'    => function_exists( 'aioseo' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-all-in-one-seo.png',
			'title'     => 'AIOSEO',
			'excerpt'   => 'The original WordPress SEO plugin and toolkit that improves your website’s search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.',
			'installed' => array_key_exists( 'all-in-one-seo-pack/all_in_one_seo_pack.php', $installed_plugins ) ||
				array_key_exists( self::SEO_PACK_PRO_URL, $installed_plugins ),
			'basename'  => $is_aioseo_pro_installed ? self::SEO_PACK_PRO_URL : 'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'slug'      => 'aioseo',
			'settings'  => admin_url( 'admin.php?page=aioseo' ),
		);

		// SeedProd.
		$parsed_addons['coming-soon'] = array(
			'active'    => defined( 'SEEDPROD_VERSION' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-seedprod.png',
			'title'     => 'SeedProd',
			'excerpt'   => 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.',
			'installed' => array_key_exists( 'coming-soon/coming-soon.php', $installed_plugins ),
			'basename'  => 'coming-soon/coming-soon.php',
			'slug'      => 'coming-soon',
			'settings'  => admin_url( 'admin.php?page=seedprod_lite' ),
		);

		// WP Mail Smtp.
		$parsed_addons['wp-mail-smtp'] = array(
			'active'    => function_exists( 'wp_mail_smtp' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-smtp.png',
			'title'     => 'WP Mail SMTP',
			'excerpt'   => 'Improve your WordPress email deliverability and make sure that your website emails reach user’s inbox with the #1 SMTP plugin for WordPress. Over 2 million websites use it to fix WordPress email issues.',
			'installed' => array_key_exists( 'wp-mail-smtp/wp_mail_smtp.php', $installed_plugins ),
			'basename'  => 'wp-mail-smtp/wp_mail_smtp.php',
			'slug'      => 'wp-mail-smtp',
		);

		// RafflePress
		$parsed_addons['rafflepress'] = array(
			'active'    => function_exists( 'rafflepress_lite_activation' ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/pluign-rafflepress.png',
			'title'     => 'RafflePress',
			'excerpt'   => 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.',
			'installed' => array_key_exists( 'rafflepress/rafflepress.php', $installed_plugins ),
			'basename'  => 'rafflepress/rafflepress.php',
			'slug'      => 'rafflepress',
			'settings'  => admin_url( 'admin.php?page=rafflepress_lite' ),
		);

		// AffiliateWP
		$parsed_addons['affiliateWp'] = array(
			'active'      => class_exists( 'AffiliateWP_External_Referral_Links' ),
			'icon'        => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-affiliate.png',
			'title'       => 'AffiliateWP',
			'excerpt'     => 'The #1 affiliate management plugin for WordPress. Easily create an affiliate program for your eCommerce store or membership site within minutes and start growing your sales with the power of referral marketing.',
			'installed'   => array_key_exists( 'affiliate-wp/affiliate-wp.php', $installed_plugins ),
			'basename'    => 'affiliate-wp/affiliate-wp.php',
			'slug'        => 'affiliateWp',
			'settings'    => admin_url( 'admin.php?page=affiliate-wp' ),
			'redirectUrl' => 'https://affiliatewp.com/?utm_source=pushengageplugin&utm_medium=link&utm_campaign=About%20PushEngage',
		);

		// Easy Digital Downloads (EDD)
		$is_edd_pro_installed = false;
		if ( array_key_exists( self::EDD_PRO_URL, $installed_plugins ) ) {
			$is_edd_pro_installed = true;
		}
		$parsed_addons['edd'] = array(
			'active'    => is_plugin_active( self::EDD_URL ) || is_plugin_active( self::EDD_PRO_URL ),
			'icon'      => PUSHENGAGE_PLUGIN_URL . '/assets/img/plugin-edd.png',
			'title'     => 'Easy Digital Downloads',
			'excerpt'   => 'The best WordPress eCommerce plugin for selling digital downloads. Start selling eBooks, software, music, digital art, and more within minutes. Accept payments, manage subscriptions, advanced access control, and more.',
			'installed' => array_key_exists( self::EDD_PRO_URL, $installed_plugins ) || array_key_exists( self::EDD_URL, $installed_plugins ),
			'basename'  => $is_edd_pro_installed ? self::EDD_PRO_URL : self::EDD_URL,
			'slug'      => 'edd',
			'settings'  => admin_url( 'admin.php?page=edd' ),
		);

		return $parsed_addons;
	}

	/**
	 * Check if specific addon is installed or not
	 *
	 * @since 4.0.0
	 *
	 * @param string $slug Slug of the addon
	 * @return boolean
	 */
	public static function is_addon_installed( $slug ) {
		$addon = self::get_addons();
		if ( isset( $addon[ $slug ] ) ) {
			return $addon[ $slug ]['installed'];
		}

		return false;
	}

	/**
	 * Get specific addon by addon slug
	 *
	 * @since 4.0.0
	 *
	 * @param string $slug Slug of the addon
	 * @return array|null
	 */
	public static function get_addon( $slug ) {
		$addon = self::get_addons();
		if ( isset( $addon[ $slug ] ) ) {
			return $addon[ $slug ];
		}

		return null;
	}

	/**
	 * Install / Activate addon
	 *
	 * @param string $slug
	 * @return string|boolean
	 */
	public static function install( $slug ) {
		// sanitize addon slug
		$slug = isset( $slug ) ? sanitize_text_field( wp_unslash( $slug ) ) : false;

		if ( ! $slug ) {
			return false;
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/template.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
		require_once ABSPATH . 'wp-admin/includes/screen.php';

		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'pushengage',
				),
				admin_url( 'admin.php' )
			)
		);

		// Create the plugin upgrader with our custom skin.
		$installer = new PluginUpgraderSilent( new PluginUpgraderSkin() );

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

		// Activate the plugin silently.
		$addon      = self::get_addon( $slug );
		$plugin_url = ! empty( $addon['basename'] ) ? $addon['basename'] : '';
		$activated  = activate_plugin( $plugin_url );

		if ( ! is_wp_error( $activated ) ) {
			return $slug;
		}

		// Using output buffering to prevent the FTP form from being displayed in the screen.
		ob_start();
		$creds = request_filesystem_credentials( $url, '', false, false, null );
		ob_end_clean();

		// Check for file system permissions.
		if ( false === $creds ) {
			return false;
		}

		// Error check.
		if ( ! method_exists( $installer, 'install' ) ) {
			return false;
		}

		$install_link = ! empty( self::$plugin_links[ $slug ] ) ? self::$plugin_links[ $slug ] : null;

		$installer->install( $install_link );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin_base_name = $installer->plugin_info();

		if ( ! $plugin_base_name ) {
			return false;
		}

		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_base_name );

		if ( is_wp_error( $activated ) ) {
			return false;
		}

		return $plugin_base_name;
	}
}
