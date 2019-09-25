<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/5/18
 * Time: 11:51 AM
 */

register_setting( 'discussion', 'hid_per_page' );
register_setting( 'discussion', 'whatsapp_phone_number_toggle' );
register_setting( 'discussion', 'whatsapp_phone_number' );
register_setting( 'general', 'foody_404_text' );
register_setting( 'general', 'foody_show_ingredients_conversion' );
register_setting( 'general', 'foody_conversion_table_link_show' );
register_setting( 'general', 'foody_conversion_table_link' );
register_setting( 'general', 'foody_conversion_table_link_target' );
register_setting( 'general', 'foody_conversion_table_link_text' );
register_setting( 'general', 'foody_google_tag_manager_id' );
register_setting( 'general', 'foody_show_google_adx' );
register_setting( 'general', 'foody_google_adx_script' );
register_setting( 'general', 'foody_show_newsletter_popup' );
register_setting( 'general', 'foody_id_for_newsletter' );
register_setting( 'reading', 'foody_show_post_views' );
register_setting( 'reading', 'foody_show_followers_count_views' );

$page_name_search_options   = __( 'הגדרות חיפוש - פודי', 'foody' );
$page_name_purchase_buttons = __( 'כפתורי רכישה', 'foody' );

/** @var array $options_pages
 * All ACF Options Pages.
 * page_title,menu_title and menu_slug
 * are required.
 * If post_id is not set, default will be 'options'
 */
$options_pages = array(
	array(
		'page_title' => $page_name_search_options,
		'menu_title' => $page_name_search_options,
		'menu_slug'  => 'foody-search-options.php',
		'post_id'    => 'foody_search_options',
		'icon_url'   => 'dashicons-search'
	),
	array(
		'page_title' => $page_name_purchase_buttons,
		'menu_title' => $page_name_purchase_buttons,
		'menu_slug'  => 'foody-purchase-options.php',
		'post_id'    => 'foody_purchase_options',
		'icon_url'   => 'dashicons-cart'
	)
);


/** @var array $default_args
 * default arguments to @see acf_add_options_page()
 * Will be merged with the specific arguments
 * set in @see $options_pages
 */
$default_args = array(
	/* (int|string) The position in the menu order this menu should appear.
	WARNING: if two menu items use the same position attribute, one of the items may be overwritten so that only one item displays!
	Risk of conflict can be reduced by using decimal instead of integer values, e.g. '63.3' instead of 63 (must use quotes).
	Defaults to bottom of utility menu items */
	'position'        => false,

	/* (string) The slug of another WP admin page. if set, this will become a child page. */
	'parent_slug'     => '',

	/* (string) The icon class for this menu. Defaults to default WordPress gear.
	Read more about dashicons here: https://developer.wordpress.org/resource/dashicons/ */
	'icon_url'        => false,

	/* (boolean) If set to true, this options page will redirect to the first child page (if a child page exists).
	If set to false, this parent page will appear alongside any child pages. Defaults to true */
	'redirect'        => true,

	/* (int|string) The '$post_id' to save/load data to/from. Can be set to a numeric post ID (123), or a string ('user_2').
	Defaults to 'options'. Added in v5.2.7 */
	'post_id'         => 'options',

	/* (boolean)  Whether to load the option (values saved from this options page) when WordPress starts up.
	Defaults to false. Added in v5.2.8. */
	'autoload'        => false,

	/* (string) The update button text. Added in v5.3.7. */
	'update_button'   => __( 'Update', 'acf' ),

	/* (string) The message shown above the form on submit. Added in v5.6.0. */
	'updated_message' => __( "Options Updated", 'acf' ),

);


if ( function_exists( 'acf_add_options_page' ) ) {
	foreach ( $options_pages as $options_page_args ) {
		if ( validate_args( $options_page_args ) ) {
			$args = array_merge( $default_args, $options_page_args );
			acf_add_options_page( $args );
		}
	}
}


/**
 * Validates arguments for @param $args array specific arguments to a page
 * @return bool true if required arguments are set
 * @see acf_add_options_page()
 *
 */
