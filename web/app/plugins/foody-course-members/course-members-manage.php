<?php
/**
 * @package Foody Courses Members Manager
 */

/*
Plugin Name: Foody Courses Members Manager
Description:￿help mangage Foody's Courses Members
Verision: 1.0.0
Author: Danielk
*/

defined('ABSPATH');
require 'courses-members-export.php';
require 'courses-members-ajax.php';
require 'coupons-section/coupons-ajax.php';
require 'coupons-section/courses-coupons-export.php';

//activation
register_activation_hook(__FILE__, 'members_table_install');
function members_table_install()
{
}

add_action('admin_menu', 'add_admin_page_content');
function add_admin_page_content()
{
    add_menu_page('משתמשי קורסים', 'משתמשי קורסים', 'administrator', __FILE__, 'members_table_admin_page', 'dashicons-admin-users');
    add_submenu_page('foody-course-members/course-members-manage.php', 'הוסף משתמש חדש', 'הוסף חדש', 'administrator', 'add_new_member', 'add_new_member_page', 1);
    add_submenu_page('foody-course-members/course-members-manage.php', 'עדכון משתמש', '', 'administrator', 'update_course_member', 'update_course_member', 2);

    // coupons system init
    add_menu_page('רשימת קופונים', 'רשימת קופונים', 'administrator', 'coupons_table_admin_page', 'coupons_table_admin_page_func', 'dashicons-tickets');
    add_submenu_page('coupons_table_admin_page', 'הוסף קופון חדש', 'הוסף חדש', 'administrator', 'add_new_coupon', 'add_new_coupon_page', 1);
    add_submenu_page('coupons_table_admin_page', 'עדכון קופון', '', 'administrator', 'update_coupon', 'update_coupon_page', 2);
}

function add_new_member_page()
{
    require 'add_new_course_member.php';
}

function add_new_coupon_page()
{
    require 'coupons-section/add_new_coupon.php';
}

