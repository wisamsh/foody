<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 4:49 PM
 */
class Foody_HowIDid
{


    /**
     * Foody_HowIDid constructor.
     */
    public function __construct()
    {
    }


    public function get_comments()
    {
        return get_comments($this->get_args());
    }


    public function get_args()
    {
        $args =
            array(
                'type' => 'how_i_did',
                'number' => 3,
                'walker' => new Foody_HowIDidWalker(),
                'post_id' => get_the_ID()
            );

        return $args;
    }

    /**
     */
    public function the_comments()
    {

        $comments = get_comments(
            array(
                'type__not_in' => array('comment', 'pings'),
                'type' => 'how_i_did',
                'number' => 3
            )
        );

        foreach ($comments as $comment) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-comment-how-i-did.php', $comment);
        }
    }

    public function the_title()
    {
        $foody_comment_count = get_comments(array('count' => true, 'type' => 'how_i_did'));

        printf(
        /* translators: 1: comment count number, 2: title. */
            esc_html(_nx('תיראו מה יצא לי (%s)', 'תיראו מה יצא לי (%s)', $foody_comment_count, 'comments title', 'foody')),
            number_format_i18n($foody_comment_count)
        );
    }

    public function the_comments_form()
    {
    }
}