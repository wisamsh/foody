<?php

require('../vendor/autoload.php');
use Google\Cloud\BigQuery\BigQueryClient;
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
        echo json_encode($response);
        // Terminate immediately after sending the response
        wp_die();
    }

    public function custom_endpoint() {
        add_rewrite_rule('^foodybigqueryuri/?$', 'index.php?foodybigqueryuri=1', 'top');
        add_rewrite_tag('%foodybigqueryuri%', '([^&]+)');
    }


   public function executeBigQuery(){
$projectId = 'foody-340014';
$FileName = 'foody-340014-a1b12010795d';
$keyFilePath = get_template_directory() . '/w_helpers/'. $FileName;
// Set the path to your service account JSON key file


// Create a BigQuery client with service account credentials
$bigQuery = new BigQueryClient([
'projectId' => $projectId,
'keyFilePath' => $keyFilePath,
]);

        $datasetId = 'analytics_258865933';
        $tableId = 'recipe_rank_by_category';
        $query = "SELECT * FROM " .$datasetId ."." .$tableId . " LIMIT 10";



// Query BigQuery
$queryResults = $bigQuery->query($query);

// Loop through the results and process data (assuming data exists)
if ($queryResults->isValid()) {  // Corrected method to check valid results
$rows = $queryResults->readAllRows();
print_r($rows);
}
}




   public function handle_custom_endpoint() {
        global $wp_query;
       
        if (isset($wp_query->query_vars['foodybigqueryuri'])) {
            // Execute your PHP code here
            $response = $this->executeBigQuery();
           
            // Output the response
            header('Content-Type: application/json');
            echo json_encode($response);
           
            // Stop WordPress from loading any further
            exit;
        }
    }
}