function coupons_table_admin_page_func()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';

    if (isset($_POST['new_coupon'])) {
        $coupon_name = $_POST['coupon'];
        $coupon_type = $_POST['coupon_type'];
        $course_name = is_array($_POST['course_name']) ? get_courses_names($_POST['course_name']) : $_POST['course_name'];
        $courses_ids = is_array($_POST['course_name']) ? implode(',', $_POST['course_name']) : implode(',', [$_POST['course_name']]);
        $creation_date = $_POST['creation_date'];
        $expiration_date = $_POST['expiration_date'];
        $coupon_value = $_POST['percentages'] != 'false' ? $_POST['coupon_value'] . '%' : $_POST['coupon_value'];
        $organization = isset($_POST['organization']) ? $_POST['organization'] : '';
        $max_amount = $_POST['max_amount'];
        $used_amount = 0;
        $invoice_desc = $_POST['invoice_desc'];
        $gen_coupons_held = $coupon_type == 'כללי' ? 0 : -1;

        /** add to coupons table */
        $wpdb->query("INSERT INTO {$table_name} (coupon, coupon_type, course_name, creation_date, expiration_date, coupon_value, organization, max_amount, used_amount, gen_coupons_held, invoice_desc)
                VALUES('$coupon_name','$coupon_type','$course_name','$creation_date','$expiration_date','$coupon_value','$organization','$max_amount','$used_amount','$gen_coupons_held','$invoice_desc')");

        $coupon_id = $wpdb->insert_id;

        /** add to coupons_meta table */
        switch ($coupon_type) {
            case __('כללי'):
                add_to_meta_table('general', ['coupon_id', 'coupon_code', 'course_id'], ['coupon_id' => $coupon_id, 'coupon_code' => $coupon_name, 'courses_ids' => $_POST['course_name']], $max_amount);
                break;
            case __('חח״ע'):
                add_to_meta_table('unique', ['coupon_id', 'coupon_prefix', 'coupon_code', 'used', 'courses_ids'], ['coupon_id' => $coupon_id, 'coupon_prefix' => $coupon_name, 'used' => 0, 'courses_ids' => $courses_ids], $max_amount);
                break;
        }

        echo "<script>location.replace('admin.php?page=coupons_table_admin_page');</script>";
    }

    if (isset($_POST['unique_coupons_id'])) {
        Foody_courses_coupons_exporter::generate_xlsx($_POST['unique_coupons_id']);
    }

    if (isset($_POST['update'])) {
        $table_fields = ['expiration_date', 'course_name', 'coupon_value', 'max_amount', 'invoice_desc'];
        $coupon_id = $_POST['coupon_id'];
        $new_coupon_amount = $_POST['max_amount'];
        $coupons = $wpdb->get_results("SELECT * FROM $table_name WHERE coupon_id='$coupon_id'");
        $course_name = is_array($_POST['course_name']) ? get_courses_names($_POST['course_name']) : $_POST['course_name'];
        $courses_ids = is_array($_POST['course_name']) ? implode(',', $_POST['course_name']) : implode(',', [$_POST['course_name']]);

        foreach ($coupons as $coupon) {
            $old_coupon_amount = $coupon->max_amount;
            $used_coupon_amount = $coupon->used_amount;
            $coupon_type = $coupon->coupon_type;
            $coupon_name = $coupon->coupon;
        }

        $update_query = 'UPDATE ' . $table_name . ' SET ';
        foreach ($table_fields as $table_field) {
            if ($table_field == 'coupon_value') {
                $coupon_value = $_POST['percentages'] != 'false' ? $_POST['coupon_value'] . '%' : $_POST['coupon_value'];
                $update_query .= $table_field . "= " . "'" . $coupon_value . "'" . ',';
            } elseif ($table_field == 'course_name') {
                $update_query .= $table_field . "= " . "'" . $course_name . "'" . ',';
            } elseif (isset($_POST[$table_field])) {
                $update_query .= $table_field . "= " . "'" . $_POST[$table_field] . "'" . ',';
            }
        }

        if (substr($update_query, -1) == ',') {
            $update_query = substr($update_query, 0, -1);
        }

        $update_query .= " WHERE coupon_id=" . $coupon_id;
        $wpdb->query($update_query);


        /** add to coupons_meta table */
        $amount_delta = get_the_delta_of_amounts($old_coupon_amount, $new_coupon_amount, $used_coupon_amount);
        if ($coupon_type == 'חח״ע') {
            $unique_coupons_table = $wpdb->prefix . 'foody_unique_coupons_meta';
            if ($amount_delta['delta'] > 0) {
                switch ($amount_delta['action']) {
                    case 'delete':
                        remove_meta_from_table('unique', ['coupon_id' => $coupon_id, 'coupon_prefix' => $coupon_name], $amount_delta['delta']);
                        break;
                    case 'insert':
                        add_to_meta_table('unique', ['coupon_id', 'coupon_prefix', 'coupon_code', 'used', 'courses_ids'], ['coupon_id' => $coupon_id, 'coupon_prefix' => $coupon_name, 'used' => 0, 'courses_ids' => $courses_ids], $amount_delta['delta']);
                        break;
                }
            }
            $courses_ids_update_query = "UPDATE {$unique_coupons_table} SET courses_ids = '" . $courses_ids . "' WHERE coupon_id = " . $coupon_id;
            $wpdb->query($courses_ids_update_query);
        } else {
            // general coupons table
            //delete all prev db rows for coupon
            $general_coupons_table = $wpdb->prefix . 'foody_general_coupons_meta';
            $delete_query = "DELETE FROM  {$general_coupons_table} WHERE coupon_id=" . $coupon_id;
            $wpdb->query($delete_query);

            add_to_meta_table('general', ['coupon_id', 'coupon_code', 'course_id'], ['coupon_id' => $coupon_id, 'coupon_code' => $coupon_name, 'courses_ids' => $_POST['course_name']], $amount_delta['delta']);
        }

        echo "<script>location.replace('admin.php?page=coupons_table_admin_page');</script>";
    }

    ?>
    <div class="container-fluid">
        <?php require 'coupons-section/coupons-list.php'; ?>
    </div>
    <?php
}

function get_the_delta_of_amounts($old_amount, $new_amount, $used_amount)
{
    $delta_old = $old_amount - $used_amount;
    $delta_new = $new_amount - $used_amount;

    if ($old_amount > $new_amount) {
        return ['delta' => $delta_old - $delta_new, 'action' => 'delete'];
    } else {
        return ['delta' => $delta_new - $delta_old, 'action' => 'insert'];
    }
}

function get_coupon_data_by_id($id, $table_column)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_courses_coupons';

    $coupon_type_query = "SELECT {$table_column} FROM {$table_name} where coupon_id =" . $id;
    return $wpdb->query($coupon_type_query);
}

function get_courses_names($ids_list)
{
    $courses_names = '';
    $list_length = count($ids_list);

    foreach ($ids_list as $index => $id) {
        $is_last_iteration = $list_length - 1 == $index;

        $course_name = get_field('course_register_data_item_name', $id);
        if (!empty($course_name)) {
            // new course template
            if ($is_last_iteration) {
                $courses_names .= $course_name;
            } else {
                $courses_names .= $course_name . ',';
            }
        } else {
            $course_name = get_field('course_name_html', $id);
            // old course template - html
            if (!empty($course_name)) {
                if ($is_last_iteration) {
                    $courses_names .= $course_name;
                } else {
                    $courses_names .= $course_name . ',';
                }
            }
        }
    }
    return $courses_names;
}

