<?php 
if(is_category()){
    
   
    function WhiteLabelMetaTags() {
        // Check if it's a specific page where you want to set noindex, nofollow
    $meta_keywords_seo = get_field("meta_keywords_seo", get_the_ID());
    $meta_tag_seo = get_field("meta_tag_seo", get_the_ID());
            echo '<meta name="keywords" content="'. $meta_keywords_seo  .' />';
            echo '<meta name="description" content="'.$meta_tag_seo.'" />';
        }
        
            // Hook into wp_head
         add_action('wp_head', 'WhiteLabelMetaTags', 1);
}

?>