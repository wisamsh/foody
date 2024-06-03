<?php




class GoogleBigQuery
{
  function __construct()
  {
    
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
      'posts_per_page' => 100,            // Number of posts to fetch
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
        echo '<div style="widht:100%;display:block;"><span>Updated :</span> '  .$dataArray[$k]['receipe_name'] . ' Score : '.$dataArray[$k]['recipe_score'].'</div>';
      }
      
      die();
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
   foreach ($dataArray as $k=>$v){
    update_field('recipe_poppularity', $dataArray[$k]['recipe_score'], $dataArray[$k]['item_id']);
    echo '<div style="widht:100%;display:block;"><span>Updated :</span> '  .$dataArray[$k]['receipe_name'] . '</div>';
  }
   
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
    echo ('<div class="wrap"><h1>Google Big Query</h1><p>Fetching Google BigQuery:</p>');
    echo ('<input type="button" id="showbigquery" value="Update"/><div class="result_bigquery"></div></div>');
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

    if ($this->Get_Envoierment() != 'foody.co.il') 
    {
      $data = $this->Staging_Develop_Fetch_BigQuery() ;
    }
      else {
      $data = $this->Fetching_BigQuery_Popolarity();
    }

    print_r($data);

    wp_die();
  }
}// End class
