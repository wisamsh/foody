<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/15/18
 * Time: 4:54 PM
 */
class FoodyTeam
{

    private static $debug = false;

    /**
     * FoodyTeam constructor.
     */
    public function __construct()
    {

    }

    public function list_authors($display_args = array())
    {

        $num_of_authors = $display_args['max'];

        if (sizeof($display_args) == 0) {
            $display_args = array(
                'display' => 'row',
                'show_count' => false
            );
        }

        $show_count = $display_args['show_count'];
        $display = $display_args['display'];

        $args = array(
            'role' => 'author',
            'orderby' => 'meta_value_num',
            'meta_key' => 'menu_order',
            'order' => 'ASC',
            'number' => $num_of_authors
        );

        $authors = get_users($args);
        $query_count = sizeof($authors);
        $debug_author = $authors[0];


        $content = '';
        switch ($display) {
            case 'grid':
                $row_container = '<div class="row team-grid-row">';
                $col_span = isset($display_args['grid_col_span']) ? intval($display_args['grid_col_span']) : 5;
                if ($col_span >= $query_count) {
                    $col_span = $query_count;
                }
                if (wp_is_mobile()) {
                    $col_span = 3;
                }

                if (self::$debug) {
                    $add = 17;
                    while ($add > 0) {
                        $authors[] = $debug_author;
                        $add--;
                    }
                }


                $rows = array_chunk($authors, $col_span);
                while (sizeof($rows[sizeof($rows) - 1]) < $col_span) {
                    $rows[sizeof($rows) - 1][] = null;
                }

                $order = 1;
                foreach ($rows as $row) {
                    $content .= $row_container;

                    foreach ($row as $author) {
                        $content .= $this->get_author_template($author, $show_count, $order);
                        $order++;
                    }
                    $content .= '</div>';
                }
                break;

            default:
                $order = 1;
                foreach ($authors as $author) {
                    $content .= $this->get_author_template($author, $show_count, $order);
                }

                if (self::$debug && sizeof($authors) < 7) {
                    $add = 7 - $query_count;
                    while ($add > 0) {
                        $content .= $this->get_author_template($debug_author, $show_count, $order);
                        $add--;
                    }
                }
                break;
        }

        return array(
            'content' => $content,
            'count' => $this->count_authors_more(!self::$debug ? $query_count : 1)
        );
    }

    public function team($disply_args = array())
    {

        $data = $this->list_authors($disply_args);
        $title = '<h3 class="title"><a href="' . get_permalink(get_page_by_path('הנבחרת')) . '" >';
        $title .= 'הנבחרת שלנו';
        $title .= '</a></h3>';
        if (!$disply_args['show_title']) {
            $title = '';
        }
        $sort = '';
        if (isset($disply_args['allow_sort']) && $disply_args['allow_sort']) {
            $select_args = array(
                'id' => 'team-sort',
                'placeholder' => 'סדר על פי',
                'options' => array(
                    array(
                        'value' => 1,
                        'label' => 'א-ת'
                    ),
                    array(
                        'value' => -1,
                        'label' => 'ת-א'
                    )
                ),
                'return' => true
            );
            $sort = '<div class="sort">' .
                foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $select_args) .
                '</div>';
        }

        $bootstrap_classes = 'row';
        if (isset($disply_args['display']) && $disply_args['display'] == 'grid') {
            $bootstrap_classes = '';
        }
        $container = '<div class="team-listing ' . $bootstrap_classes . '" data-count="' . $data['count'] . '" dir="rtl">';
        $content = $data['content'];
        $close = '</div>';


        return $title . $sort . $container . $content . $close;

    }


    public function count_authors_more($num_exclude)
    {

        $user_query = new WP_User_Query(array('role' => 'Author'));

        return (int)$user_query->get_total() - $num_exclude;
    }

    /**
     * @param $author WP_User author to display
     */
    private function get_author_template($author, $show_count = false, $order)
    {
        if ($author == null) {
            return '<div class="authorempty col" data-order="' . PHP_INT_MAX . '"></div>';
        }

        $user_avatars = get_the_author_meta('wp_user_avatars', $author->ID);

        if (is_null($user_avatars) || empty($user_avatars) || !isset($user_avatars[250])) {
            $image = get_avatar_url($author->ID, ['size' => 96]);
        } else {
            $image = $user_avatars['250'];
        }


        $name = get_the_author_meta('nickname', $author->ID);

        $author_data = array(
            'name' => $name,
            'image' => $image,
            'order' => $order,
            'return' => true
        );

        if ($show_count) {
            $author_data['post_count'] = foody_count_posts_by_user($author->ID);
        }

        return foody_get_template_part(get_template_directory() . '/template-parts/content-author-listing.php', $author_data);
    }
}