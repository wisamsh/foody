<?php
/**
 * Foody Theme Customizer
 *
 * @package Foody
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function foody_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'foody_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'foody_customize_partial_blogdescription',
		) );
	}

	// Foody alternative logo link
	$wp_customize->add_setting(
		'foody_logo_link',
		array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'foody_sanitize_url'
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
		$wp_customize,
		'foody_logo_link',
		array(
			'label'       => __( 'קישור לוגו', 'foody' ),
			'description' => __( 'קישור חלופי ללוגו', 'foody' ),
			'settings'    => 'foody_logo_link',
			'priority'    => 10,
			'section'     => 'title_tagline',
			'type'        => 'url',
			'input_attrs' => array(
				'placeholder' => get_home_url(),
			)
		)
	) );

	//	Remove default color pickers
	$wp_customize->remove_control('background_color');
	$wp_customize->remove_control('header_textcolor');

	// Add color picker - titles
	$wp_customize->add_setting( 'foody_title_color' , array(
		'transport'   => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'foody_title_color', array(
		'label'      => __( 'כותרות ראשיות', 'foody' ),
		'section'    => 'colors',
		'settings'   => 'foody_title_color',
	) ) );

	// Add color picker - subtitles
	$wp_customize->add_setting( 'foody_subtitle_color' , array(
		'transport'   => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'foody_subtitle_color', array(
		'label'      => __( 'כותרות משניות', 'foody' ),
		'section'    => 'colors',
		'settings'   => 'foody_subtitle_color',
	) ) );

	// Add color picker - texts
	$wp_customize->add_setting( 'foody_text_color' , array(
		'transport'   => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'foody_text_color', array(
		'label'      => __( 'טקסט רץ', 'foody' ),
		'section'    => 'colors',
		'settings'   => 'foody_text_color',
	) ) );

	// Add color picker - links
	$wp_customize->add_setting( 'foody_links_color' , array(
		'transport'   => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'foody_links_color', array(
		'label'      => __( 'טקסט קישור', 'foody' ),
		'section'    => 'colors',
		'settings'   => 'foody_links_color',
	) ) );

	// Add color picker - hover links
	$wp_customize->add_setting( 'foody_links_hover_color' , array(
		'transport'   => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'foody_links_hover_color', array(
		'label'      => __( 'טקסט קישור בריחוף', 'foody' ),
		'section'    => 'colors',
		'settings'   => 'foody_links_hover_color',
	) ) );
}

add_action( 'customize_register', 'foody_customize_register' );

function foody_sanitize_url( $url ) {
	return esc_url_raw( $url );
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function foody_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function foody_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function foody_customize_partial_logo_link() {
	bloginfo( 'logo_link' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function foody_customize_preview_js() {
	wp_enqueue_script( 'foody-customizer', get_template_directory_uri() . '/resources/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}

add_action( 'customize_preview_init', 'foody_customize_preview_js' );
