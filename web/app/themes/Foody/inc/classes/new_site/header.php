<?php 
class FoodyHeader_NewSite{

function __construct()
{
    add_action( 'wp_enqueue_scripts', array($this,'my_enqueue_styles'), 1 );
    
}

public function my_enqueue_styles() {
    // Enqueue new site style
    wp_enqueue_style( 'new-site-style', get_template_directory_uri() . '/resources/sass/newsite/new_site_style.css', array(), '1.0.0' );
}



}
?>