<?php
function foody_get_coupon_value()
{
    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
    $course_name = isset($_POST['course_name']) ? $_POST['course_name'] : '';
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : '';
    $coupon = false;
    $original_price = (int)get_field('course_register_data_final_price', $course_id);
    $new_price = '';

    if ($coupon_code && $course_name && $course_id) {
        if (strpos($coupon_code, '_') != false) {
            /** unique coupon */
            $coupon_details = explode('_', $coupon_code);
            if (count($coupon_details) == 2) {
                $coupon = get_unique_coupon($coupon_details, $course_name);
                if (isset($coupon->used) && $coupon->used == '0') {
                    $new_price = get_modified_course_price($course_id, $coupon->coupon_value, $original_price);
                }
            }
        } else {
            /** general coupon */
            $coupon = get_general_coupon($coupon_code, $course_id);
            if (isset($coupon->max_amount) && isset($coupon->used_amount) && ($coupon->max_amount > $coupon->used_amount)) {
                $new_price = get_modified_course_price($course_id, $coupon->coupon_value, $original_price);
            }
        }
    } else {
        wp_send_json_error(['error' => 'missing coupon data']);
    }

    if (!empty($new_price)) {
        wp_send_json_success(['new_price' => $new_price]);
    } else {
        wp_send_json_success(['wp_send_json_success' => 'no discount', 'price' => $original_price]);
    }
}

add_action('wp_ajax_foody_nopriv_get_coupon_value', 'foody_get_coupon_value');
add_action('wp_ajax_foody_get_coupon_value', 'foody_get_coupon_value');

function get_modified_course_price($course_id, $coupon_value, $original_price)
{

    if (strpos($coupon_value, '%') != false) {
        //percentages
        $discount_percentages = (int)str_replace('%', '', $coupon_value);
        $new_price = $original_price - ($original_price * $discount_percentages) / 100;
    } else {
        $new_price = $original_price - (int)$coupon_value;
    }

    return $new_price;
}

function get_unique_coupon($coupon_details, $course_name)
{
    global $wpdb;
    $main_table_name = $wpdb->prefix . 'foody_courses_coupons';
    $unique_coupons_table_name = $wpdb->prefix . 'foody_unique_coupons_meta';

    $coupon_query = "SELECT * FROM {$main_table_name} as coupons
INNER JOIN {$unique_coupons_table_name} as unique_coupons
ON coupons.coupon_id = unique_coupons.coupon_id 
where coupon_prefix = '{$coupon_details[0]}'
and unique_coupons.coupon_code = '{$coupon_details[1]}'
and coupons.course_name LIKE '%{$course_name}%'";

    $coupon = $wpdb->get_results($coupon_query);
    if ($coupon != false && count($coupon) > 0) {
        return $coupon[0];
    } else {
        return false;
    }
}


function get_general_coupon($coupon_code, $course_id)
{
    global $wpdb;
    $main_table_name = $wpdb->prefix . 'foody_courses_coupons';
    $unique_general_table_name = $wpdb->prefix . 'foody_general_coupons_meta';

    $coupon_query = "SELECT * FROM {$main_table_name} as coupons
INNER JOIN {$unique_general_table_name} as general_coupons
ON coupons.coupon_id = general_coupons.coupon_id WHERE general_coupons.coupon_code = '{$coupon_code}' AND course_id = {$course_id}";

    $coupon = $wpdb->get_results($coupon_query);
    if ($coupon != false && count($coupon) > 0) {
        return $coupon[0];
    } else {
        return false;
    }
}