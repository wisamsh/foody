<?php
function foody_add_course_member_to_table($custom_val, $return_id = false)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_members';

    $member_email = $custom_val['email'];
    $first_name = $custom_val['first_name'];
    $last_name = $custom_val['last_name'];
    $phone = $custom_val['phone'];
    $enable_marketing = $custom_val['enable_marketing'] == 'true' ? 1 : 0;
    $course_name = $custom_val['course_name'];
    $course_id = $custom_val['course_id'];
    $price_paid = $custom_val['price'];
    $payment_method = $custom_val['payment_method'];
    $transaction_id = $custom_val['transaction_id'];
    $coupon = $custom_val['coupon'];
    $purchase_date = $custom_val['purchase_date'];
    $status = $custom_val['status'];
    $payment_method_id = $custom_val['payment_method_id'];
//    $server_number = FOODY_INSTANCE_NUM;

    if($payment_method == __('כרטיס אשראי') || $payment_method == __('ללא עלות')) {
        if(isset($custom_val['address'])){
            $address = $custom_val['address'];
            $member_added_to_table = $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, address, marketing_status, course_name, course_id, price_paid, organization, payment_method, transaction_id, credit_low_profile_code, coupon, purchase_date, note, status, payment_method_id)
                VALUES('$member_email','$first_name','$last_name','$phone','$address','$enable_marketing','$course_name','$course_id','$price_paid','','$payment_method','-1','$transaction_id','$coupon','$purchase_date','','$status','$payment_method_id')");
        }
        else {
            $member_added_to_table = $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, marketing_status, course_name, course_id, price_paid, organization, payment_method, transaction_id, credit_low_profile_code, coupon, purchase_date, note, status, payment_method_id)
                VALUES('$member_email','$first_name','$last_name','$phone','$enable_marketing','$course_name','$course_id','$price_paid','','$payment_method','-1','$transaction_id','$coupon','$purchase_date','','$status','$payment_method_id')");
        }
    }
    else{
        $member_added_to_table = $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, marketing_status, course_name, course_id, price_paid, organization, payment_method, transaction_id, credit_low_profile_code, coupon, purchase_date, note, status, payment_method_id)
                VALUES('$member_email','$first_name','$last_name','$phone','$enable_marketing','$course_name','$course_id','$price_paid','','$payment_method','$transaction_id','-1','$coupon','$purchase_date','','$status','$payment_method_id')");
    }


    $member_id = $wpdb->insert_id;

    if ($member_added_to_table) {
        if ($return_id) {
            return $member_id;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

function foody_delete_row()
{
    $member_id =  isset($_POST['memberID']) ? $_POST['memberID'] : false;
    if ($member_id) {
        $updated = update_course_member_by_id_and_cloumns($member_id, ['deleted' => 1]);

        if($updated){
            wp_send_json_success(['msg' => __('השורה עם מזהה ' . $member_id . ' נמחקה')]);
        }
        else{
            wp_send_json_error(['error' => 'המחיקה לא צלחה, אנא נסו שוב']);
        }
    } else {
        wp_send_json_error(['error' => 'missing member id']);
    }

}

add_action('wp_ajax_nopriv_foody_delete_row', 'foody_delete_row');
add_action('wp_ajax_foody_delete_row', 'foody_delete_row');
