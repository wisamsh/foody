<?php
/*
 *      Class for uility
*/

class WPGO_Foody_Shortcode_Utility {

	/* Class properties. */
	protected static $_thumb_size = 22; // pixels

	// Enqueue scripts only when shortcode used on a page.
	// Some scripts are also conditional on certain attribute values too.
	public static function enqueue_shortcode_scripts($args, $shortcode) {

		// only enqueue these scripts if shortode tabs are displayed
		// @todo this conditional should probably should be further refined to include only shortcodes the render attribute is supported on
		if( isset($args['render']) && $args['render'] === 'tab') {
			wp_enqueue_script( 'wpgo-simple-sitemap-pro-js' );
			wp_enqueue_style( 'wpgo-simple-sitemap-pro-jquery-ui-css' );
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script( 'jquery-ui-tabs' );
		}

		// Always enqueue main plugin CSS if and sitemap shortcode used on a page
		wp_enqueue_style( 'wpgo-simple-sitemap-pro-css' );

		if( isset($args['horizontal']) && $args['horizontal'] === 'true' ) {
			self::add_separator_script($args['horizontal_separator']);
		}
	}

	// @todo: replace jQuery code with PHP to remove the last separator before it's rendered
	public static function add_separator_script( $horizontal_separator ) {
		?>
		<script>
			jQuery(document).ready(function($) {
				$('ul.simple-sitemap-page li:last').html(function(index,html){
					var hs = '<?php echo $horizontal_separator; ?>';
					return html.replace(hs, '');
				});
			});
		</script>
		<?php
	}

	public static function get_post_type_label( $show_label, $post_type, $post_type_tag ) {

		$label = '';
		// conditionally show label for each post type
		if( $show_label == 'true' ) {
			$post_type_obj  = get_post_type_object( $post_type );
			$post_type_name = $post_type_obj->labels->name;
			$label = '<' . $post_type_tag . '>' . $post_type_name . '</' . $post_type_tag . '>';
		}

		return $label;
	}

	public static function get_query_args($args, $post_type) {

		// convert comma separated string into array (array required in post query)
		if ( empty( $args['exclude'] ) ) {
			$args['exclude'] = array();
		} else {
			$args['exclude'] = array_map( 'trim', explode( ',', $args['exclude']) );
		}

		if ( empty( $args['include'] ) ) {
			$args['include'] = array();
		} else {
			$args['include'] = array_map( 'trim', explode( ',', $args['include']) );
		}

		$args['tax_query'] = empty( $args['tax_query'] ) ? '' : $args['tax_query'];

		return array(
			'posts_per_page' => -1,
			'post_type' => $post_type,
			'order' => $args['order'],
			'orderby' => $args['orderby'],
			'post__not_in' => $args['exclude'],
			'post__in' => $args['include'],
			'tax_query' => $args['tax_query']
		);
	}

