<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 6:38 PM
 */
class Foody_HowIDidWalker extends Walker_Comment
{

    public $tree_type = 'how_i_did';
    public $db_fields = array('parent' => 'comment_parent', 'id' => 'comment_ID');

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $GLOBALS['comment_depth'] = $depth + 1;
        switch ($args['style']) {
            case 'div':
                break;
            case 'ol':
                $output .= '<ol class="children">' . "\n";
                break;
            case 'ul':
            default:
                $output .= '<ul class="children">' . "\n";
                break;
        }
    }

    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        $GLOBALS['comment_depth'] = $depth + 1;
        switch ($args['style']) {
            case 'div':
                break;
            case 'ol':
                $output .= "</ol><!-- .children -->\n";
                break;
            case 'ul':
            default:
                $output .= "</ul><!-- .children -->\n";
                break;
        }
    }

    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        if (!$element)
            return;
        $id_field = $this->db_fields['id'];
        $id = $element->$id_field;
        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
        /*
         * If at the max depth, and the current element still has children, loop over those
         * and display them at this level. This is to prevent them being orphaned to the end
         * of the list.
         */
        if ($max_depth <= $depth + 1 && isset($children_elements[$id])) {
            foreach ($children_elements[$id] as $child)
                $this->display_element($child, $children_elements, $max_depth, $depth, $args, $output);
            unset($children_elements[$id]);
        }
    }

    public function start_el(&$output, $comment, $depth = 0, $args = array(), $id = 0)
    {
        $depth++;
        $GLOBALS['comment_depth'] = $depth;
        $GLOBALS['comment'] = $comment;
        if (!empty($args['callback'])) {
            ob_start();
            call_user_func($args['callback'], $comment, $args, $depth);
            $output .= ob_get_clean();
            return;
        }
        if (('pingback' == $comment->comment_type || 'trackback' == $comment->comment_type) && $args['short_ping']) {
            ob_start();
            $this->ping($comment, $depth, $args);
            $output .= ob_get_clean();
        } elseif ('html5' === $args['format']) {
            ob_start();
            $this->html5_comment($comment, $depth, $args);
            $output .= ob_get_clean();
        } else {
            ob_start();
            $this->comment($comment, $depth, $args);
            $output .= ob_get_clean();
        }
    }

    public function end_el(&$output, $comment, $depth = 0, $args = array())
    {
        if (!empty($args['end-callback'])) {
            ob_start();
            call_user_func($args['end-callback'], $comment, $args, $depth);
            $output .= ob_get_clean();
            return;
        }
        if ('div' == $args['style'])
            $output .= "</div><!-- #comment-## -->\n";
        else
            $output .= "</li><!-- #comment-## -->\n";
    }

    protected function ping($comment, $depth, $args)
    {
        $tag = ('div' == $args['style']) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('', $comment); ?>>
        <div class="comment-body">
            <?php _e('Pingback:'); ?><?php comment_author_link($comment); ?><?php edit_comment_link(__('Edit'), '<span class="edit-link">', '</span>'); ?>
        </div>
        <?php
    }

    protected function comment($comment, $depth, $args)
    {
        if ('div' == $args['style']) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        ?>
        <<?php echo $tag; ?><?php comment_class($this->has_children ? 'parent' : '', $comment); ?> id="comment-<?php comment_ID(); ?>">
        <?php if ('div' != $args['style']) : ?>
        <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <?php endif; ?>
        <div class="comment-author vcard">
            <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
            <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link($comment)); ?>
        </div>
        <?php if ('0' == $comment->comment_approved) : ?>
        <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
        <br/>
    <?php endif; ?>

        <div class="comment-meta commentmetadata"><a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                <?php
                /* translators: 1: comment date, 2: comment time */
                printf(__('%1$s at %2$s'), get_comment_date('', $comment), get_comment_time()); ?></a><?php edit_comment_link(__('(Edit)'), '&nbsp;&nbsp;', '');
            ?>
        </div>

        <?php comment_text($comment, array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>

        <?php
        comment_reply_link(array_merge($args, array(
            'add_below' => $add_below,
            'depth' => $depth,
            'max_depth' => $args['max_depth'],
            'before' => '<div class="reply">',
            'after' => '</div>'
        )));
        ?>

        <?php if ('div' != $args['style']) : ?>
        </div>
    <?php endif; ?>
        <?php
    }

    protected function html5_comment($comment, $depth, $args)
    {


        $attachment_id = get_comment_meta(get_comment_ID(), 'attachment',true);

        $image = wp_get_attachment_url($attachment_id);


        $args['image'] = $image;
        $template_args = array_merge($args, array(
            $comment
        ));

        foody_get_template_part(get_template_directory() . '/template-parts/content-comment-how-i-did.php', $template_args);
    }

}