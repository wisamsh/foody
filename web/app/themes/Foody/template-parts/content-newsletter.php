<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/16/19
 * Time: 6:29 PM
 */

$input_classes = foody_get_array_default($template_args,'input_classes','col-9 col-lg-7');
$button_classes = foody_get_array_default($template_args,'button_classes','col-3 col-lg-3 offset-lg-1');
?>



<h4 class="newsletter-title">
    <?php echo __('אל תפספסו את המתכונים החמים!', 'foody'); ?>
</h4>

<section class="newsletter">
    <form class="row justify-content-between" method="post">
        <div class="input-container <?php echo $input_classes?>">
            <input type="email" placeholder="<?php echo __('הכנס כתובת מייל', 'foody') ?>">

        </div>
        <button type="submit" class="<?php echo $button_classes?>">
            <?php echo __('הרשמה', 'foody') ?>
        </button>
    </form>
</section>