	/**
	 * Render the sitemap list items.
	 *
	 * @since 0.2.0
	 */
	public static function render_list_items($args, $post_type, $query_args) {

		$args['image_size'] = self::$_thumb_size;
		$sitemap_query = new WP_Query( $query_args );

		if ( $sitemap_query->have_posts() ) :

			if( $post_type == 'page') :
				echo self::list_pages( $sitemap_query->posts, $args );
			else :
				$horizontal_sep = "";
				$ul_class = 'simple-sitemap-' . $post_type . ' main';
				if($args['shortcode_type'] == 'normal') { // not group shortcode
					if($args['horizontal'] == 'true') {
						$ul_class .= ' horizontal';
						$args['page_depth'] = 1;
						$horizontal_sep = $args['horizontal_separator'];
					}
				}
				echo '<ul class="' . esc_attr($ul_class) . '">';

				// start of the loop
				while ( $sitemap_query->have_posts() ) : $sitemap_query->the_post();

					// Only display public posts
					if( $args['visibility'] == 'public') {
						if( get_post_status( get_the_ID() ) == 'private' ) {
							continue;
						}
					}

					// check if we're on the last post
					if( ($sitemap_query->current_post + 1) == $sitemap_query->post_count ) {
						$horizontal_sep = "";
					}

					$image_html = '';
					if($args['image'] == "true") {
						$image_html = get_the_post_thumbnail( null, array($args['image_size'], $args['image_size']) );
						$image_html = !empty($image_html) ? '<span class="simple-sitemap-fi">' . $image_html . '</span>' : '';
					}

					// @todo can combine this into one line in the future when minimum PHP version allows using function return value as an argument
					$title_text = get_the_title();
					$title_text = WPGO_Simple_Sitemap_Pro_Hooks::simple_sitemap_pro_title_text($title_text, get_the_ID());

					$permalink = get_permalink();
					$title = self::get_the_title( $title_text, $permalink, $args );
					$title = WPGO_Simple_Sitemap_Pro_Hooks::simple_sitemap_pro_title_link_text($title, get_the_ID());

					$excerpt = $args['show_excerpt'] == 'true' ? '<' . $args['excerpt_tag'] . ' class="excerpt">' . get_the_excerpt() . '</' . $args['excerpt_tag'] . '>' : '';
					$separator_html = ($args['separator'] == "true") ? '<div class="separator"></div>' : '';

					// render list item
					// @todo add this to a template (static method?) so we can reuse it in this and other classes?
					echo '<li>';
					echo $image_html;
					echo $title;
					echo $excerpt;
					echo $separator_html;
					echo $horizontal_sep;
					echo '</li>';

				endwhile; // end of post loop -->

				echo '</ul>';

				echo '</div>'; // .simple-sitemap-wrap

				// put pagination functions here
				wp_reset_postdata();
			endif;

            // Foody - No need for an else case, since we removed opening simple-sitemap-wrap div on group shortcode.
            //else:
            //$post_type_obj  = get_post_type_object( $post_type );
            //$post_type_name = strtolower($post_type_obj->labels->name);
            //echo '<p>Sorry, no ' . $post_type_name . ' found.</p>';
            //echo '</div>'; // .simple-sitemap-wrap

		endif;

	}

	public static function get_the_title( $title_text, $permalink, $args, $parent_page = false, $parent_page_link = '1' ) {

		$links = $args['links'];
		$title_open = $args['title_open'];
		$title_close = $args['title_close'];
		$nofollow = $args['nofollow'];
		if( $nofollow === '1' ) { $nofollow = ' rel="nofollow"'; } else { $nofollow = ''; }

		if( !empty( $title_text ) ) {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url($permalink) . '"' . $nofollow . '>' . wp_kses_post($title_text) . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url($permalink) . '"' . $nofollow . '>' . wp_kses_post($title_text) . '</a>' . $title_close;
			}else {
				$title = $title_open . wp_kses_post($title_text) . $title_close;
			}
		}
		else {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url($permalink) . '"' . $nofollow . '>' . '(no title)' . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url($permalink) . '"' . $nofollow . '>' . '(no title)' . '</a>' . $title_close;
			} else {
				$title = $title_open . '(no title)' . $title_close;
			}
        }

		return $title;
	}

	public static function walk_page_tree( $pages, $depth, $r ) {

		$walker = new WPGO_Walker_Page();
		$walker->ssp_args = $r;

		foreach ( (array) $pages as $page ) {
			if ( $page->post_parent )
				$r['pages_with_children'][ $page->post_parent ] = true;
		}

		$args = array($pages, $depth, $r);
		return call_user_func_array(array($walker, 'walk'), $args);
	}

	public static function list_pages( $pages, $args ) {

		$output = '';

		if ( empty( $pages ) )
			return $output;

		$class =  'simple-sitemap-page main';
		if($args['shortcode_type'] == 'normal') { // not group shortcode
			if($args['horizontal'] == 'true') {
				$class .= ' horizontal';
				//$args['page_depth'] = 1;
			}
		}

		$output = '<ul class="' . $class . '">';
		$output .= self::walk_page_tree( $pages, $args['page_depth'], $args );
		$output .= '</ul>';
		$output .= '</div>'; // .simple-sitemap-wrap

		return $output;
	}
}