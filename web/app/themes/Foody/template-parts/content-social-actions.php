<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 1:16 PM
 */

$buttons = ['gmail', 'pinterest', 'whatsapp', 'facebook'];


if (!wp_is_mobile()) {
    array_unshift($buttons, 'print');
}
/** @noinspection PhpUndefinedVariableInspection */
if (isset($template_args['exclude']) && is_array($template_args['exclude'])) {
    $buttons_tmp = [];
    foreach ($buttons as $button) {
        if (!in_array($button, $template_args['exclude'])) {
            $buttons_tmp[] = $button;
        }
    }

    $buttons = $buttons_tmp;

}

$buttons_attr = implode(',', $buttons);

$social_icons = do_shortcode('[easy-social-share buttons="' . $buttons_attr . '" template="11" counters=0 style="icon" point_type="simple"]');


$show_rating = !isset($template_args['hide_rating']) || $template_args['hide_rating'] == false;

?>


<div class=" social col">
    <?php if (is_single()): ?>
        <div class="description social-title">
            <?php
            if (get_post_type() == 'foody_recipe') {
                echo __('שתפו את המתכון');
            } else if (get_post_type() == 'post') {
                echo __('שתפו את הכתבה');
            } else if (get_post_type() == 'foody_feed_channel') {
                echo __('שתפו את הערוץ');
            } else if (get_post_type() == 'foody_course') {
                echo __('שתפו את הקורס');
            }
            ?>
        </div>
    <?php if(wp_is_mobile()) { ?>
        <div class="kosher-sign">
            <?php echo __('כשר'); ?>
        </div>
    <?php } ?>
    <?php endif; ?>
    <?php echo $social_icons ?>
    <?php if(!wp_is_mobile()) { ?>
        <div class="kosher-sign">
            <?php echo __('כשר'); ?>
        </div>
    <?php } ?>

    <?php if (wp_is_mobile() && is_single() && in_array(get_post_type(), ['foody_recipe', 'post'])): ?>
        <section class="d-block d-lg-none">
            <?php Foody_Recipe::ratings() ?>
        </section>
    <?php endif; ?>


    <?php
    if (isset($template_args['extra_content'])) {

        echo $template_args['extra_content'];
    }

    ?>
</div>

