<?php 
class Bootstrap {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_bootstrap_assets']);
    }

    // Function to enqueue Bootstrap CSS and JS
    public function enqueue_bootstrap_assets() {
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap-css', get_template_directory().'/resources/bootstrap-5/css/bootstrap.rtl.min.css', array(), '5.3.0');

        // Enqueue Bootstrap JS (with Popper.js for proper functionality)
        wp_enqueue_script('bootstrap-js', get_template_directory().'/resources/bootstrap-5/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
    }


}//End Class


?>