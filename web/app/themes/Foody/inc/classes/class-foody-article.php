<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:32 AM
 */
class Foody_Article extends Foody_Post implements Foody_ContentWithSidebar {

	public function the_featured_content($shortcode = false) {
		$this->the_video_box();
	}

	public function the_sidebar_content( $args = array() ) {
		parent::the_sidebar_content( $args );
	}

	public function the_details() {
		foody_get_template_part(
			get_template_directory() . '/template-parts/_content-recipe-details-old.php',
			[
				'page'          => $this,
				'show_favorite' => false
			]
		);
	}

    public function before_content()
    {
        $cover_image = get_field('cover_image');
        $mobile_image = get_field('mobile_cover_image');
        $feed_area_id = get_field('recipe_channel');

        if (isset($_GET['referer']) || $feed_area_id) {
            $referer_post = isset($_GET['referer']) ? $_GET['referer'] : $feed_area_id;
            if (!empty($referer_post)) {
                $cover_image = get_field('cover_image', $referer_post);
                $mobile_image = get_field('mobile_cover_image', $referer_post);
            }
        }

        if (!empty($cover_image)) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-cover-image.php', [
                'image' => $cover_image,
                'mobile_image' => $mobile_image
            ]);
        }
    }
}