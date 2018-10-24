<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 2:34 PM
 */


function foody_ajax_load_more()
{

    $foody_query = Foody_Query::get_instance();
    $required = [
        'context',
        'page',
        'filter'
    ];

    $valid = foody_validate_post_required($required);

    if ($valid) {


        $filter = $_POST['filter'];
        if (!is_array($filter)) {
            $error = "invalid filter: " . strval($filter);
        } else {

            $context_args = [];
            if (isset($_POST['context_args'])) {
                $context_args = $_POST['context_args'];
                if (!is_array($context_args)) {
                    $context_args = [$context_args];
                }
            }
            $context = $_POST['context'];


            $page = $_POST['page'];

            $page_args = $foody_query->get_query($context, $context_args);

            if (is_wp_error($page_args)) {
                $error = $page_args->get_error_message();
            } else {

                unset($filter['context']);

                $foody_search = new Foody_Search();

                $sort = '';
                if(!empty($_POST['sort'])){
                    $sort = $_POST['sort'];
                    if(get_query_var('paged',null)){
                        unset($page_args['paged']);
                    }
                }

                $query = $foody_search->build_query($filter, $page_args,$sort);

                $next = $query->max_num_pages > $page;

                $foody_search->before_query();

                $posts = $query->get_posts();

                $foody_search->after_query();

                $grid = new FoodyGrid();

                $foody_posts = array_map('Foody_Post::create', $posts);

                $cols = foody_get_array_default($_POST, 'cols', 3);
                $cols = intval($cols);
                $items = $grid->loop($foody_posts, $cols, false);

                $response = [
                    'next' => $next,
                    'items' => $items
                ];

            }
        }

    } else {
        $error = 'bad request';
    }

    if (!empty($error)) {
        wp_send_json_error(['message' => $error], 400);
    } elseif (!isset($response)) {
        wp_send_json_error(['message' => 'unknown error']);
    } else {
        wp_send_json_success($response);
    }


}

add_action('wp_ajax_load_more', 'foody_ajax_load_more');
add_action('wp_ajax_nopriv_load_more', 'foody_ajax_load_more');