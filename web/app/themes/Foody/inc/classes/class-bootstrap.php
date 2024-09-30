<?php 
class Bootstrap5 {
    
    public function __construct() {
        // Hook the enqueue function to wp_enqueue_scripts action
        add_action('wp_enqueue_style', array($this, 'enqueue_bootstrapStyles'));
        add_action('wp_enqueue_script', array($this, 'enqueue_bootstrapScripts'));
   
    }
    public function enqueue_bootstrapStyles() {
        // Deregister the default jQuery
        // wp_deregister_script('jquery');
        
        // // Enqueue jQuery from CDN
        // wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', array(), '3.6.0', true);
        // wp_enqueue_script('jquery');
    
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap5-css', get_template_directory_uri() . '/resources/bootstrap_ver5/css/bootstrap.rtl.min.css');
    
        // Enqueue the styles and scripts
        //wp_enqueue_style('bootstrap5-css'); 
       
    }
    
    public function enqueue_bootstrapScripts() {

        // Enqueue Bootstrap JS (with Popper.js included in the bundle)
        wp_enqueue_script('bootstrap5-js', get_template_directory_uri() . '/resources/bootstrap_ver5/js/bootstrap.bundle.min.js', array('jquery'),true);
       // wp_enqueue_script('bootstrap5-js'); 
    }



public function ManuelBootstrap(){
 $link_style_script = '
<link href="'.get_template_directory_uri() . '/resources/bootstrap_ver5/css/bootstrap.rtl.min.css'.'" rel="stylesheet"/>
<script src="'.get_template_directory_uri() . '/resources/bootstrap_ver5/js/bootstrap.bundle.min.js'.'"></script>';
return $link_style_script ;
}



} // End Class



?>
