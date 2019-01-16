<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/16/19
 * Time: 10:53 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Topic $topic */
$topic = $template_args['topic'];
$is_followed = false;
global $wp_session;
if (isset($wp_session["followed_{$topic->get_type()}"])) {

    $followed = $wp_session["followed_{$topic->get_type()}"];

    if ($followed && is_array($followed)) {
        if (in_array($topic->get_id(), $followed)) {
            $is_followed = true;
        }
    }

}

$follow_btn_class = 'btn btn-primary btn-follow';
if ($is_followed) {
    $follow_btn_class .= ' followed';
}

if(!empty($template_args['classes'])){
    $classes =$template_args['classes'];
    $follow_btn_class .= " $classes";
}


$follow_btn_text = $is_followed ? __('עוקב') : __('עקוב');
?>

<button class="<?php echo $follow_btn_class ?>"
        data-id="<?php echo $topic->get_id() ?>"
        data-followed="<?php echo $is_followed ? 'true' : 'false' ?>"
        data-topic="followed_<?php echo $topic->get_type() ?>">
    <i class="icon-Shape"></i>
    <span>
                    <?php echo $follow_btn_text ?>
                </span>
</button>
