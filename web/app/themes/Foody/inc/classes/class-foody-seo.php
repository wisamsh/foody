<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/31/19
 * Time: 4:45 PM
 */

class Foody_Seo
{

    /**
     * Foody_Seo constructor.
     */
    public function __construct()
    {

    }

    public static function seo()
    {
        if (empty($_REQUEST['page']) && empty(get_query_var('page', ''))) {
            $post_id = get_the_ID();
            $object_id = get_queried_object_id();
            if (is_author()) {
                $post_id = "user_$object_id";
            } elseif (is_category() || is_tag()) {
                $tax = is_category() ? 'category' : 'post_tag';
                $post_id = "{$tax}_$object_id";
            }


            $seo_text = get_field('seo_text', $post_id);

            if (!empty($seo_text)) {

                $seo_text = foody_normalize_content($seo_text);
                ?>
                <section class="seo" id="seo">
                    <?php echo $seo_text ?>
                </section>
                <?php

            }
        }
    }

}