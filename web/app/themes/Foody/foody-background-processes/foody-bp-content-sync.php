<?php

require_once  get_template_directory().'/foody-background-processes/class-foody-commercial-rules-mapping-task.php';

/** @var Foody_CommercialRulesMappingProcess $add_commercial_mappings_rules_process */
global $add_commercial_mappings_rules_process;

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