function add_to_meta_table($table_type, $fields, $data, $amount)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'foody_' . $table_type . '_coupons_meta';
    $query_fields = '(' . implode(', ', $fields) . ')';
    $insert_query = "INSERT INTO {$table_name} {$query_fields} VALUES ";

    if ($table_type == 'unique') {
        for ($i = 0; $i < $amount; $i++) {
            $is_last_iteration = $amount - 1 == $i;
            if ($is_last_iteration) {
                // last iteration
                $insert_query .= '(' . $data['coupon_id'] . ', ' . "'" . $data['coupon_prefix'] . "'" . ', ' . "'" . substr(md5(uniqid()), 16) . "'" . ', ' . $data['used'] . ', ' . "'" . $data['courses_ids'] . "'" . ');';
            } else {
                $insert_query .= '(' . $data['coupon_id'] . ', ' . "'" . $data['coupon_prefix'] . "'" . ', ' . "'" . substr(md5(uniqid()), 16) . "'" . ', ' . $data['used'] . ', ' . "'" . $data['courses_ids'] . "'" . '), ';
            }
        }
    } else {
        // general coupon
        $num_of_ids = count($data['courses_ids']);
        foreach ($data['courses_ids'] as $id => $course_id) {
            if ($num_of_ids - 1 == $id) {
                // last iteration
                $insert_query .= '(' . $data['coupon_id'] . ', ' . "'" . $data['coupon_code'] . "'" . ', ' . $course_id . ');';
            } else {
                $insert_query .= '(' . $data['coupon_id'] . ', ' . "'" . $data['coupon_code'] . "'" . ', ' . $course_id . '), ';
            }
        }
    }

    return $wpdb->query($insert_query);
}

function remove_meta_from_table($table_type, $data, $amount)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'foody_' . $table_type . '_coupons_meta';
    $delete_query = "DELETE FROM {$table_name} WHERE coupon_id = {$data['coupon_id']} AND coupon_prefix = '{$data['coupon_prefix']}' AND used = 0 LIMIT {$amount}";

    if ($table_type == 'unique') {
        $result = $wpdb->query($delete_query);
    } else {
        $result = false;
    }

    return $result;
}

function members_table_admin_page()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'foody_courses_members';

    // requests check
    if (isset($_POST['new_member'])) {
        $member_email = $_POST['member_email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['phone'];
        $enable_marketing = $_POST['enable_marketing'] == 'true' ? 1 : 0;

        $course_obj = explode(':', $_POST['course_name'] );
        $course_id = $course_obj[0];
        $course_name = $course_obj[1];
        $price_paid = $_POST['course_price'];
        $payment_method = $_POST['payment_method'];
        $transaction_id = $_POST['transaction_id'];
        $coupon = $_POST['coupon'];
        $purchase_date = $_POST['purchase_date'];
        $organization = isset($_POST['organization']) ? $_POST['organization'] : '';
        $note = $_POST['note'];
        $status = 'paid';

        $send_data = $_POST['send_data'] == 'true' ? true : false;

        if ($send_data) {
            send_new_course_member_data([
                'member_email' => $member_email,
                'phone' => $phone,
                'name' => $first_name . ' ' . $last_name,
                'course_name' => $course_name,
                'price' => $price_paid,
                'enable_marketing' => $enable_marketing,
                'coupon' => $coupon
            ], $course_id);
        }

        $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, marketing_status, course_name, course_id, price_paid, organization, payment_method, transaction_id, coupon, purchase_date, note, status, payment_method_id)
                VALUES('$member_email','$first_name','$last_name','$phone','$enable_marketing','$course_name','$course_id','$price_paid','$organization','$payment_method','$transaction_id','$coupon','$purchase_date','$note','$status','-1')");
        echo "<script>location.replace('admin.php?page=foody-course-members%2Fcourse-members-manage.php');</script>";
    }

    if (isset($_POST['update'])) {
        $table_fields = ['member_email', 'first_name', 'last_name', 'phone', 'course_name', 'price_paid', 'payment_method', 'coupon', 'purchase_date', 'organization', 'note'];
        $member_id = $_POST['member_id'];

        $update_query = 'UPDATE ' . $table_name . ' SET ';
        foreach ($table_fields as $table_field) {
            if (isset($_POST[$table_field])) {
                if($table_field == 'course_name'){
                    $course_obj = explode(':', $_POST['course_name'] );
                    $update_query .= "course_name= " . "'" . $course_obj[1] . "'" . ',';
                    $update_query .= "course_id= " . "'" . $course_obj[0] . "'" . ',';
                }
                else {
                    $update_query .= $table_field . "= " . "'" . $_POST[$table_field] . "'" . ',';
                }
            }
        }

        if (substr($update_query, -1) == ',') {
            $update_query = substr($update_query, 0, -1);
        }

        $update_query .= " WHERE member_id=" . $member_id;
        $wpdb->query($update_query);
        echo "<script>location.replace('admin.php?page=foody-course-members%2Fcourse-members-manage.php');</script>";
    }
    ?>

    <div class="container-fluid">
        <?php require 'members-list.php'; ?>
    </div>
    <?php
}

