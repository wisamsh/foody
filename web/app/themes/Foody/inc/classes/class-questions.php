<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:32 PM
 */
class Foody_Questions extends Foody_Post {

	private $foody_search;

	/**
	 * Foody_Technique constructor.
	 *
	 * @param $technique_post_id
	 */
	public function __construct( $post ) {
		parent::__construct( $post );

		$this->foody_search = new Foody_Search( 'questions' );
	}

	public function feed() {
		$foody_query = Foody_Query::get_instance();

		$args = $foody_query->get_query( 'questions', [ $this->id ], true );

		$query = new WP_Query( $args );

		$posts = $query->get_posts();

		$posts = array_map( 'Foody_Post::create', $posts );

		$grid_args = [
			'id'     => 'questions-feed',
			'posts'  => $posts,
			'more'   => $foody_query->has_more_posts( $query ),
			'cols'   => 2,
			'header' => [
				'sort' => true
			]
		];

		echo '<div class="container-fluid feed-container technique-feed-container">';
		foody_get_template_part( get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args );
		echo '</div>';
	}

	public function the_featured_content($shortcode = false) {

	}

	public function the_sidebar_content( $args = array() ) {
		dynamic_sidebar( 'foody-sidebar' );
		dynamic_sidebar( 'foody-social' );
	}

	public function the_details() {
		echo '<section class="technique-details-container">';
		bootstrap_breadcrumb();
		the_title( '<h1 class="title">', '</h1>' );
		echo '</section>';
	}

	function __clone() {

	}

}