<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */

class Foody_WhiteLabelAuthorDuplicatorProcess extends WP_Background_Process
{

    protected $action = 'foody_wl_author_duplicator';

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
     */
    protected function task($item)
    {
        Foody_WhiteLabelLogger::info("starting task {$this->action}", $item);

        $user_id = $item['user_id'];

        $user = get_user_by('ID', $user_id);

        $duplicatedRoles = [
            'author'
        ];

        // run duplication only for relevant user roles
        $user_has_relevant_role = count(array_intersect($user->roles, $duplicatedRoles)) > 0;
        if ($user_has_relevant_role) {

            // get custom field
            $sites_to_copy_to = get_field('sites', "user_$user_id");

            if (!empty($sites_to_copy_to)) {

                foreach ($sites_to_copy_to as $site_to_copy_to) {

                    // see ./acf.php for info about the
                    // 'foody_sites' key with acf select field
                    // and the dynamic population of the field
                    $blog_id = $site_to_copy_to['foody_sites'];

                    $copied_to_key = "copied_to_$blog_id";

                    $copied = get_user_meta($user_id, $copied_to_key, true);

                    // don't run if already copied
                    if (empty($copied)) {
                        $result = Foody_WhiteLabelDuplicator::duplicateAuthor($user_id, $blog_id, $site_to_copy_to);
                        if (!empty($result['success'])) {
                            update_user_meta($user_id, $copied_to_key, true);
                        }
                    }
                }

            }
        }


        return false;
    }
}