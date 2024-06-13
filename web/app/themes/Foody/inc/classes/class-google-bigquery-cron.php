<?php
// Your existing code in functions.php
//error_reporting(0);
// Class to handle monthly cron job
class My_Monthly_Cron_Job_GoogleBigQueryPopularity
{

    public function __construct()
    {
        
        add_action('admin_enqueue_scripts', array($this, 'cron_enqueue_scripts'));
        
        // Hook into the 'admin_notices' to display our notice
        add_action('admin_notices', array($this, 'display_admin_notice'));
        
        // Hook into the AJAX action
        add_action('wp_ajax_run_background_check', array($this, 'run_background_check'));
    }


    public function cron_enqueue_scripts() {
        wp_enqueue_script('background-check-script', get_template_directory_uri() . '/resources/js/BackgroundCheckBigquery.js', array('jquery'));
        wp_localize_script('background-check-script', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }

    public function display_admin_notice() {
        if (current_user_can('manage_options')) {
            echo '<div id="background-check-notice" class="notice notice-info is-dismissible">
            <p class="cron_notice"></p>
            </div>';
        }
    }
//TODO MAKE THIS FUNCTION CHECK IF WENT MORE THAN MONTH SINCE THE LAST BIGQUERY UPDATE
    public function run_background_check() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
            return;
        }
        
        wp_send_json_success(['progress' => "90%"]);
    }





    public function runCron()
    {


        $expiration_time = time() + (30 * 24 * 60 * 60);
       // $GoogleBigQuery = new GoogleBigQuery;
       // $GoogleBigQuery->Update_BigQuery_Popolarity_ForCronJob();
    }


    public function daysDifference($startDate, $endDate)
    {
        // Create DateTime objects for the start date and end date with the format d-m-Y
        $startDateObj = DateTime::createFromFormat('d-m-Y', $startDate);
        $endDateObj = DateTime::createFromFormat('d-m-Y', $endDate);

        // Calculate the difference between the end date and the start date
        $interval = $endDateObj->diff($startDateObj);

        // Return the difference in days
        return $interval->days;
    }

    public function get_Last_GBQ_Fetch()
    {

        global $wpdb;

        // Table name
        $table_name = $wpdb->prefix . 'cron_job_for_googlebigquery';
        $sql  = 'select * from ' . $table_name . ' ORDER BY id DESC LIMIT 1';
        return $wpdb->query($sql);
    }

   



public function DayOfMonth(){
    return $current_day = date('j');
}


}//end class
