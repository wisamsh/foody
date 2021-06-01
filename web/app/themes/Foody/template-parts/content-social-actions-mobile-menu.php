<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 1:16 PM
 */

$buttons = ['gmail', 'pinterest', 'whatsapp', 'facebook', 'print'];


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

?>


<div class=" social col">
    <?php echo $social_icons ?>


    <?php
//    if (isset($template_args['extra_content'])) {
//
//        echo $template_args['extra_content'];
//    }

    ?>
</div>

