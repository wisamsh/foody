<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 4:38 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$settings = $template_args;

$rating     = $settings['value'];
$disabled   = ! empty( $settings['disabled'] );
$hide_title = ! empty( $settings['hide_title'] );

$size       = foody_get_array_default( $settings, 'size', 'data-size="md"' );
$show_value = foody_get_array_default( $settings, 'show_value', false ) && $rating > 0;

?>

<?php if ( ! $hide_title ): ?>
    <h4>
        דרגו את המנה
    </h4>
<?php endif; ?>
<div>
    <!--    <input class="foody-rating" data-show-clear="false" data-show-caption="false" data-theme="krajee-svg"-->
    <!--           data-disabled="--><?php //echo $disabled ?><!--" --><?php //echo $size ?>
    <!--           type="text" value="--><?php //echo $rating ?><!--"/>-->
    <!--    --><?php //if ($show_value): ?>
    <!--        <span class="value">-->
    <!--            --><?php //echo number_format($rating, 1) ?>
    <!--        </span>-->
    <!--    --><?php //endif; ?>


    <?php Foody_Recipe::ratings() ?>
</div>
