<?php
function foody_add_course_member_to_table(){
    $custom_val = $_POST['data_of_member'];
}
add_action('wp_ajax_nopriv_add_course_member_to_table', 'foody_add_course_member_to_table');
add_action('wp_ajax_add_course_member_to_table
', 'foody_add_course_member_to_table');