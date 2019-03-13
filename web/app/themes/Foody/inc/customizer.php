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

//    $wp_customize->add_setting( 'header_link' , array(
//        'default'   => '',
//        'transport' => 'refresh',
//    ) );

//    $wp_customize->add_section( 'foody_cover_link' , array(
//        'title'      => __('לינק קאבר','foody'),
//        'priority'   => 30,
//    ) );
}
add_action( 'customize_register', 'foody_customize_register' );

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
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function foody_customize_preview_js() {
	wp_enqueue_script( 'foody-customizer', get_template_directory_uri() . '/resources/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'foody_customize_preview_js' );