function send_new_course_member_data($member_data, $course_id)
{
    $to = get_field('course_register_data_schooler_mail_box', $course_id);
    if (!empty($to)) {
        $subject = 'New Course Member';
        $body = create_courses_mail_body($member_data);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($to, $subject, $body, $headers);
    } else {
        return false;
    }
}

function create_courses_mail_body($member_data)
{
    $enable_marketing_text = $member_data['enable_marketing'] == 'true' ? __('מאשר קבלת דואר') : __('לא מאשר קבלת דואר');

    $mail_body = '<p>';
    $mail_body .= 'querystring__UserEmail: ' . $member_data['member_email'];
    $mail_body .= '</p>';
    $mail_body .= '<p>';
    $mail_body .= 'querystring__InvMobile: ' . $member_data['phone'];
    $mail_body .= '</p>';
    $mail_body .= '<p>';
    $mail_body .= 'querystring__intTo: ' . $member_data['name'];
    $mail_body .= '</p>';
    $mail_body .= '<p>';
    $mail_body .= 'querystring__ProdName: ' . $member_data['course_name'];
    $mail_body .= '</p>';
    $mail_body .= '<p>';
    $mail_body .= 'querystring__suminfull: ' . $member_data['price'];
    $mail_body .= '</p>';
    $mail_body .= '<p>';
    $mail_body .= 'querystring__Custom5: ' . __('רשימה ראשית');
    $mail_body .= '</p>';
    $mail_body .= '<p>';
    $mail_body .= 'querystring__Custom10: ' . $enable_marketing_text;
    $mail_body .= '</p>';
    if (!empty($member_data['coupon'])) {
        $mail_body .= '<p>';
        $mail_body .= 'querystring__CouponNumber: ' . $member_data['coupon'];
        $mail_body .= '</p>';
    }

    return $mail_body;
}


function update_course_member()
{
    require 'courses-members-update.php';
}

function update_coupon_page()
{
    require 'coupons-section/courses-coupons-update.php';
}

function get_courses_list($for_coupons = true)
{
    $query_courses = new WP_Query(array(
        'post_type' => 'foody_course',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'post_status' => 'publish'
    ));

    $courses_list = [];
    if (isset($query_courses->posts) && is_array($query_courses->posts)) {
        foreach ($query_courses->posts as $id) {
            $course_name = get_field('course_register_data_item_name', $id);
            if (!empty($course_name)) {
                // new course template
                if ($for_coupons) {
                    $courses_list[$id] = $course_name;
                } else {
                    array_push($courses_list, $course_name);
                }
            } else {
                $course_name = get_field('course_name_html', $id);
                // old course tamplate - html
                if (!empty($course_name)) {
                    // new course template
                    if ($for_coupons) {
                        $courses_list[$id] = $course_name;
                    } else {
                        array_push($courses_list, $course_name);
                    }
                }
            }
        }
    }
    return $courses_list;
}

function get_orginazations_list()
{
    $query = new WP_Query(array(
        'post_type' => 'foody_organizations',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    $organizations_list = [];
    if (isset($query->posts) && is_array($query->posts)) {
        foreach ($query->posts as $organization) {
            if (!empty($organization->post_title)) {
                array_push($organizations_list, $organization->post_title);
                //todo: coupon

                $sub_organizations = get_field('sub_organizations', $organization->ID);
                if (!empty($sub_organizations) && is_array($sub_organizations)) {
                    foreach ($sub_organizations as $sub_organization) {
                        $sub_organization_name = $organization->post_title . ' - ' . $sub_organization['name'];

                        //todo: coupon
                        array_push($organizations_list, $sub_organization_name);
                    }
                }
            }
        }
    }

    return $organizations_list;
}