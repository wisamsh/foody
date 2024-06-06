<?php
class GoogleBigQuery
{
  function __construct()
  {
    $this->create_Cron_table();
    add_action('admin_menu', array($this, 'register_menu'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('wp_ajax_get_big_query_data', array($this, 'handle_ajax_request'));
  }

  private function Get_Envoierment()
  {
    return $_SERVER['SERVER_NAME'];
  }




  private function Staging_Develop_Fetch_BigQuery()
  {
    $args = array(
      'post_type'      => 'foody_recipe', // Custom post type name
      'posts_per_page' => 100000,            // Number of posts to fetch
      'fields'         => 'ids',          // Fetch only post IDs
      'orderby'        => 'date',         // Order by date
      'order'          => 'DESC'          // Order by descending (latest posts first)
    );

    $recipes = get_posts($args);

    // Fetch JSON content using cURL
    //$jsonUrl = 'https://storage.googleapis.com/store_recipe_bq/recipe_stats_idbased.json';
    $jsonUrl = get_field('bigquery_url', 'option');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $jsonUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout after 30 seconds
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout for connection phase
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing purposes

    $jsonContent = curl_exec($ch);

    if (curl_errno($ch)) {
      $error_message = 'Error fetching data: ' . curl_error($ch);
      curl_close($ch);
      echo json_encode(array('error' => $error_message));
      die();
    }

    curl_close($ch);

    $dataArray = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      echo json_encode(array('error' => 'Error decoding JSON: ' . json_last_error_msg()));
      die();
    } else {
      foreach ($recipes as $k => $v) {
        $dataArray[$k]['item_id'] = $v;
        update_field('recipe_poppularity', $dataArray[$k]['recipe_score'], $v);
        //update_field('recipe_poppularity', '', $v);
        echo '<div class=bgdiv><span>ItemID : ' . $dataArray[$k]['item_id'] . '</span><span>עודכן המתכון :</span><b> '  . $dataArray[$k]['receipe_name'] . '</b> פופולריות : ' . $dataArray[$k]['recipe_score'] . '</div>';
      }
      echo '<style>.bgdiv{width:80%;display:block;direction:rtl;text-align:right;border-radius:10px;padding:10px;margin:5px;border:1px solid #ddd;background:#fff;}</style>';
      die();
    }
  }


  public function get_Last_GBQ_Fetch()
  {

    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'cron_job_for_googlebigquery';
    
    // SQL query to get the last inserted row
    $sql = 'select * from '.$table_name.' ORDER BY id DESC LIMIT 1';
    
    // Fetch the row
    $last_row = $wpdb->get_results($sql);
    
    // Check if a row was returned and return it
    if ($last_row) {
        return $last_row;
    } else {
        return null;
    }
    
  }



  private function Fetching_BigQuery_Popolarity()
  {
 
    $jsonUrl = get_field('bigquery_url', 'option');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $jsonUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Timeout after 30 seconds
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout for connection phase
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing purposes

    $jsonContent = curl_exec($ch);

    if (curl_errno($ch)) {
      $error_message = 'Error fetching data: ' . curl_error($ch);
      curl_close($ch);
      return array($error_message);
    }

    curl_close($ch);

    $dataArray = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return array('Error decoding JSON: ' . json_last_error_msg());
    } else {
      //return $dataArray;
        $this->insert_data_into_custom_table($this->GetUserAdmin() , $jsonContent);
       
      foreach ($dataArray as $k => $v) {
        update_field('recipe_poppularity', $dataArray[$k]['recipe_score'], $dataArray[$k]['item_id']);
        echo '<div class=bgdiv><span>Item_ID : ' . $dataArray[$k]['item_id'] . '</span> <span>עודכן המתכון :</span><b> '  . $dataArray[$k]['receipe_name'] . '</b> פופולריות : ' . $dataArray[$k]['recipe_score'] . '</div>';
      }
     
//GetUserAdmin
      echo '<style>.bgdiv{width:80%;display:block;direction:rtl;text-align:right;border-radius:10px;padding:10px;margin:5px;border:1px solid #ddd;background:#fff;}</style>';
      die();
    }
  }


  public function GetUserAdmin()
  {
    $current_user = wp_get_current_user();
    if ($current_user->exists()) {
      $username = $current_user->user_login; // Get the username
     return $username;
    } else {
      return 'Error in getting user';
    }
  }

