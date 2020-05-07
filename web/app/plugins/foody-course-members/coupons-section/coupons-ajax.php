<?php
function get_coupon_value()
{
    global $wpdb;
    $main_table_name = $wpdb->prefix . 'foody_courses_coupons';
    $unique_coupons_table_name = $wpdb->prefix . 'foody_unique_coupons_meta';
    $unique_general_table_name = $wpdb->prefix . 'foody_general_coupons_meta';

    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
    $course_name = isset($_POST['course_name']) ? $_POST['course_name'] : '';



    if($coupon_code && $course_name){
        $coupon_query = "SELECT * FROM {$main_table_name} as coupons
INNER JOIN {$unique_coupons_table_name} as unique_coupons
ON coupons.coupon_id = unique_coupons.coupon_id 
where coupon_prefix = 'funz' 
and unique_coupons.coupon_code = '8c5c8ae58c7' 
and unique_coupons.used = 0
and coupons.course_name LIKE ";
    }
    else{
        wp_send_json_error();
    }

}
add_action('wp_ajax_foody_nopriv_get_coupon_value', 'foody_get_coupon_value');
add_action('wp_ajax_foody_get_coupon_value', 'foody_get_coupon_value');