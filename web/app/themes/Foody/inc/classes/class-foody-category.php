<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/2/18
 * Time: 3:34 PM
 */
class Foody_Category extends Foody_Term implements Foody_ContentWithSidebar {

	/**
	 * Foody_Category constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id ) {
		parent::__construct( $id );
	}


	/**
	 * Get the category image (ACF Field)
	 *
	 * @param string $size
	 *
	 * @return mixed|null|string
	 */
	public function get_image( $size = 'list-item' ) {
		$image = '';
		if ( $this->term != null ) {
			$image = get_field( 'image', $this->term->taxonomy . '_' . $this->term->term_id );


			if ( is_array( $image ) ) {
				if ( isset( $image['sizes'][ $size ] ) ) {
					$image = $image['sizes'][ $size ];
				} else {
					$image = $image['sizes'][ array_keys( $image['sizes'] )[0] ];
				}
			}
		}

		return $image;
	}


	public function get_mobile_image() {
		$mobile_image = get_field( 'homepage_image', $this->term->taxonomy . '_' . $this->term->term_id );
		if ( ! $mobile_image ) {
			$mobile_image = $this->get_image();
		}

		return $mobile_image;
	}


	/**
	 * Get array of sub categories
	 * as Foody_Category objects
	 * @return Foody_Category[]
	 */
	public function get_sub_categories() {
		$parent_id      = get_queried_object_id();
		$wp_categories  = get_categories( [ 'parent' => $parent_id, 'hide_empty' => false ] );
		$sub_categories = [];
		if ( is_array( $wp_categories ) ) {
			$sub_categories = array_map( function ( $category ) {
				return new Foody_Category( $category->term_id );
			}, $wp_categories );
		}

		return $sub_categories;
	}

	public function has_sub_categories() {
		$parent_id     = get_queried_object_id();
		$wp_categories = get_categories( [ 'parent' => $parent_id, 'hide_empty' => false ] );

		return ! empty( $wp_categories );
	}


	/**
	 * Get the category tree.
	 * Returns parents categories in
	 * desc order.
	 * @return array
	 */
	function get_tree() {

		$current = get_term( $this->id, 'category' );

		$tree = [];

		while ( $current->parent ) {
			$tree[]  = $current->term_id;
			$current = get_term( $current->parent, 'category' );
		}
		$tree[] = $current->term_id;
		$tree   = array_reverse( $tree );

		return $tree;
	}


	// === Foody_ContentWithSidebar === //
	function the_details() {
		bootstrap_breadcrumb();
		if ( ! empty( $this->get_sub_categories() ) ) {
			foody_get_template_part( get_template_directory() . '/template-parts/content-categories-slider.php', [
				'category' => $this
			] );
		}
	}

	function the_content( $page ) {
		parent::the_content( $page );
	}

	public function before_content() {
		$cover_image = get_field( 'cover_image', $this->term->taxonomy . '_' . $this->term->term_id );
		if ( ! empty( $cover_image ) ) {
		    $cover_link = get_field( 'cover_link', $this->term->taxonomy . '_' . $this->term->term_id );
		    if(! empty( $cover_link )){
                $cover_link = ['url' => $cover_link, 'target' => '_blank'];
                if(is_array($cover_image)) {
                    $cover_image = array_merge($cover_image , ['link' => $cover_link]);
                    }
            }
			foody_get_template_part( get_template_directory() . '/template-parts/content-cover-image.php', $cover_image );
		}
	}


	// === Foody_Term === //


	/**
	 * Extending classes should implement the
	 * relevant query function in @return string
	 * @see Foody_Query
	 * and return here the name of said function.
	 *
	 */
	protected function get_foody_query_handler() {
		return 'category';
	}

	/**
	 * Arguments will be merged with the default args (@return array arguments to use in grid rendering.
	 * @see Foody_Term::feed()).
	 * Arguments will override default args.
	 * Note: returned arguments must contain the 'id' and 'header['title']'
	 * keys.
	 */
	protected function get_grid_args() {
		return [
			'id'       => 'category-feed',
			'header'   => [
				'title' => $this->title
			],
			'title_el' => $this->has_sub_categories() ? 'h2' : 'h1'
		];
	}

    public function the_css()
    {
        if (isset($_GET) && isset($_GET['referer']) && $_GET['referer']) {
            $titles_color = get_field('titles_color', $_GET['referer']);
            if (!empty($titles_color)) {

                if (preg_match('/^#/', $titles_color) === false) {
                    $titles_color = "#$titles_color";
                }

                ?>
                <style id="feed-style">
                    .title {
                        color: <?php echo $titles_color?>;
                    }

                    a:hover {
                        color: <?php echo $titles_color ?>;
                    }

                    .block-see-more a {
                        color: <?php echo $titles_color ?>;
                    }
                </style>
                <?php
            }
        }
    }
}