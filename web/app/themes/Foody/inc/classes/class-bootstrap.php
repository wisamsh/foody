<?php 
class Bootstrap5 {
    
    public function __construct() {
        // Hook the enqueue function to wp_enqueue_scripts action
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    // Function to enqueue Bootstrap CSS, JS, and jQuery with CDN fallback
    public function enqueue_assets() {
        // First, check if jQuery is already registered by WordPress
        if (!wp_script_is('jquery', 'enqueued')) {
            // Enqueue jQuery from a CDN if not available
            wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', array(), '3.6.0', true);
        } else {
            // Enqueue WordPress's built-in jQuery if available
            wp_enqueue_script('jquery');
        }

        // Enqueue Bootstrap CSS
        wp_register_style('bootstrap5-css', get_template_directory_uri() . '/resources/bootstrap_ver5/css/bootstrap.rtl.min.css', array(), '5.3.0');

        // Enqueue Bootstrap JS (with Popper.js included in the bundle)
        wp_register_script('bootstrap5-js', get_template_directory_uri() . '/resources/bootstrap_ver5/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
    }

} // End Class



?>
