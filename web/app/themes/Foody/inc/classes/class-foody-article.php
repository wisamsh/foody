<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:32 AM
 */
class Foody_Article extends Foody_Post implements Foody_ContentWithSidebar {

	public function the_featured_content() {
		$this->the_video_box();
	}

	public function the_sidebar_content( $args = array() ) {
		parent::the_sidebar_content( $args );
	}

	public function the_details() {
		foody_get_template_part(
			get_template_directory() . '/template-parts/_content-recipe-details.php',
			[
				'page'          => $this,
				'show_favorite' => false
			]
		);
	}
}