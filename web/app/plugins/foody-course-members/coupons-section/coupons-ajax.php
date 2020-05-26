<?php
function foody_get_coupon_value()
{
    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
    $course_name = isset($_POST['course_name']) ? $_POST['course_name'] : '';
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : '';
    $coupon = false;
    $coupon_id = null;
    $coupon_type = null;
    $original_price = (int)get_field('course_register_data_final_price', $course_id);
    $new_price = '';
    $current_date = strtotime(date("Y-m-d"));
    $expired = false;

    if ($coupon_code && $course_name && $course_id) {
        if (strpos($coupon_code, '_') != false) {
            /** unique coupon */
            $coupon_details = explode('_', $coupon_code);
            if (count($coupon_details) == 2) {
                $coupon = get_unique_coupon($coupon_details, $course_name);
                if (isset($coupon->used) && $coupon->used == '0' && isset($coupon->pending) && $coupon->pending == '0' && isset($coupon->expiration_date)) {
                    $expiration_date = strtotime($coupon->expiration_date);
                    if ($expiration_date >= $current_date) {
//                        update_unique_copupon_columns($coupon->coupon_id, $coupon_details, ['pending' => 1]);
                        $new_price = get_modified_course_price($course_id, $coupon->coupon_value, $original_price);
                        $coupon_id = $coupon->coupon_id;
                        $coupon_type = 'unique';
                    } else {
                        $expired = true;
                    }
                }
            }
        } else {
            /** general coupon */
            $coupon = get_general_coupon($coupon_code, $course_id);
            if (isset($coupon->max_amount) && isset($coupon->used_amount) && isset($coupon->gen_coupons_held) && ($coupon->max_amount > $coupon->used_amount + $coupon->gen_coupons_held) && isset($coupon->expiration_date)) {
                $expiration_date = strtotime($coupon->expiration_date);
                if ($expiration_date >= $current_date) {
//                    update_general_copupon_columns($coupon->coupon_id, ['gen_coupons_held' => $coupon->gen_coupons_held + 1]);
                    $new_price = get_modified_course_price($course_id, $coupon->coupon_value, $original_price);
                    $coupon_id = $coupon->coupon_id;
                    $coupon_type = 'general';
                } else {
                    $expired = true;
                }
            }
        }
    } else {
        wp_send_json_error(['error' => 'missing coupon data']);
    }

    if ($new_price !== false && (!empty($new_price) || $new_price === 0)) {
        wp_send_json_success(['new_price' => $new_price, 'id' => $coupon_id, 'couponType' => $coupon_type]);
    } else {
        if ($expired) {
            wp_send_json_success(['msg' => 'expired', 'price' => $original_price]);
        } else {
            wp_send_json_success(['msg' => 'no discount', 'price' => $original_price]);
        }
    }
}

add_action('wp_ajax_nopriv_foody_get_coupon_value', 'foody_get_coupon_value');
add_action('wp_ajax_foody_get_coupon_value', 'foody_get_coupon_value');

function get_modified_course_price($course_id, $coupon_value, $original_price)
{
    $new_price = false;
    if (strpos($coupon_value, '%') != false) {
        //percentages
        $discount_percentages = (int)str_replace('%', '', $coupon_value);
        $new_price = $original_price - ($original_price * $discount_percentages) / 100;
    } elseif ($original_price - (int)$coupon_value >= 0) {
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

function update_unique_copupon_columns($coupon_id, $coupon_details, $columns)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_unique_coupons_meta';

    $update_query = "UPDATE {$table_name} SET ";

    foreach ($columns as $table_column => $column_value) {
        $update_query .= $table_column . "= " . "'" . $column_value . "'" . ',';
    }

    if (substr($update_query, -1) == ',') {
        $update_query = substr($update_query, 0, -1);
    }

    $update_query .= " WHERE coupon_id=" . $coupon_id . " AND coupon_code= '" . $coupon_details[1] . "'";

    return $wpdb->query($update_query);
}

function update_general_copupon_columns($coupon_id, $columns)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';

    $update_query = "UPDATE {$table_name} SET ";

    foreach ($columns as $table_column => $column_value) {
        $update_query .= $table_column . "= " . "'" . $column_value . "'" . ',';
    }

    if (substr($update_query, -1) == ',') {
        $update_query = substr($update_query, 0, -1);
    }

    $update_query .= " WHERE coupon_id=" . $coupon_id;

    return $wpdb->query($update_query);
}

/**
 * $coupon_id = isset($_POST['couponId']) ? $_POST['couponId'] : false;
 * $coupon_type = isset($_POST['couponType']) ? $_POST['couponType'] : false;
 * $coupon_code = isset($_POST['couponCode']) ? $_POST['couponCode'] : false;
 */
function get_coupon_data_by_name($name)
{
    $coupon_data = [];
    if (strpos($name, '_') != false) {
        /** unique coupon */
        $coupon_details = explode('_', $name);
        if (count($coupon_details) == 2) {
            $coupon_data = [
                'id' => get_unique_coupon_id($coupon_details[0], $coupon_details[1]),
                'type' => 'unique',
                'coupon_code' => $name
            ];
        }
    } else {
        $coupon_data = [
            'id' => get_general_coupon_id($name),
            'type' => 'general',
            'coupon_code' => $name
        ];
    }


    return $coupon_data;
}

function get_unique_coupon_id($coupon_prefix, $coupon_code)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_unique_coupons_meta';
    $coupon_details = [];

    $query = "SELECT coupon_id FROM {$table_name} WHERE coupon_prefix = '" . $coupon_prefix . "' AND coupon_code = '" . $coupon_code . "'";
    $results = $wpdb->get_results($query);
    $results = is_array($results) ? $results : [];

    foreach ($results as $result) {
        $coupon_details = $result->coupon_id;
    }

    return $coupon_details;
}

function get_general_coupon_id($coupon_code)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $coupon_details = [];

    $query = "SELECT coupon_id FROM {$table_name} WHERE coupon = '" . $coupon_code . "' AND coupon_type = '" . __('כללי') . "'";
    $results = $wpdb->get_results($query);
    $results = is_array($results) ? $results : [];

    foreach ($results as $result) {
        $coupon_details = $result->coupon_id;
    }

    return $coupon_details;
}