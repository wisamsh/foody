<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 2/17/19
 * Time: 11:38 AM
 */

?>

<div id="password-lost-form" class="widecolumn">
    <?php if ($attributes['show_title']) : ?>
        <h3><?php _e('Forgot Your Password?', 'personalize-login'); ?></h3>
    <?php endif; ?>

    <p>
        <?php
        _e(
            "הזן כתובת מייל לשליחת לינק איפוס סיסמא",
            'foody'
        );
        ?>
    </p>

    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
        <p class="form-row">
            <label for="user_login"><?php _e('אימייל', 'foody'); ?>
                <input type="text" name="user_login" id="user_login">
        </p>

        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button"
                   value="<?php _e('שלח', 'foody'); ?>"/>
        </p>
    </form>
</div>
