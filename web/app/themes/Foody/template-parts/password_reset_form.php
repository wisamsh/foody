<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 2/17/19
 * Time: 11:55 AM
 */

$error = isset( $_REQUEST['error'] );

?>

<div id="password-reset-form" class="widecolumn">

    <h3>
		<?php
		_e( 'בחר/י סיסמא חדשה', 'foody' );
		?>
    </h3>

	<?php if ( $error ): ?>
        <p class="error" style="color: red;">
			<?php
			_e( 'אנא ודא/י שהסיסמאות תקינות ותואמות', 'foody' );
			?>
        </p>
	<?php endif; ?>


    <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>"
          method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>"
               autocomplete="off"/>
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>"/>

        <p>
            <label for="pass1"><?php _e( 'סיסמא חדשה', 'foody' ) ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off"/>
        </p>
        <p>
            <label for="pass2"><?php _e( 'ווידוא סיסמא חדשה', 'foody' ) ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off"/>
        </p>

        <p class="resetpass-submit">
            <button type="submit" name="submit" id="resetpass-button"
                    class="btn btn-primary">
				<?php _e( 'איפוס', 'foody' ); ?>
            </button>
        </p>
    </form>
</div>
