<?php
function foody_add_course_member_to_table($custom_val)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_members';

    //if we got here -> bit payment confirmed
//    $custom_val = $_POST['memberData'];

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



    $member_added_to_table = $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, marketing_status, course_name, course_id, price_paid, organization, payment_method, transaction_id, coupon, purchase_date, note, status, payment_method_id)
                VALUES('$member_email','$first_name','$last_name','$phone','$enable_marketing','$course_name','$course_id','$price_paid','','$payment_method','$transaction_id','$coupon','$purchase_date','','$status','$payment_method_id')");

//    if($mail_sent && $member_added_to_table) {
//        wp_send_json_success();
//    }
    if($member_added_to_table) {
        return true;
    }
//    elseif ($mail_sent){
//        wp_send_json_success();
//    }
//    elseif ($member_added_to_table){
//        wp_send_json_success();
//    }
    else{
//        wp_send_json_error();
        return false;
    }
}

//add_action('wp_ajax_foody_nopriv_add_course_member_to_table', 'foody_add_course_member_to_table');
//add_action('wp_ajax_foody_add_course_member_to_table', 'foody_add_course_member_to_table');