function validate_args( $args ) {
	$valid = false;
	if ( $args != null ) {
		if ( isset( $args['page_title'] ) && isset( $args['menu_title'] ) && isset( $args['menu_slug'] ) ) {
			$valid = true;
		}
	}

	return $valid;
}


function foody_custom_options() {
	// number of how i did per page
	add_settings_field( 'hid_per_page', __( 'מספר ״איך יצא לי״ בעמוד' ), 'foody_hid_per_page_callback', 'discussion' );
	function foody_hid_per_page_callback() {
		$options = get_option( 'hid_per_page', 3 );

		echo '<input type="number" id="hid_per_page" name="hid_per_page" value="' . $options . '">';

	}

	// WhatsApp business phone number
	add_settings_field( 'whatsapp_phone_number', __( 'מספר טלפון (WhatsApp)' ), 'foody_whatsapp_phone_number_callback', 'discussion' );
	function foody_whatsapp_phone_number_callback() {

		$options = get_option( 'whatsapp_phone_number' );

		echo '<input type="tel" id="whatsapp_phone_number" name="whatsapp_phone_number" value="' . $options . '">';

	}

	// WhatsApp business toggle
	add_settings_field( 'whatsapp_phone_number_toggle', __( 'הצג WhatsApp' ), 'foody_whatsapp_phone_number_toggle_callback', 'discussion' );
	function foody_whatsapp_phone_number_toggle_callback() {
		$options = get_option( 'whatsapp_phone_number_toggle', false );
		$checked = $options ? 'checked' : '';
		echo '<input ' . $checked . ' type="checkbox" id="whatsapp_phone_number_toggle" name="whatsapp_phone_number_toggle">';

	}

	// WhatsApp business toggle
	add_settings_field( 'foody_404_text', __( 'טקסט 404' ), 'foody_404_text_callback', 'general' );
	function foody_404_text_callback() {
		$content = get_option( 'foody_404_text', '' );
		wp_editor( $content, 'foody_404_text', $settings = array( 'textarea_rows' => '10' ) );
	}


	// General Foody settings
	add_settings_section(
		'foody_general_settings',
		'הגדרות אתר כלליות',
		'foody_settings_section_description',
		'general'
	);

	// Should show Ingredients
	add_settings_field( 'foody_conversion_table_link_show', __( 'הצגת קישור לטבלת המרות', 'foody' ), 'foody_conversion_table_link_show_callback', 'general', 'foody_general_settings' );
	add_settings_field( 'foody_conversion_table_link', __( 'קישור לטבלת המרות', 'foody' ), 'foody_conversion_table_link_callback', 'general', 'foody_general_settings' );
	add_settings_field( 'foody_conversion_table_link_target', __( 'פתח טבלת המרות בחלון חדש', 'foody' ), 'foody_conversion_table_link_target_callback', 'general', 'foody_general_settings' );
	add_settings_field( 'foody_conversion_table_link_text', __( 'טקסט קישור לטבלת המרות', 'foody' ), 'foody_conversion_table_link_text_callback', 'general', 'foody_general_settings' );

	// Should show Ingredients
	add_settings_field( 'foody_show_ingredients_conversion', __( 'הצגת טבלת ערכים תזונתיים', 'foody' ), 'foody_show_ingredients_callback', 'general', 'foody_general_settings' );

	// Google tag manager id
	add_settings_field( 'foody_google_tag_manager_id', __( 'מזהה Google Tag Manager', 'foody' ), 'foody_show_tag_manager_callback', 'general', 'foody_general_settings' );

	// Show Google AdX feature
	add_settings_field( 'foody_show_google_adx', __( 'הצג רכיב Google AdX', 'foody' ), 'foody_show_google_adx_callback', 'general', 'foody_general_settings' );
	add_settings_field( 'foody_google_adx_script', __( 'סקריפט רכיב Google AdX', 'foody' ), 'foody_google_adx_script_callback', 'general', 'foody_general_settings' );

	// show newsletter popup
    add_settings_field( 'foody_show_newsletter_popup', __( 'הצג newsletter popup', 'foody' ), 'foody_show_newsletter_popup_callback', 'general', 'foody_general_settings' );
    add_settings_field( 'foody_id_for_newsletter', __( 'מזהה newsletter', 'foody' ), 'foody_id_for_newsletter_callback', 'general', 'foody_general_settings' );

	// Toggle post views visibility
	add_settings_field( 'foody_show_post_views', __( 'הצג כמות צפיות', 'foody' ), 'foody_show_post_views_callback', 'reading' );

	// Toggle channel & authors followers visibility
	add_settings_field( 'foody_show_followers_count_views', __( 'הצג כמות עוקבים', 'foody' ), 'foody_show_followers_count_callback', 'reading' );

}

