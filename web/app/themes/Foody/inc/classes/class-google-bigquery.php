<?php 
class FoodyGoogleBigQuery {
    public function __construct() {
        // Hooks the enqueue_scripts method to wp_enqueue_scripts action
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Hooks the Foody_GoogleBigQuery_Ajax_Call method to wp_ajax_my_ajax_action and wp_ajax_nopriv_my_ajax_action actions
        add_action('wp_ajax_my_ajax_action', array($this, 'Foody_GoogleBigQuery_Ajax_Call'));
        add_action('wp_ajax_nopriv_my_ajax_action', array($this, 'Foody_GoogleBigQuery_Ajax_Call'));

        // Add custom endpoint
        add_action('init', array($this, 'custom_endpoint'));
        add_action('template_redirect', array($this, 'handle_custom_endpoint'));
    }

    public function enqueue_scripts() {
        // Enqueues the bigQueryAjaxScript.js file with appropriate dependencies and localization
        wp_enqueue_script('bigQueryAjaxScript', get_template_directory_uri() . '/resources/js/bigQueryAjaxScript.js', array('jquery'), '1.0', true);
        wp_localize_script('bigQueryAjaxScript', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function Foody_GoogleBigQuery_Ajax_Call() {
        // Handle AJAX request here
        $response = array('response' => 'This is the response from the server.');
        return json_encode($response);
        wp_die();
       // this is required to terminate immediately and return a proper response
    }

    public function custom_endpoint() {
        add_rewrite_rule('^foodybigqueryuri/?$', 'index.php?foodybigqueryuri=1', 'top');
        add_rewrite_tag('%foodybigqueryuri%', '([^&]+)');
    }

    public function handle_custom_endpoint() {
        global $wp_query;
        
        if (isset($wp_query->query_vars['foodybigqueryuri'])) {
            // Execute your PHP code here
            $response = $this->Foody_GoogleBigQuery_Ajax_Call();
            
            // Output the response
            header('Content-Type: application/json');
            echo $response;
            
            // Stop WordPress from loading any further
            exit();
        }
    }
    
}

?>