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


    $wp_customize->add_setting(
        'foody_logo_mode',
        array(
            'capability'        => 'edit_theme_options'
        )
    );

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'foody_logo_mode',
        array(
            'label'       => __( 'תחם לוגו לגבולות ההדר', 'foody' ),
            'description' => __( 'תחם לוגו לגבולות ההדר', 'foody' ),
            'settings'    => 'foody_logo_mode',
            'priority'    => 10,
            'section'     => 'title_tagline',
            'type'        => 'checkbox'
        )
    ) );

    $wp_customize->add_setting(
        'foody_logo_border_radius',
        array(
            'capability'        => 'edit_theme_options'
        )
    );

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'foody_logo_border_radius',
        array(
            'label'       => __( 'עגל גבול לוגו', 'foody' ),
            'description' => __( 'עגל גבול לוגו', 'foody' ),
            'settings'    => 'foody_logo_border_radius',
            'priority'    => 11,
            'section'     => 'title_tagline',
            'type'        => 'checkbox'
        )
    ) );

	//	Remove default color pickers
	$wp_customize->remove_control( 'background_color' );
	$wp_customize->remove_control( 'header_textcolor' );

	// Add color picker - titles
	foody_add_custom_color_picker_setting( $wp_customize, 'foody_title_color', 'כותרות ראשיות' );
	// Add color picker - subtitles
	foody_add_custom_color_picker_setting( $wp_customize, 'foody_subtitle_color', 'כותרות משניות' );
	// Add color picker - texts
	foody_add_custom_color_picker_setting( $wp_customize, 'foody_text_color', 'טקסט רץ' );
	// Add color picker - links
	foody_add_custom_color_picker_setting( $wp_customize, 'foody_links_color', 'טקסט קישור' );
	// Add color picker - hover links
	foody_add_custom_color_picker_setting( $wp_customize, 'foody_links_hover_color', 'טקסט קישור בריחוף' );

	// Add Titles underline
	$wp_customize->add_setting(
		'foody_show_titles_underline',
		array(
			'default'    => 'true',
			'capability' => 'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
		$wp_customize,
		'foody_show_titles_underline',
		array(
			'label'       => __( 'הצג קו תחתון לכותרות', 'foody' ),
			'description' => __( '', 'foody' ),
			'settings'    => 'foody_show_titles_underline',
			'section'     => 'colors',
			'type'        => 'checkbox'
		)
	) );

	// Add color picker - title underline
	foody_add_custom_color_picker_setting( $wp_customize, 'foody_underline_color', 'צבע קו תחתון כותרות' );


	// Add Social Links section
	$wp_customize->add_section( 'foody_social_links', array(
		'title'    => __( 'קישורי רשתות חברות ורכיבי header', 'foody' ),
		'priority' => 31,
	) );

	add_social_link_setting( $wp_customize, 'youtube', 'יוטיוב', 'https://www.youtube.com/channel/UCy_lqFqTpf7HTiv3nNT2SxQ', 1 );
	add_social_link_setting( $wp_customize, 'instagram', 'אינסטגרם', 'https://www.instagram.com/foody_israel', 3 );
	add_social_link_setting( $wp_customize, 'facebook', 'פייסבוק', 'https://www.facebook.com/FoodyIL/', 5 );

    $wp_customize->add_setting(
        'foody_social_text',
        array(
            'default'           => __('עקבו אחרינו','foody'),
            'capability' => 'edit_theme_options',
        )
    );

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'foody_social_text' ,
        array(
            'label'       => __( 'טקסט ליד אייקונים', 'foody' ),
            'description' => __( '', 'foody' ),
            'settings'    => 'foody_social_text',
            'priority'    => 6,
            'section'     => 'foody_social_links',
            'type'        => 'text'
        )
    ) );


	$wp_customize->add_setting(
		'show_foody_collaboration_text',
		array(
			'capability' => 'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
		$wp_customize,
		'show_foody_collaboration_text' ,
		array(
			'label'       => __( 'הצג רכיב בשיתוף עם Foody', 'foody' ),
			'description' => __( '', 'foody' ),
			'settings'    => 'show_foody_collaboration_text',
			'priority'    => 6,
			'section'     => 'foody_social_links',
			'type'        => 'checkbox'
		)
	) );

	$wp_customize->add_setting(
		'foody_collaboration_text',
		array(
			'capability' => 'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
		$wp_customize,
		'foody_collaboration_text' ,
		array(
			'label'       => __( 'טקסט רכיב בשיתוף עם Foody', 'foody' ),
			'description' => __( '', 'foody' ),
			'settings'    => 'foody_collaboration_text',
			'priority'    => 6,
			'section'     => 'foody_social_links',
			'type'        => 'text'
		)
	) );

    // Add texts section
    $wp_customize->add_section( 'foody_texts', array(
        'title'    => __( 'טקסטים', 'foody' ),
        'priority' => 32,
    ) );

    foody_customize_add_text($wp_customize,'search_placeholder','פלייסהולדר חיפוש',__('חפשו מתכון או כתבה…','foody'),1);



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


function foody_add_custom_color_picker_setting( $wp_customize, $id, $label_text ) {
	// Add color picker - titles
	$wp_customize->add_setting( $id, array(
		'transport' => 'refresh',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
		'label'    => __( $label_text, 'foody' ),
		'section'  => 'colors',
		'settings' => $id,
	) ) );
}

function add_social_link_setting( $wp_customize, $id, $label_text, $default_value, $weight ) {

	// Custom Show Youtube
	$wp_customize->add_setting(
		'foody_show_social_' . $id,
		array(
			'default'    => 'true',
			'capability' => 'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
		$wp_customize,
		'foody_show_social_' . $id,
		array(
			'label'       => __( 'הצג קישור ל-' . $label_text, 'foody' ),
			'description' => __( '', 'foody' ),
			'settings'    => 'foody_show_social_' . $id,
			'priority'    => $weight,
			'section'     => 'foody_social_links',
			'type'        => 'checkbox'
		)
	) );

	// Custom Youtube Link
	$wp_customize->add_setting(
		'foody_social_' . $id,
		array(
			'default'           => $default_value,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'foody_sanitize_url'
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
		$wp_customize,
		'foody_social_' . $id,
		array(
			'label'       => __( 'חשבון ' . $label_text, 'foody' ),
			'description' => __( '', 'foody' ),
			'settings'    => 'foody_social_' . $id,
			'priority'    => ( $weight + 1 ),
			'section'     => 'foody_social_links',
			'type'        => 'url'
		)
	) );
}


/**
 * @param $wp_customize
 * @param $id
 * @param $label_text
 * @param $default_value
 * @param $weight
 */
function foody_customize_add_text($wp_customize, $id, $label_text, $default_value, $weight){
    $wp_customize->add_setting(
        'foody_text_' . $id,
        array(
            'default'           => $default_value,
            'capability' => 'edit_theme_options',
        )
    );

    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'foody_text_' . $id,
        array(
            'label'       => __( $label_text, 'foody' ),
            'description' => __( '', 'foody' ),
            'settings'    =>  'foody_text_' . $id,
            'priority'    => $weight,
            'section'     => 'foody_texts',
            'type'        => 'text'
        )
    ) );
}