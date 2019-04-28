<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */

class Foody_WhiteLabelTermDuplicatorTask extends WP_Async_Task
{

    protected $action = 'edit_term';

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
            'term_id' => $data[0],
            'tt_id' => $data[1],
            'taxonomy' => $data[2]
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
        $duplicatedTerms = [
            'category',
            'post_tag'
        ];

        $term_id = $_POST['term_id'];
        $taxonomy = $_POST['taxonomy'];

        // if this taxonomy can be duplicated
        if (in_array($taxonomy, $duplicatedTerms)) {
            $sites = get_sites(['fields' => 'ids']);

            foreach ($sites as $site) {
                $term_duplication_key = "pass_data_$site";
                $copy = get_term_meta($term_id, $term_duplication_key, true);

                if (!empty($copy)) {
                    $copied_to_key = "copied_to_$site";
                    $copied = get_term_meta($term_id, $copied_to_key, true);

                    // if not already copied
                    if (empty($copied)) {
                        if ($taxonomy == 'post_tag') {
                            $result = Foody_WhiteLabelDuplicator::duplicateTag($term_id, $site);
                        } elseif ($taxonomy == 'category') {
                            $result = Foody_WhiteLabelDuplicator::duplicateCategory($term_id, $site);
                        }

                        if (!empty($result)) {
                            update_term_meta($term_id, $copied_to_key, true);
                        }
                    }
                }
            }
        }
    }
}