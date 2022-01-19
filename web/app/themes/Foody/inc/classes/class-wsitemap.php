<?php 
 class Foody_wsitemap 
{
   
private function get_posts_map_private($type){
    
  
        global $wpdb;
$uri = '';
$uri_flet = '' ;
switch($type){
case 'posts':
    $uri='';
    break;
    case 'page':
        $uri='';
        break;

    default:
    $uri='/'.$type;
}
if($uri !=''){
    $uri_flet ='';  
}
else{
    $uri_flet = '/'; 
}

    
        $custom_post_type = $type; 
    
        // A sql query to return all post titles
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title , post_name FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish' order by ID desc ", $custom_post_type ), ARRAY_A );
    
        // Return null if we found no results
        if ( ! $results )
            return;
    
        // HTML for our select printing post titles as loop
        $output = '<ul style="list-style:square;">';
    
        foreach( $results as $index => $post ) {
            $postID=$post['ID'];
            $url = get_permalink($postID);
            $output .= '<li  value="' . $post['ID'] . '"><a href="'.$url.'">' . $post['post_title'] . '</a></li>';
        }
    
        $output .= '</ul>'; // end of select element
    
        // get the html
        return $output;
    
    


}

public function get_posts_map($type){
    return $this->get_posts_map_private($type);
}

}
?>