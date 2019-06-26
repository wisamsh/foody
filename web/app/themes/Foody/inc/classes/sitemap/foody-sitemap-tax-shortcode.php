<?php
/*
 *      Class for the [foody-simple-sitemap-tax] shortcode
*/

class WPGO_Foody_Sitemap_Tax_Shortcode {


	 /* Main class constructor. */
	public function __construct() {

		require_once( 'foody_shortcodes_utility.php' );
		add_shortcode( 'foody-simple-sitemap-tax', array( &$this, 'render_sitemap_taxonomy' ) );
	}

	/* Shortcode function. */
	public function render_sitemap_taxonomy($attr) {

		/* Get attributes from the shortcode. */
		$args = shortcode_atts( array(
			'include' => '',
			'exclude' => '',
			'depth' => '0',
			'child_of' => '0',
			'title_li' => '',
			'nofollow' => 0,
			'show_count' => '0',
			'orderby' => 'name',
			'order' => 'ASC',
			'taxonomy' => 'category',
			'hide_empty' => '0',
			'echo' => '0'
		), $attr );

		// convert comma separated strings into array (array required in post query)
		$args['include'] = empty( $args['include'] ) ? array() : array_map( 'trim', explode( ',', $args['include']) );
		$args['exclude'] = empty( $args['exclude'] ) ? array() : array_map( 'trim', explode( ',', $args['exclude']) );
		$args['echo'] = 0; // don't echo out output immediately as we need to optionally add a nofollow attribute

		// Enqueue shortcode scripts
		WPGO_Foody_Shortcode_Utility::enqueue_shortcode_scripts($args, 'foody-simple-sitemap-tax');

		$terms = wp_list_categories( array(
			'hide_empty' => $args['hide_empty'],
			'orderby' => $args['orderby'],
			'order' => $args['order'],
			'show_count' => $args['show_count'],
			'title_li' => '',
			'child_of' => $args['child_of'],
			'depth' => $args['depth'],
			'include' => $args['include'],
			'exclude' => $args['exclude'],
			'taxonomy' => $args['taxonomy'],
			'echo' => $args['echo']
		) );
		return "<ul>" . $terms . "</ul>";
	}

}