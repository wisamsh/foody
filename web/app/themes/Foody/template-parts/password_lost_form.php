<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 2/17/19
 * Time: 11:38 AM
 */

$sent = isset($_GET['checkemail']) && $_GET['checkemail'] == 'confirm';
?>

<div id="password-lost-form" class="widecolumn">


    <p>
        <?php

        if ($sent){
            _e('אימייל נשלח לכתובת שצויינה. אנא בדוק/י את תיבת המייל הנכנס', 'foody');
        }else{
            _e(
                "הזן כתובת מייל לשליחת לינק איפוס סיסמא",
                'foody'
            );
        }
        ?>
    </p>

    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
        <p class="required-input">
            <label for="user_login"><?php _e('אימייל', 'foody'); ?>
            </label>
            <input type="text" name="user_login" id="user_login">
        </p>

        <p class="lostpassword-submit form-group">
            <button type="submit" name="submit" class="lostpassword-button btn btn-primary">
                <?php _e('שלח', 'foody'); ?>
            </button>
        </p>
    </form>
</div>