add_action( 'admin_init', 'foody_custom_options' );

// Show foody_conversion_table_link_show field
function foody_conversion_table_link_show_callback() {
	$options = get_option( 'foody_conversion_table_link_show', false );
	$checked = $options ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" id="foody_conversion_table_link_show" name="foody_conversion_table_link_show">';
}

// Show foody_conversion_table_link field
function foody_conversion_table_link_callback() {
	$options = get_option( 'foody_conversion_table_link', false );
	echo '<input type="url" size="50" id="foody_conversion_table_link" name="foody_conversion_table_link" value="' . $options . '">';
}

// Show foody_conversion_table_link_target field
function foody_conversion_table_link_target_callback() {
	$options = get_option( 'foody_conversion_table_link_target', false );
	$checked = $options ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" id="foody_conversion_table_link_target" name="foody_conversion_table_link_target">';
}

// Show foody_conversion_table_link_text field
function foody_conversion_table_link_text_callback() {
	$options = get_option( 'foody_conversion_table_link_text', false );
	echo '<input type="text" size="25" id="foody_conversion_table_link_text" name="foody_conversion_table_link_text" value="' . $options . '">';
}

function add_units_columns( $columns ) {
	$columns['foo'] = 'Foo';

	return $columns;
}

function foody_settings_section_description() {
	echo '';
}

function foody_show_ingredients_callback() {
	$options = get_option( 'foody_show_ingredients_conversion', false );
	$checked = $options ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" id="foody_show_ingredients_conversion" name="foody_show_ingredients_conversion">';
}


function foody_show_tag_manager_callback() {
	$options = get_option( 'foody_google_tag_manager_id', false );
	echo '<input value="' . $options . '"type="text" id="foody_google_tag_manager_id" name="foody_google_tag_manager_id">';
}

// Show Google AdX feature
function foody_show_google_adx_callback() {
	$options = get_option( 'foody_show_google_adx', false );
	$checked = $options ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" id="foody_show_google_adx" name="foody_show_google_adx">';
}

function foody_google_adx_script_callback() {
	$content = get_option( 'foody_google_adx_script', '' );
	echo '<textarea id="foody_google_adx_script" name="foody_google_adx_script" rows="5" cols="50">' . $content . '</textarea>';
}

function foody_show_newsletter_popup_callback() {
    $options = get_option( 'foody_show_newsletter_popup', false );
    $checked = $options ? 'checked' : '';
    echo '<input ' . $checked . ' type="checkbox" id="foody_show_newsletter_popup" name="foody_show_newsletter_popup">';
}

function foody_id_for_newsletter_callback(){
    $options = get_option( 'foody_id_for_newsletter', false );
    echo '<input value="' . $options . '"type="text" id="foody_id_for_newsletter" name="foody_id_for_newsletter">';
}

function foody_show_post_views_callback() {
	$options = get_option( 'foody_show_post_views', true );
	$checked = $options ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" id="foody_show_post_views" name="foody_show_post_views">';
}

function foody_show_followers_count_callback() {
	$options = get_option( 'foody_show_followers_count_views', true );
	$checked = $options ? 'checked' : '';
	echo '<input ' . $checked . ' type="checkbox" id="foody_show_followers_count_views" name="foody_show_followers_count_views">';
	echo '<p class="description">הצג/הסר כמות עוקבים אחר ערוצים/מתחמי פידים/יוצרים</p>';
}

//add_filter('manage_edit-units_columns', 'add_units_columns');
//
//function add_units_column_content($content)
//{
//    $content = 'test';
//    return $content;
//}
//
//add_filter('manage_units_custom_column', 'add_units_column_content');
