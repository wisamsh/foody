<?php
/**
 * Template Name: Google Big Query Tester
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
header('Content-Type: application/json; charset=utf-8');
set_time_limit(300);
// Fetch posts
$args = array(
    'post_type'      => 'foody_recipe', // Custom post type name
    'posts_per_page' => 100,            // Number of posts to fetch
    'fields'         => 'ids',          // Fetch only post IDs
    'orderby'        => 'date',         // Order by date
    'order'          => 'DESC'          // Order by descending (latest posts first)
);

$recipes = get_posts($args);

// Fetch JSON content using cURL
$jsonUrl = 'https://storage.googleapis.com/store_recipe_bq/recipe_stats_idbased.json';

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
    }
    echo json_encode($dataArray);
    die();
}
?>
