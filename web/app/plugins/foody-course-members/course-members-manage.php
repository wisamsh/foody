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

}

function add_new_member_page()
{
    require 'add_new_course_member.php';
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
        $course_name = $_POST['course_name'];
        $price_paid = $_POST['course_price'];
        $payment_method = $_POST['payment_method'];
        $transaction_id = $_POST['transaction_id'];
        $coupon = $_POST['coupon'];
        $purchase_date = $_POST['purchase_date'];
        $organization = isset($_POST['organization']) ? $_POST['organization'] : '';
        $note = $_POST['note'];

        $send_data = $_POST['send_data'] == 'true' ? true : false;

        if ($send_data) {
            // todo: if true send data to schooler
            send_new_course_member_date([
                'member_email' => $member_email,
                'phone' => $phone,
                'name' => $first_name . ' ' . $last_name,
                'course_name' => $course_name,
                'price' => $price_paid,
                '$enable_marketing' => $enable_marketing
            ]);
        }

        $wpdb->query("INSERT INTO {$table_name} (member_email, first_name, last_name, phone, marketing_status, course_name, price_paid, organization, payment_method, transaction_id, coupon, purchase_date, note)
                VALUES('$member_email','$first_name','$last_name','$phone','$enable_marketing','$course_name','$price_paid','$organization','$payment_method','$transaction_id','$coupon','$purchase_date','$note')");
        echo "<script>location.replace('admin.php?page=foody-course-members%2Fcourse-members-manage.php');</script>";
    }

    if (isset($_POST['update'])) {
        $table_fields = ['member_email', 'first_name', 'last_name', 'phone', 'course_name', 'price_paid', 'payment_method', 'coupon', 'purchase_date', 'organization', 'note'];
        $member_id = $_POST['member_id'];

        $update_query = 'UPDATE ' . $table_name . ' SET ';
        foreach ($table_fields as $table_field) {
            if (isset($_POST[$table_field])) {
                $update_query .= $table_field . "= " . "'" .$_POST[$table_field] . "'" . ',';
            }
        }

        if(substr($update_query, -1) == ','){
            $update_query = substr($update_query, 0, -1);
        }

        $update_query .= " WHERE member_id=".$member_id;
//        $member_email = $_POST['member_email'];
//        $first_name = $_POST['first_name'];
//        $last_name = $_POST['last_name'];
//        $phone = $_POST['phone'];
//        $course_name = $_POST['course_name'];
//        $price_paid = $_POST['course_price'];
//        $payment_method = $_POST['payment_method'];
//        $coupon = $_POST['coupon'];
//        $purchase_date = $_POST['purchase_date'];
//        $organization = isset($_POST['organization']) ? $_POST['organization'] : '';
//        $note = $_POST['note'];
//        $wpdb->query("UPDATE $table_name SET member_email='$member_email',first_name='$first_name',last_name='$last_name',phone='$phone', lat='$course_name', lng='$lng' WHERE id='$id'");
        $wpdb->query($update_query);
        echo "<script>location.replace('admin.php?page=foody-course-members%2Fcourse-members-manage.php');</script>";
    }
    ?>

    <div class="container-fluid">
        <?php require 'members-list.php'; ?>
    </div>
    <?php
}

function send_new_course_member_date($member_data)
{
    $to = get_option('foody_mail_for_courses_data');
    if (!empty($to)) {
        $subject = 'New Course Member';
        $body = create_courses_mail_body($member_data);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $mail_sent = wp_mail($to, $subject, $body, $headers);
        if ($mail_sent) {
            //finish
        } else {
            // Error
        }

    } else {
        // Error
    }
}

function create_courses_mail_body($member_data)
{
    $enable_marketing_text = $member_data['$enable_marketing'] ? __('מאשר קבלת דואר') : __('לא מאשר קבלת דואר');

    $mail_body = '<p>';
    $mail_body .= 'querystring__UserEmail: ' . $member_data['phone'];
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
    $mail_body .= 'querystring__Custom10: ' . $enable_marketing_text;
    $mail_body .= '</p>';

    return $mail_body;
}


function update_course_member()
{
    require 'courses-members-update.php';
}

function get_courses_list()
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
                array_push($courses_list, $course_name);
            } else {
                $course_name = get_field('course_name_html', $id);
                // old course tamplate - html
                if (!empty($course_name)) {
                    // new course template
                    array_push($courses_list, $course_name);
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
