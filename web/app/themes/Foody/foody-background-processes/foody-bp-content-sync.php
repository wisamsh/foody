<?php

require_once  get_template_directory().'/foody-background-processes/class-foody-commercial-rules-mapping-task.php';
require_once  get_template_directory().'/foody-background-processes/class-foody-bit-server-query-task.php';

/** @var Foody_CommercialRulesMappingProcess $add_commercial_mappings_rules_process */
global $add_commercial_mappings_rules_process;

/** @var Foody_CommercialRulesMappingProcess $add_query_process_for_bit_status */
global $add_query_process_for_bit_status;

add_action('init', 'foody_init_bp_async_tasks');
/**
 * Initializes async operations that take place
 * on blog creation and content edit.
 * Hooked into 'plugins_loaded'
 * @see WP_Async_Task
 */
function foody_init_bp_async_tasks()
{
    try {
        global $add_commercial_mappings_rules_process;
        $add_commercial_mappings_rules_process = new Foody_CommercialRulesMappingProcess();

        global $add_query_process_for_bit_status;
        $add_query_process_for_bit_status = new Foody_BitServerQueryProcess();
    } catch (Exception $e) {
        Foody_WhiteLabelLogger::exception($e);
    }
}

function foody_add_commercial_mappings_rules($post_id, $post, $update)
{
    if ($post->post_status !== 'publish') {
        return;
    }
    global $add_commercial_mappings_rules_process;
    try {
        $add_commercial_mappings_rules_process
            ->push_to_queue(['post_id' => $post_id, 'post' => $post, 'update' => $update])
            ->save()
            ->dispatch();
    } catch (Exception $e) {
        Foody_WhiteLabelLogger::exception($e);
    }
}

add_action('save_post_foody_comm_rule', 'foody_add_commercial_mappings_rules', 10, 3);
add_action('save_post_foody_recipe', 'foody_add_commercial_mappings_rules', 10, 3);
add_action('save_post_foody_feed_channel', 'foody_add_commercial_mappings_rules', 10, 3);



function foody_query_process_for_bit_status($payment_initiation_id, $member_data, $coupon_details)
{
    global $add_query_process_for_bit_status;
    try {
        $add_query_process_for_bit_status
            ->push_to_queue(['payment_initiation_id' => $payment_initiation_id, 'member_data' => $member_data, 'coupon_details' => $coupon_details])
            ->save()
            ->dispatch();
    } catch (Exception $e) {
        Foody_WhiteLabelLogger::exception($e);
    }
}