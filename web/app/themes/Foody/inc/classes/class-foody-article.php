<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:32 AM
 */
class Foody_Article extends Foody_Post implements Foody_ContentWithSidebar {


    private function pid($request_pid = null)
    {
        return $pid =  get_the_ID();
    }


    public function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }


public function Get_Recipes_Title(){
    $tmagic_title = get_field("items_title" ,$this->pid());
    return $tmagic_title;
}
public function Go_Recipes_For_Posts(){
   $recipies_array = get_field("items_recipe", $this->pid());
  echo '<div class="container"><div  class="row text-center">';
   foreach($recipies_array as $p){
    $post_title = get_the_title($p);
    $thumb = get_the_post_thumbnail_url($p);
    echo '<div style="margin-bottom:15px;" class="col-6  col-md-4 col-lg-4 text-center">';
    echo '<a href="/?p='.$p.'" target="_blank"><img src="'.$thumb.'"/>';
    echo '<b><span style="font-size:18px;color:#000 !important;">'.$post_title.'</span></b></a>';
    echo '</div>';
   }
   echo '</div></div>';
}


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