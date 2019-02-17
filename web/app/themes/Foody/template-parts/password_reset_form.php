<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 2/17/19
 * Time: 11:55 AM
 */

?>

<div id="password-reset-form" class="widecolumn">
    <?php if ($attributes['show_title']) : ?>
        <h3><?php _e('בחר/י סיסמא חדשה', 'foody'); ?></h3>
    <?php endif; ?>

    <form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>"
          method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($attributes['login']); ?>"
               autocomplete="off"/>
        <input type="hidden" name="rp_key" value="<?php echo esc_attr($attributes['key']); ?>"/>

        <?php if (count($attributes['errors']) > 0) : ?>
            <?php foreach ($attributes['errors'] as $error) : ?>
                <p>
                    <?php echo $error; ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <p>
            <label for="pass1"><?php _e('סיסמא חדשה', 'foody') ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off"/>
        </p>
        <p>
            <label for="pass2"><?php _e('ווידוא סיסמא חדשה', 'foody') ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off"/>
        </p>

        <p class="description"><?php echo wp_get_password_hint(); ?></p>

        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button"
                   class="button" value="<?php _e('איפוס', 'foody'); ?>"/>
        </p>
    </form>
</div>
