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


$buttons_attr = implode(',', $buttons);

$social_icons = do_shortcode('[easy-social-share buttons="' . $buttons_attr . '" template="11" counters=0 style="icon" point_type="simple"]');

/** @noinspection PhpUndefinedVariableInspection */
$show_rating = !isset($template_args['hide_rating']) || $template_args['hide_rating'] == false;

?>


<div class=" social col">
    <?php echo $social_icons ?>

    <?php if (wp_is_mobile()): ?>
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

