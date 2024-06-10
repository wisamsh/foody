<?php
// Your existing code in functions.php
error_reporting(0);
// Class to handle monthly cron job
class My_Monthly_Cron_Job_GoogleBigQueryPopularity
{

    public function __construct()
    {
        // Hook to add custom schedules

        // if (date('j') == 6) {
        //     $this->runCron();
        // }
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
}//end class
