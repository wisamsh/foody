<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/16/19
 * Time: 6:29 PM
 */

$input_classes  = foody_get_array_default( $template_args, 'input_classes', 'col-9 col-lg-7' );
$button_classes = foody_get_array_default( $template_args, 'button_classes', 'col-3 col-lg-3 offset-lg-1' );
$title          = isset( $template_args['title'] ) ? $template_args['title'] : 'אל תפספסו את המתכונים החמים!';

?>


<div class="newsletter-title">
	<?php echo __( $title, 'foody' ); ?>
</div>

<section class="newsletter">
	<?php echo do_shortcode( '[contact-form-7 id="3101" title="ניוזלטר"]' ) ?>
    <!--    <form class="row justify-content-between" method="post">-->
    <!--        <div class="input-container --><?php //echo $input_classes?><!--">-->
    <!--            <input type="email" placeholder="--><?php //echo __('הכנס כתובת מייל', 'foody') ?><!--">-->
    <!---->
    <!--        </div>-->
    <!--        <button type="submit" class="--><?php //echo $button_classes?><!--">-->
    <!--            --><?php //echo __('הרשמה', 'foody') ?>
    <!--        </button>-->
    <!--    </form>-->
</section>
