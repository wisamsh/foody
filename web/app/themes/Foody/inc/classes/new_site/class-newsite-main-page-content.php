<?php 
class MainPageContent{
public $pageID;
public function __construct()
{
    $this->pageID = get_the_ID(); 
}


public function get_MainBanner(){
 if(!wp_is_mobile()){
    return get_field("ns_hp_fetured_image_desktop");
 }
 else{
    return get_field("ns_hp_fetured_image_mobile");
 }
}

public function Get_Promoted_Recipies(){
    $posts_IDS =  get_field("ns_mp_featured_post");
    $post_details = [];
foreach($posts_IDS as $key=>$post_id){
    $author_id = get_post_field('post_author', $post_id);
    $post_details[$post_id] =  array(
       "id"=>$post_id,
        "title"=>get_the_title($post_id),
        "expert"=>get_the_excerpt($post_id),
        "featured_image"=> get_the_post_thumbnail_url($post_id, 'full'),
        "author_name"=>get_the_author_meta('display_name', $author_id),
        "author_avatar"=>get_avatar_url($author_id, ['size' => 96]),
        "author_url"=>get_author_posts_url($author_id)
    );
}
return $post_details;
}




}//END CLASS
?>