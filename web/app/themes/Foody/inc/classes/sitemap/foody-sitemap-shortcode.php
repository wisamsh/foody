<?php
/*
 *      Class for the [simple-sitemap] shortcode
*/

class WPGO_Foody_Sitemap_Shortcode {


	 /* Main class constructor. */
	public function __construct() {

		require_once( 'foody_shortcodes_utility.php' );
		add_shortcode( 'foody-simple-sitemap', array( &$this, 'render_sitemap' ) );
	}

	public function render_sitemap($attr) {

		/* Get attributes from the shortcode. */
		$args = shortcode_atts( array(
			'types' => 'page',
			'show_label' => 'true',
			'show_excerpt' => 'false',
			'container_tag' => 'ul',
			'title_tag' => '',
			'post_type_tag' => 'h3',
			'excerpt_tag' => 'div',
			'links' => 'true',
			'page_depth' => 0,
			'order' => 'asc',
			'orderby' => 'title',
			'exclude' => '',
			'include' => '',
			'nofollow' => 0,
			'visibility' => 'all',
			'render' => '',
			'list_icon' => 'true',
			'separator' => 'false',
			'horizontal' => 'false', // great for adding to footer
			'horizontal_separator' => ', ',
			'image' => 'false',
			'shortcode_type' => 'normal',
			'parent_page_link' => '1', // is this used anymore, or only plugin settings version?
			'id' => '1' // if using multiple sitemaps with tabs then set this to a unique id for each sitemap to avoid CSS id conflicts
		), $attr );

		// escape tag names
		$args['container_tag'] = tag_escape( $args['container_tag'] );
		$args['title_tag'] = tag_escape( $args['title_tag'] );
		$args['excerpt_tag'] = tag_escape( $args['excerpt_tag'] );
		$args['post_type_tag'] = tag_escape( $args['post_type_tag'] );

		// force 'ul' or 'ol' to be used as the container tag
		$allowed_container_tags = array('ul', 'ol');
		if(!in_array($args['container_tag'], $allowed_container_tags)) {
			$args['container_tag'] = 'ul';
		}

		// validate numeric values
		$args['id'] = intval( $args['id'] );
		$args['page_depth'] = intval( $args['page_depth'] );

		$container_format_class = ($args['list_icon'] == "true") ? '' : ' hide-icon';
		$render_class = empty($args['render']) ? '' : ' ' . sanitize_html_class( $args['render'] );

		// Enqueue shortcode scripts
		WPGO_Foody_Shortcode_Utility::enqueue_shortcode_scripts($args, 'foody-simple-sitemap');

		// ******************
		// ** OUTPUT START **
		// ******************

		// Start output caching (so that existing content in the [foody-simple-sitemap] post doesn't get shoved to the bottom of the post
		ob_start();

		$post_types = array_map( 'trim', explode( ',', $args['types'] ) ); // convert comma separated string to array
		$registered_post_types = get_post_types();

		//echo "<pre>";
		//print_r($post_types);
		//print_r($sitemap_query->posts);
		//echo "</pre>";

		$container_classes = 'simple-sitemap-container' . $render_class . $container_format_class;
		echo '<div class="' . esc_attr($container_classes) . '">';

		// conditionally output tab headers
		if( $args['render'] == 'tab' ):

			echo '<ul>'; // tab header open

			// create tab headers
			$header_tab_index = 1; // initialize to 1
			foreach( $post_types as $post_type ) {

				if( !array_key_exists( $post_type, $registered_post_types ) )
					break; // bail if post type isn't valid

				$post_type_label = WPGO_Foody_Shortcode_Utility::get_post_type_label($args['show_label'], $post_type, $args['post_type_tag']);
				echo '<li><a href="#simple-sitemap-tab-' . $header_tab_index . '-' . $args['id'] . '">' .  $post_type_label . '</a></li>';

				$header_tab_index++;
			}

			echo '</ul>'; // tab header close

		endif;

		// conditionally create tab panels
		$header_tab_index = 1; // reset to 1
		foreach( $post_types as $post_type ) :

			if( !array_key_exists( $post_type, $registered_post_types ) )
				break; // bail if post type isn't valid

			// set opening and closing title tag
			if( !empty($args['title_tag']) ) {
				$args['title_open'] = '<' . $args['title_tag'] . '>';
				$args['title_close'] = '</' . $args['title_tag'] . '>';
			}
			else {
				$args['title_open'] = $args['title_close'] = '';
			}

			$post_type_label = WPGO_Foody_Shortcode_Utility::get_post_type_label($args['show_label'], $post_type, $args['post_type_tag']);

			$list_item_wrapper_class = 'simple-sitemap-wrap' . $render_class;
			$tab_id = ($args['render'] == 'tab') ? ' id="simple-sitemap-tab-' . $header_tab_index . '-' . $args['id'] . '"' : '';
			$header_tab_index++;

			$query_args = WPGO_Foody_Shortcode_Utility::get_query_args($args, $post_type);
			$sitemap_query = new WP_Query($query_args);

			if ($sitemap_query->have_posts()) {
				echo '<div ' . $tab_id . 'class="' . esc_attr($list_item_wrapper_class) . '">';
			}
			if( $args['render'] != 'tab' ) echo $post_type_label;

			WPGO_Foody_Shortcode_Utility::render_list_items($args, $post_type, $query_args);

		endforeach;

		echo '</div>'; // .simple-sitemap-container

		// @todo check we still need this
		echo '<br style="clear: both;">'; // make sure content after the sitemap is rendered properly if taken out

		$sitemap = ob_get_contents();
		ob_end_clean();

		// ****************
		// ** OUTPUT END **
		// ****************

		return $sitemap;
	}
}