<?php
function get_coupon_value()
{

}
add_action('wp_ajax_foody_nopriv_get_coupon_value', 'foody_get_coupon_value');
add_action('wp_ajax_foody_get_coupon_value', 'foody_get_coupon_value');