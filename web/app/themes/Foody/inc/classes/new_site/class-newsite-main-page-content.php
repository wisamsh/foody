<?php
class MainPageContent
{
    public $pageID;
    public function __construct()
    {
        $this->pageID = get_the_ID();
    }


    public function get_MainBanner()
    {
        if (!wp_is_mobile()) {
            return get_field("ns_hp_fetured_image_desktop");
        } else {
            return get_field("ns_hp_fetured_image_mobile");
        }
    }

    public function Get_Promoted_Recipies()
    {
        $posts_IDS =  get_field("ns_mp_featured_post");
        $post_details = [];
        foreach ($posts_IDS as $key => $post_id) {
            $author_id = get_post_field('post_author', $post_id);
            $post_details[$post_id] =  array(
                "id" => $post_id,
                "title" => get_the_title($post_id),
                "expert" => get_the_excerpt($post_id),
                "featured_image" => get_the_post_thumbnail_url($post_id, 'full'),
                "author_name" => get_the_author_meta('display_name', $author_id),
                "author_avatar" => get_avatar_url($author_id, ['size' => 96]),
                "author_url" => get_author_posts_url($author_id)
            );
        }
        return $post_details;
    }

    public function fp_main_recipies($args)
    {
        $rtn = '<div class="mp_recipe_cont container mt-4 p-0"><div class="row">';
        if ($args) {
            foreach ($args as $k => $rec) {
                $get_permalink = get_permalink($rec['id']);
                $rtn .= '<a href="' . esc_url($get_permalink) . '"><div class="col-md-6 mb-6 lg-6">
            <div class="inner_card">
                    <img src="' . $rec['featured_image'] . '" class=" lazy recipe_front_images" alt="Image 1">
                    <div class="card-body">
                        <h5 class="card-title">' . $rec['title'] . '</h5>
                        <div class="mp_rec_author">
                        <img src="' . $rec['author_avatar'] . '"/>
                    </div>
                    <div class="expert_text">' . $rec['expert'] . '</div>
                    <div class="authname">' . $rec['author_name'] . '</div>
                    
                    </div>
                    </div>
                    </a>
            </div>';
            }
        }
        $rtn .= '</div></div>'; //end container
        return $rtn;
    } //end fp_main_recipies()-------------------------------------


    public function mp_Hotspots()
    {
        $hotspots =  get_field("ns_mp_hot_spots");
        $html = '<div class="container"><div class="row">';
        foreach ($hotspots as $hs) {
            if ($hs['ns_mp_hotspot_main_title']) {
        $html .='<div class="col-12"><h1 class="ht_h1">'.$hs['ns_mp_hotspot_main_title'].'</h1></div></div>';
            if(!empty($hs['ns_mp_newhotspot'])){
$html .='<div class="row">';
                foreach($hs['ns_mp_newhotspot'] as $hsfields){

                   
                    $html .='<div class="zoomcont col col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                    $html .='<a  href="' . $hsfields['ns_mp_hotspot_url'] . '">';
                    $html .='<img class="ht_img lazy" src="'.$hsfields['ns_mp_hs_image'].'"/></a>';
                    $html .='<p class="ht_ttl">' .$hsfields['ns_mp_hotspot_title'] . '</p>' ;
                    $html .='</div>';

                }
                $html .='</div>';
            }

        }
        $html .='</div>'; //container and col closer
        }
        return $html;
    }
} //END CLASS
