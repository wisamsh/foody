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

    if($payment_method == __('כרטיס אשראי')) {
        $member_added_to_table = $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, marketing_status, course_name, course_id, price_paid, organization, payment_method, transaction_id, credit_low_profile_code, coupon, purchase_date, note, status, payment_method_id)
                VALUES('$member_email','$first_name','$last_name','$phone','$enable_marketing','$course_name','$course_id','$price_paid','','$payment_method','-1','$transaction_id','$coupon','$purchase_date','','$status','$payment_method_id')");
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
