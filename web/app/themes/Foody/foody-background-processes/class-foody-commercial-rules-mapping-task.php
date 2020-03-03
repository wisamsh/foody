<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */
class Foody_CommercialRulesMappingProcess extends WP_Background_Process
{
    /**
     * @var string
     */
    protected $action = 'foody_commercials_rules_mapping_process';

    /**
     * /**
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
            $post_id = $item['post_id'];
            $post = $item['post'];
            $update = $item['update'];

            switch ($post->post_type) {
                case 'foody_comm_rule':
                    $this->foody_save_commercial_rule_mapping($post_id, $post, $update);
                    break;
                case 'foody_recipe':
                    $this->foody_save_post_for_commercial_rule_mapping($post_id, $post, $update);
                    break;
            }
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::exception($e);
        }

        return false;
    }

    /**
     * @param $rule_id
     * @param $rule
     * @param $update
     *
     * @throws Exception
     */
    function foody_save_commercial_rule_mapping($rule_id, $rule, $update)
    {

        if ($rule->post_type != 'revision' && $rule->post_status != 'auto-draft') {
            $this->foody_save_commercial_rule_mapping_for_rule($rule_id);
        }
    }

    function foody_save_post_for_commercial_rule_mapping($post_id, $post, $update)
    {

        if ($post->post_type != 'revision' && $post->post_status != 'auto-draft') {
            $query = new WP_Query(array(
                'post_type' => 'foody_comm_rule',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ));


            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $this->foody_save_commercial_rule_mapping_for_rule($post_id);
            }

            wp_reset_query();
        }
    }


    /**
     * @param $rule_id
     *
     * @throws Exception
     */
    function foody_save_commercial_rule_mapping_for_rule($rule_id)
    {

        // Clear old rules
        Foody_CommercialRuleMapping::removeRules($rule_id);

        // if delete commercial rule
        if (isset($_REQUEST) && (isset($_REQUEST['action']) && $_REQUEST['action'] == 'trash')) {
            return;
        }
        $posts = [];

        if (get_field('type', $rule_id) == 'area') {
            // Area rule

            $areas = get_field('comm_rule_area', $rule_id);

            // loop over areas to fetch relevant recipes
            foreach ($areas as $area) {
                $foody_search = new Foody_Search('feed_channel');
                $foody_query = Foody_Query::get_instance();
                $blocks_drawer = new Foody_Blocks($foody_search);

                $blocks = get_field('blocks', $area->ID);

                if (!empty($blocks)) {

                    foreach ($blocks as $block) {
                        $type = $block['type'];

                        if (!empty($type)) {
                            if ($type == 'dynamic') {
                                $blocks_drawer->validate_block($block);

                                $block_fn = "get_{$type}_block_posts";
                                if (method_exists($blocks_drawer, $block_fn)) {
                                    $block_posts = call_user_func([$blocks_drawer, $block_fn], $block);
                                    if (!empty($block_posts)) {
                                        array_filter($block_posts, function ($a_post) {
                                            return $a_post->post_type == 'foody_recipe';
                                        });
                                        $posts = array_merge($posts, $block_posts);
                                    }
                                }
                            } else if ($type == 'manual') {
                                if (!empty($block['items'])) {
                                    $block_posts = [];
                                    foreach ($block['items'] as $item) {
                                        if (!empty($item) && !empty($item['post']) && $item['post']->post_type == 'foody_recipe') {
                                            array_push($block_posts, $item['post']);
                                        }
                                    }
                                    $posts = array_merge($posts, $block_posts);
                                }
                            }
                        }
                    }
                }

                //
            }
        } else if (get_field('type', $rule_id) == 'filter') {
            // Filter rule

            $filter = get_field('comm_rule_filter', $rule_id);


            $filters = [];

            // consider all filters lists
            foreach ($filter['filters_list'] as $list) {
                if (is_array($list)) {
                    $filters = array_merge($filters, $list);
                }
            }

            $args = [
                'types' => SidebarFilter::parse_search_args($filters)
            ];

            // purchase_buttons will invoke purchase_buttons ffn
            // in class Foody_Query
            $foody_search = new Foody_Search('foody_commercial_rule', $filters);

            $result = $foody_search->query($args);
            // Execute Rule
            $posts = array_merge($posts, $result['posts']);
        }

        $object_type = get_field('object_type', $rule_id);
        $object = get_field('object', $rule_id);

        $posts = array_unique($posts, SORT_REGULAR);

        // Save new rule mapping - TODO: foreach recipe id


        $values = [];

        foreach ($posts as $post) {
            array_push($values, [
                'rule_id' => $rule_id,
                'recipe_id' => $post->ID,
                'object_id' => $object->ID
            ]);
        }

        Foody_CommercialRuleMapping::addMany($values);
    }
}