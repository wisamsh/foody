<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 5:33 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$buttons = $template_args['buttons'];
$classes = $template_args['classes'];


?>

<ul class="purchase-buttons nolist row <?php echo $classes ?>">

    <?php foreach ($buttons as $button) :

        $analytics_label = foody_get_array_default(
            $button,
            'analytics_label',
            foody_get_array_default($button, 'title', '')
        );


        $analytics_attr = '';
        if (!empty($analytics_label)) {
            $analytics_attr = "data-analytics='$analytics_label'";
        }

        if (isset($button['link']) && isset($button['link']['url']) && isset($button['link']['target'])){
        ?>
        <li class="purchase-button-container col-6 col-lg-auto" <?php echo $analytics_attr ?>>
            <a href="<?php echo $button['link']['url'] ?>"
               target="<?php echo !empty($button['link']['target']) ? $button['link']['target'] : '_blank' ?>">
                <?php Foody_PurchaseButtons::the_button($button) ?>
            </a>
        </li>
    <?php } ?>
	<?php endforeach; ?>

</ul>