  public function Update_BigQuery_Popolarity_ForCronJob()
  {
    $jsonUrl = get_field('bigquery_url', 'option');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $jsonUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Timeout after 30 seconds
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout for connection phase
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for testing purposes

    $jsonContent = curl_exec($ch);

    if (curl_errno($ch)) {
      $error_message = 'Error fetching data: ' . curl_error($ch);
      curl_close($ch);
      return array($error_message);
    }

    curl_close($ch);

    $dataArray = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return array('Error decoding JSON: ' . json_last_error_msg());
    } else {
      //return $dataArray;
      foreach ($dataArray as $k => $v) {
        update_field('recipe_poppularity', $dataArray[$k]['recipe_score'], $dataArray[$k]['item_id']);
        }
       $this->insert_data_into_custom_table($this->GetUserAdmin());
     
    }
  }


  public function register_menu()
  {
    add_menu_page(
      'BigQuery',           // Page Title
      'Google BigQuery',    // Menu Title
      'manage_options',     // Capability (who can access)
      'google-bigquery-menu', // Menu Slug (unique identifier)
      array($this, 'render_menu'), // Callback function to render content
      'dashicons-admin-generic' // Icon (optional)
    );

    // Add a submenu page
    add_submenu_page(
      'google-bigquery-menu', // Parent Menu Slug
      'Submenu Page',         // Page Title
      'Submenu Page',         // Menu Title
      'manage_options',       // Capability (who can access)
      'custom-submenu',       // Menu Slug (unique identifier)
      array($this, 'render_submenu') // Callback function to render content
    );
  }

  public function render_menu()
  {
$CronGoogleBigQueryPopularity = new My_Monthly_Cron_Job_GoogleBigQueryPopularity;

    $currentDate = date('d-m-Y');
    $user_fetched = $this->get_Last_GBQ_Fetch();
    if($user_fetched){
    $lastdate = trim($user_fetched[0]->date_quering);
    $last_fetchingUpdate = $CronGoogleBigQueryPopularity->daysDifference($currentDate, $lastdate) ;
    $lu = $last_fetchingUpdate == 0 ? ' Today ' : $last_fetchingUpdate  ; 
  }
    echo ('<div class="wrap"><h1>Google Big Query</h1><p>Fetching Google BigQuery:</p>');
    if($user_fetched){
    echo ('Last Modified User : <b>' .  $user_fetched[0]->username . '</b><hr>');
    echo ('Last Modified Date : <b>' .  $user_fetched[0]->date_quering . '</b><hr>');
    echo  ('Last Modified Update Was <b>' . $lu  . '</b> days Ago!<hr>');
    }
    echo ('<input class="button button-primary" type="button" id="showbigquery" value="Update Big Query Popular Recipe"/><div class="result_bigquery"></div></div>');
  
  }

  public function enqueue_admin_scripts($hook)
  {
    // Check if we are on the correct admin page
    if ($hook != 'toplevel_page_google-bigquery-menu') {
      return;
    }
    wp_enqueue_script('GetBigQueryWithAjaxScript', get_template_directory_uri() . '/resources/js/GetBigQueryWithAjaxScript.js', array('jquery'), null, true);
    wp_localize_script('GetBigQueryWithAjaxScript', 'BigQueryObject', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('get_bigQuery_Nonce')
    ));
  }

  public function handle_ajax_request()
  {
    check_ajax_referer('get_bigQuery_Nonce', 'nonce');

    //if ($this->Get_Envoierment() != 'foody.co.il') 
    // {
    // $data = $this->Staging_Develop_Fetch_BigQuery() ;
    $data = $this->Fetching_BigQuery_Popolarity();

    //}
    ///  else {
    //  $data = $this->Fetching_BigQuery_Popolarity();
    //}

    print_r($data);

    wp_die();
  }


  private function create_Cron_table()
  {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'cron_job_for_googlebigquery';

    // SQL query to create the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date_quering VARCHAR(50),
        username VARCHAR(200) ,
        fetched_json LONGTEXT 

    )";

    // Execute the SQL query
    $wpdb->query($sql);
  }


  private function insert_data_into_custom_table($username, $json)
  {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'cron_job_for_googlebigquery';

    // Format the current date as dd-mm-yyyy
    $formatted_date = date('d-m-Y');

    // Data to insert
    $data = array(
      'date_quering' => $formatted_date, // Formatted date
      'username' => $username,
      'fetched_json'=>$json
    );

    // Insert data into the table
    $wpdb->insert($table_name, $data);
  }
}// End class
