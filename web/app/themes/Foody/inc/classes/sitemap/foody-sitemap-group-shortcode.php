<?php
/*
 *      Class for the [foody-simple-sitemap-group] shortcode
*/

class WPGO_Foody_Sitemap_Group_Shortcode {

	 /* Main class constructor. */
	public function __construct() {

		require_once( 'foody_shortcodes_utility.php' );
		add_shortcode( 'foody-simple-sitemap-group', array( &$this, 'render_sitemap_group' ) );
	}

	/* Shortcode function. */
	public function render_sitemap_group($attr) {

		/* Get attributes from the shortcode. */
		$args = shortcode_atts( array(
			'id' => '1', // if using multiple sitemaps with tabs then set this to a unique id for each sitemap to avoid CSS id conflicts
			'page_depth' => 0,
			'type' => 'post',
			'tax' => 'category', // single taxonomy
			'container_tag' => 'ul',
			'title_tag' => '',
			'excerpt_tag' => 'div',
			'post_type_tag' => 'h3',
			'term_tag' => 'h3',
			'term_order' => 'asc',
			'term_orderby' => 'name',
			'order' => 'asc',
			'orderby' => 'title',
			'exclude' => '',
			'include_terms' => array(),
			'exclude_terms' => array(),
			'render' => '',
			'show_label' => 'true',
			'links' => 'true',
			'list_icon' => 'true',
			'show_excerpt' => 'false',
			'separator' => 'false',
			'horizontal' => 'false',
			'horizontal_separator' => ', ',
			'image' => 'false',
			'shortcode_type' => 'group',
			'nofollow' => 0,
			'parent_page_link' => '1', // is this used anymore, or only plugin settings version?
			'visibility' => 'all'
		), $attr );

		$args['types'] = 'post'; // just in case a user mistakenly tries to use 'types' attribute

		// escape tag names
		$args['container_tag'] = tag_escape( $args['container_tag'] );
		$args['title_tag'] = tag_escape( $args['title_tag'] );
		$args['excerpt_tag'] = tag_escape( $args['excerpt_tag'] );
		$args['post_type_tag'] = tag_escape( $args['post_type_tag'] );
		$args['term_tag'] = tag_escape( $args['term_tag'] );

		// force 'ul' or 'ol' to be used as the container tag
		$allowed_container_tags = array('ul', 'ol');
		if(!in_array($args['container_tag'], $allowed_container_tags)) {
			$args['container_tag'] = 'ul';
		}

		// validate numeric values
		$args['id'] = intval( $args['id'] );

		$container_format_class = ($args['list_icon'] == "true") ? '' : ' hide-icon';
		$render_class = empty($args['render']) ? '' : ' ' . sanitize_html_class( $args['render'] );

		// check post type is valid
		$registered_post_types = get_post_types();
		if( !array_key_exists( $args['type'], $registered_post_types ) )
			return;

		// Enqueue shortcode scripts
		WPGO_Foody_Shortcode_Utility::enqueue_shortcode_scripts($args, 'foody-simple-sitemap-group');

		// ******************
		// ** OUTPUT START **
		// ******************

		// Start output caching (so that existing content in the [simple-sitemap] post doesn't get shoved to the bottom of the post
		ob_start();

		$container_classes = 'simple-sitemap-container' . $render_class . $container_format_class;
		echo '<div class="' . esc_attr($container_classes) . '">';

		// set opening and closing title tag
		if( !empty($args['title_tag']) ) {
			$args['title_open'] = '<' . $args['title_tag'] . '>';
			$args['title_close'] = '</' . $args['title_tag'] . '>';
		}
		else {
			$args['title_open'] = $args['title_close'] = '';
		}

		$post_type_label = WPGO_Foody_Shortcode_Utility::get_post_type_label($args['show_label'], $args['type'], $args['post_type_tag']);

		$list_item_wrapper_class = 'simple-sitemap-wrap' . $render_class;
		//if( $args['render'] != 'tab' ) echo $post_type_label;
		echo $post_type_label; // remove this line if the one above is reinstated

		$taxonomy_arr = get_object_taxonomies( $args['type'] );

		// echo "<pre>";
		// echo "Calling: get_object_taxonomies('page')<br>";
		// echo "Result: ";
		// print_r($taxonomy_arr);
		// print_r($sitemap_query->posts);
		// echo "</pre>";

		// sort via specified taxonomy
		if ( !empty($args['tax']) && in_array( $args['tax'], $taxonomy_arr ) ) {

			$term_attr = array(
				'orderby'           => $args['term_orderby'],
				'order'             => $args['term_order']
			);

			// get array of taxonomy terms to include/exclude
			$exclude_terms = !empty($args['exclude_terms'])
				? array_map( array( &$this, 'process_csl' ), explode( ',', $args['exclude_terms'] ) )
				: $args['exclude_terms'];

			$include_terms = !empty($args['include_terms'])
				? array_map( array( &$this, 'process_csl' ), explode( ',', $args['include_terms'] ) )
				: $args['include_terms'];

			//echo "<pre>";
			//print_r($include_terms);
			//print_r($exclude_terms);
			//echo "</pre>";

			$terms = get_terms( $args['tax'], $term_attr );
			foreach ( $terms as $term ) {

				//echo "<pre>";
				//print_r($term);
				//print_r($exclude_terms);
				//echo "</pre>";
				//echo "[" . empty($exclude_terms) . "]";

				$tmp = strtolower($term->name);

				if( !empty($include_terms) && !in_array($tmp, $include_terms) ) {
					continue; // skip to next loop iteration if the current term is not in list of terms to be included
				}

				if( !empty($exclude_terms) && in_array($tmp, $exclude_terms) ) {
					continue; // skip to next loop iteration if the current term is to be excluded
				}


				$args['tax_query'] = array(
					array(
						'taxonomy' => $args['tax'],
						'field' => 'slug',
						'terms' => $term ),
				);


				$query_args = WPGO_Foody_Shortcode_Utility::get_query_args($args, $args['type']);

				$sitemap_query = new WP_Query($query_args);

				if ($sitemap_query->have_posts()) {
					echo '<div class="' . esc_attr($list_item_wrapper_class) . ' ' . esc_attr(strtolower($term->name)) . '">';
					echo '<' . $args['term_tag'] . '>' . $term->name . '</' . $args['term_tag'] . '>';
				}

				WPGO_Foody_Shortcode_Utility::render_list_items($args, $args['type'], $query_args);
				// Foody - No need for an else case, since we removed opening simple-sitemap-wrap div on group shortcode.
				// else:
				// $post_type_obj  = get_post_type_object( $args['type'] );
				// $post_type_name = strtolower($post_type_obj->labels->name);
				// echo '<p>Sorry, no ' . $post_type_name . ' found.</p>';
				// echo '</div>'; // .simple-sitemap-wrap
				// endif;
			}
		}
		else {
			echo "No posts found.";
		}

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

	// process comma separated list of arguments for a shortcode attribute
	public function process_csl($item) {
		$item = trim($item);
		$item = strtolower($item);
		return $item;
	}
}