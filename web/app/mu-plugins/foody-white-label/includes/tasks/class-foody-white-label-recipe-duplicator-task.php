<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */
class Foody_WhiteLabelRecipeDuplicatorProcess extends WP_Background_Process
{
    /**
     * @var string
     */
    protected $action = 'foody_wl_recipe_duplicator';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over.
     *
     * @return mixed
     * @throws Exception
     */
    protected function task($item)
    {
        try {
            $recipes_ids = $item['recipes_ids'];

            Foody_WhiteLabelLogger::info("starting task {$this->action}", $item);

            $sites_to_copy_to = get_field('sites_for_recipe', $recipes_ids);

            if (!empty($sites_to_copy_to)) {

                foreach ($sites_to_copy_to as $site_to_copy_to) {
                    $blog_id = $site_to_copy_to['foody_sites'];
                    $copied_to_key = "copied_to_$blog_id";
                    $copied = get_term_meta($recipes_ids, $copied_to_key, true);
                    if (empty($copied)) {
                        $post = get_post($recipes_ids);
                        $result = Foody_WhiteLabelDuplicator::duplicate( $post, $blog_id, $site_to_copy_to );

                        if ($result) {
                            update_term_meta($recipes_ids, $copied_to_key, true);
                        }
                        //}
                    }
                }
            }
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::exception($e);
        }

        return false;
    }
}