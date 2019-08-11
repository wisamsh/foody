<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/23/18
 * Time: 4:00 PM
 */


/** @noinspection PhpUndefinedVariableInspection */
$form_classes = foody_get_array_default( $template_args, 'form_classes', [] );
?>

<h3 class="title">
	<?php echo __( 'שינוי סיסמא', 'foody' ); ?>
</h3>

<form class="<?php foody_el_classes( $form_classes ) ?>" id="password-reset" novalidate
      action="" method="post">


    <div class="form-group col-12 required-input">
        <label for="current-password">
			<?php echo __( 'הזן סיסמא נוכחית', 'foody' ) ?>
        </label>
        <input type="password" id="current-password" name="current_password" required>
    </div>


    <div class="form-group col-12 required-input">
        <label for="password">
			<?php echo __( 'סיסמא חדשה', 'foody' ) ?>
        </label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group col-12 required-input">
        <label for="password-confirmation">
			<?php echo __( 'וידוא סיסמא', 'foody' ) ?>
        </label>
        <input type="password" id="password-confirmation" aria-describedby="password-help"
               name="password_confirmation"
               required>
    </div>

    <ul id="password-help" class="form-text text-muted">
        <li>
			<?php echo __( 'לפחות 8 תווים' ); ?>
        </li>
        <li>
			<?php echo __( 'תווים באנגלית בלבד' ); ?>
        </li>
        <li>
			<?php echo __( 'לפחות ספרה אחת' ); ?>
        </li>
    </ul>

    <div class="form-group form-submit col-12 row justify-content-between gutter-0">
        <ul class="nolist nav nav-tabs col-lg-4 col-5" id="change-tabs">
            <li>
                <a role="tab" data-toggle="tab"
                   href="#user-content" aria-controls="user-content">
                    <button type="button" class="btn btn-primary btn-cancel" aria-label="ביטול">
						<?php echo __( 'ביטול' ) ?>
                    </button>
                </a>
            </li>
        </ul>

        <button type="submit" name="submit_change_pass" class="btn btn-primary col-lg-4 col-5" aria-label="שינוי סיסמה">
			<?php echo __( 'שינוי סיסמא' ) ?>
        </button>
    </div>

</form>

