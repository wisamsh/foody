<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:32 AM
 */
class Foody_Article extends Foody_Post implements Foody_ContentWithSidebar
{


    private function pid($request_pid = null)
    {
        return $pid = get_the_ID();
    }


    public function encodeURIComponent($str)
    {
        $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
        return strtr(rawurlencode($str), $revert);
    }


    public function Get_Content_Before_Items()
    {
        $rtn = '';
        $before_items_content = trim(get_field("before_items_content", $this->pid())) != '' ? get_field("before_items_content", $this->pid()) : '';
        if (trim($before_items_content) != "") {
            $rtn .= '<div style="width:100%; text-align:right;direction:rtl;margin-top:10px;">' . $before_items_content . '</div>';
        }
        return $rtn;
    }
    public function Get_Recipes_Title()
    {

        $tmagic_title = !empty(get_field("items_title", $this->pid())) ? get_field("items_title", $this->pid()) : '';
        return $tmagic_title;
    }
    public function Go_Recipes_For_Posts()
    {
        $rtn = '';
        if (!empty(get_field("items_recipe", $this->pid()))) {
            $recipies_array = get_field("items_recipe", $this->pid());

            $rtn .= '<div class="container"><div class="row text-center">';
            foreach ($recipies_array as $p) {
                $post_title = get_the_title($p);
                $thumb = get_the_post_thumbnail_url($p);
                $rtn .= '<div style="margin-bottom:15px;" class="col-6 col-md-4 col-lg-4 text-center">';
                $rtn .= '<a href="/?p=' . $p . '" target="_blank"><img src="' . $thumb . '"/>';
                $rtn .= '<b><span style="font-size:18px;color:#000 !important;">' . $post_title . '</span></b></a>';
                $rtn .= '</div>';
            }
            $rtn .= '</div></div>';
        }
        return $rtn;
    }

    public function Get_Schedual_Photos()
    {
        $rtn = '';
        $schedual_title = get_field("schedual_title", $this->pid());
        $whatsup_photos = get_field("whatsup_photos", $this->pid());
        if (trim($schedual_title) != "") {
            $rtn .= '<section class="categories section no-print"><h1 class="recipe_title"><b>' . $schedual_title . '</b></h2>';
        }

        if (!empty($whatsup_photos)) {
            $rtn .= '<div class="container m-0 p-0"><div class="row" style="text-align:center;">';
            $i = 1;
            foreach ($whatsup_photos as $p) {

                $whatsup_linkeg = $p['whatsup_linkeg'];
                $email_linkeg = $p['email_linkeg'];
                $photo = !wp_is_mobile() ? $p['what_desktop_photo'] : $p['what_mobile_photo'];
                $items_content_for_recipe = $p['items_content_for_recipe'];
                $rtn .= '<div class="col-12" style="text-align:center;"><img id="schedimage_' . $i . '" src="' . $photo . '" data-imgsrc="' . $photo . '" />';
                $whatsup_linkeg = $p['whatsup_linkeg'];
                $email_linkeg = $p['email_linkeg'];


                if ($whatsup_linkeg != trim('')) {
                    $rtn .= '<span ><a href="/foody-share/?u=' . $photo . '" >' . $whatsup_linkeg . '</a> </span>';
                }


                if ($email_linkeg != trim('')) {
                    $rtn .= '<span><a href="mailto:?&body=<img src=' . $photo . ' />" >' . $email_linkeg . '</a> </span>';
                }


                if (trim($items_content_for_recipe) != '') {
                    $rtn .= '<div class="col-12" style="text-align:right;direction:rtl;">' . $items_content_for_recipe . '</div>';
                }



                $rtn .= '</div>';
                $i++;
            }
            $rtn .= '</div></div></section>';
        }
        return $rtn;
    }





    public function the_featured_content($shortcode = false)
    {
        $this->the_video_box();
    }

    public function the_sidebar_content($args = array())
    {
        parent::the_sidebar_content($args);
    }

    public function the_details()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/_content-recipe-details-old.php',
            [
                'page' => $this,
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
