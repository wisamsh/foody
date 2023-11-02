<?php
function foody_start_free_pay_process()
{
    $new_member_data = $_POST['memberData'];

    // add new user to table
    $added_id = foody_add_course_member_to_table($new_member_data, true);

    if ($added_id !== false) {
        try {
            //coupon was used => update to used
            if (isset($new_member_data['coupon']) && !empty($new_member_data['coupon'])) {
                $coupon_details = get_coupon_data_by_name($new_member_data['coupon']);

                // update coupon to used
                update_coupon_to_used($coupon_details, true);
            }

            // send mail to zappier
            send_new_course_member_data([
                'member_email' => $new_member_data['email'],
                'phone' => $new_member_data['phone'],
                'name' => $new_member_data['first_name'] . ' ' . $new_member_data['last_name'],
                'course_name' => $new_member_data['course_name'],
                'price' => $new_member_data['price'],
                'enable_marketing' => $new_member_data['enable_marketing'],
                'coupon' => $new_member_data['coupon']
            ], $new_member_data['course_id']);

            Rav_Messer_API_Handler::add_member_to_rav_messer(
                [
                    'member_email' => $new_member_data['email'],
                    'course_name' => $new_member_data['course_name'],
                    'name' => $new_member_data['first_name'] . ' ' . $new_member_data['last_name'],
                    'phone' => $new_member_data['phone']
                ]);

            // update user's status to paid
            update_course_member_by_id_and_cloumns($added_id, ['status' => 'paid']);

            return wp_send_json_success('success');
        } catch (Exception $exception) {
            return wp_send_json_error(['error' => $exception->getMessage()]);
        }
    } else {
        return wp_send_json_error(['error' => 'user not added to table']);
    }
}

add_action('wp_ajax_nopriv_foody_start_free_pay_process', 'foody_start_free_pay_process');
add_action('wp_ajax_foody_start_free_pay_process', 'foody_start_free_pay_process');
