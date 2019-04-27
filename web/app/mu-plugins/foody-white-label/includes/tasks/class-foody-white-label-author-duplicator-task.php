<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */

class Foody_WhiteLabelAuthorDuplicatorTask extends WP_Async_Task
{

    protected $action = 'edit_user_profile_update';

    /**
     * Prepare any data to be passed to the asynchronous postback
     *
     * The array this function receives will be a numerically keyed array from
     * func_get_args(). It is expected that you will return an associative array
     * so that the $_POST values used in the asynchronous call will make sense.
     *
     * The array you send back may or may not have anything to do with the data
     * passed into this method. It all depends on the implementation details and
     * what data is needed in the asynchronous postback.
     *
     * Do not set values for 'action' or '_nonce', as those will get overwritten
     * later in launch().
     *
     * @throws Exception If the postback should not occur for any reason
     *
     * @param array $data The raw data received by the launch method
     *
     * @return array The prepared data
     */
    protected function prepare_data($data)
    {
        return [
            'user_id' => $data[0]
        ];
    }

    /**
     * Run the do_action function for the asynchronous postback.
     *
     * This method needs to fetch and sanitize any and all data from the $_POST
     * superglobal and provide them to the do_action call.
     *
     * The action should be constructed as "wp_async_task_$this->action"
     */
    protected function run_action()
    {
        $duplicatedRoles = [
            'author'
        ];

        $user_id = $_POST['user_id'];

        $user = get_user_by('ID', $user_id);


        // if this taxonomy can be duplicated
        if (count(array_intersect($user->roles, $duplicatedRoles)) > 0) {
            $sites = get_sites(['fields' => 'ids']);

            foreach ($sites as $site) {
                $term_duplication_key = "pass_data_$site";
                $copy = get_user_meta($user_id, $term_duplication_key, true);

                if (!empty($copy)) {
                    $copied_to_key = "copied_to_$site";
                    $copied = get_term_meta($user_id, $copied_to_key, true);

                    // if not already copied
                    if (empty($copied)) {
                        $result = Foody_WhiteLabelDuplicator::duplicateAuthor($user_id, $site);
                        if (!empty($result)) {
                            update_user_meta($user_id, $copied_to_key, true);
                        }
                    }
                }
            }
        }
    }
}