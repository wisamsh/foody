<?php
if (is_category()) {
    $current_category = get_queried_object();
    
    if (is_a($current_category, 'WP_Term') && $current_category->taxonomy == 'category') {
        // The current queried object is a category
        $category_id = $current_category->term_id;
        $category_name = $current_category->name;
    
    
        function WhiteLabelMetaTags()
        {
            // Check if it's a specific page where you want to set noindex, nofollow
            $meta_keywords_seo = get_field("meta_keywords_seo",  $category_id);
            $meta_tag_seo = get_field("meta_tag_seo",  $category_id);
            if (trim($meta_keywords_seo) != "")
            {
                echo '<meta name="keywords" content="' . $meta_keywords_seo  . '" />';
            }
    
            if (trim($meta_tag_seo) != "")
            {
                echo '<meta name="description" content="' . $meta_tag_seo . '" />';
            }
        }
    
        // Hook into wp_head
        add_action('wp_head', 'WhiteLabelMetaTags', 1);
    }
}
