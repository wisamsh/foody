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


public function Article_Style_Emplementor(){
$rtn = '<style>

@media (max-width: 768px){
.social-title{
width: 50px !important;

margin-top: 6px !important;
}
}
.social-title{font-weight: bold !important;}

.area_img{
cursor: pointer;

}

.areas{
margin-bottom:20px;
margin-top:20px;
}
.share{
font-weight: 500;
font-size: 19px;
}
.share a{color:#000 !important;}
';
if(!wp_is_mobile()){
$rtn .='.area_img{width:200px !important;height:170px!important;}';
$rtn .= '.img_holder{
width: 170px;
height: 66px;
display: inline-block;
margin-left: 40px;
background-position: center;
background-repeat: no-repeat;
background-size: cover;
object-fit: fill;
cursor: pointer;
border-radius: 15px;
}



.content_areas{text-align:center;justify-content: center;margin-top:5px;}

';
}
if(wp_is_mobile()){


$rtn .= '.img_holder{
width: 90px;
height: 40px;
display: inline-grid;
margin-left: 10px;
background-position: center;
background-repeat: no-repeat;
background-size: cover;
object-fit: fill;
border-radius: 15px;
   
    }
.content_areas{text-align:center;justify-content: center; margin-top:5px;}


';
}


$rtn .='</style>';
return $rtn;
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

public function Get_Area_Photo_Link(){
$rtn = '';
if( !wp_is_mobile() && trim(get_field("first_link_to_div_photo", $this->pid()))){
$first_link_to_div_photo = get_field("first_link_to_div_photo", $this->pid());
$rtn .= '<input type="hidden" id="area_photo_0" value="'.$first_link_to_div_photo.'" />';

}

if(wp_is_mobile() && trim(get_field("first_link_to_div_photo_mobile", $this->pid()))){
$first_link_to_div_photo = get_field("first_link_to_div_photo_mobile", $this->pid());
$rtn .= '<input type="hidden" id="area_photo_0" value="'.$first_link_to_div_photo.'" />';

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
if(!get_field("title_for_posts", $p)){
$post_title = get_the_title($p);
        }
else{
$post_title = get_field("title_for_posts", $p);
        }
    
    if(get_field("referer_publish", $this->pid()) !=""){
     $r = "/?referer=" . get_field("referer_publish", $this->pid()) ;
    }
    else{
     $r = '';
    }

        
$permalink = get_permalink($p);
$thumb = get_the_post_thumbnail_url($p);
$rtn .= '<div style="margin-bottom:15px;" class="col-6 col-md-4 col-lg-4 text-center">';
$rtn .= '<a class="post_link" href="' . $permalink . $r . '" target="_blank"><img class="post_image" src="' . $thumb . '"/>';
$rtn .= '<b><span style="font-size:15px;color:#000 !important;">' . $post_title . '</span></b></a>';
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
$rtn .= '<div class="container"><div class="row" style="text-align:center;">';
$i = 1;
foreach ($whatsup_photos as $p) {
//$rtn .='<div id="area_'. $i .'">';

if(!wp_is_mobile()){
$rtn .= $p['link_to_div_photo'] != '' ? '<input type="hidden" id="area_photo_'.$i.'" value="'.$p['link_to_div_photo'] .'"/>' : '';
}
if(wp_is_mobile()){
$rtn .= $p['link_to_div_photo_mobile'] != '' ? '<input type="hidden" id="area_photo_'.$i.'" value="'.$p['link_to_div_photo_mobile'] .'"/>' : '';
}
               

$whatsup_linkeg = $p['whatsup_linkeg'];
$email_linkeg = $p['email_linkeg'];
$photo = !wp_is_mobile() ? $p['what_desktop_photo'] : $p['what_mobile_photo'];
$items_content_for_recipe = $p['items_content_for_recipe'];
$to_buy_list_for_WA = str_replace('<br />', '%0a', $p['to_buy_list'] );
$to_buy_list_for_Email = str_replace('*', '', $p['to_buy_list'] );
$to_buy_list_for_Email2 = str_replace('<br />', '', $to_buy_list_for_Email );

$rtn .= '<div class="col-12" style="text-align:center;"><div class="areas" id="area_'. $i .'"><img class="main_des_image" id="schedimage_' . $i . '" src="' . $photo . '" data-imgsrc="' . $photo . '" /></div>';
$whatsup_linkeg = $p['whatsup_linkeg'];
$email_linkeg = $p['email_linkeg'];



if ($whatsup_linkeg != trim('')) {
$rtn .= '<span class="share" ><a class="whatsApp_Linkge" href="https://api.whatsapp.com/send/?text=' . $to_buy_list_for_WA . '" >' . $whatsup_linkeg . '</a> </span> ';
                }


if ($email_linkeg != trim('')) {
$rtn .= ' <span class="share"><a class="Email_Linkge" href="mailto:?gg&body=`' . $to_buy_list_for_Email2 . '`" >' . $email_linkeg . '</a> </span>';
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


public function tiktok_video()
    {
//WISAM : Tiktok video
//<script async src="https://www.tiktok.com/embed.js"></script>
$tiktokscript = '<script async src="https://www.tiktok.com/embed.js"></script>';

if (get_field("tiktok_video", get_the_ID())) {
// echo get_field("tiktok_video", get_the_ID());
$tiktok = get_field("tiktok_video", get_the_ID());
if (strpos($tiktokscript, $tiktok) == false) {
return $tiktok . $tiktokscript;
} else {
return $tiktok;
            }
        }
    }



public function YouTubeShort()
    {
//youtube_shorts
$rtn = '';


if (get_field("youtube_short", get_the_ID())) {
$VideoUrl = trim(get_field("youtube_short", get_the_ID()));
if (wp_is_mobile()) {
$Short_yt_width = 'width="100%"';
$Short_yt_height = 'height="650"';
} else {
$Short_yt_width = 'width="451"';
$Short_yt_height = 'height="700"';
            }

$rtn = '<div style="overflow: auto;text-align: center;width:100%;margin: 0 auto;">
       
<iframe ' . $Short_yt_width . $Short_yt_height . ' src="https://www.youtube.com/embed/' . $VideoUrl . '"
title="פודי" frameborder="0"
allow="accelerometer; autoplay; clipboard-write;
encrypted-media; gyroscope; picture-in-picture"
allowfullscreen></iframe>
</div>';
        }

return $rtn;
    }



} //end class
