<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/23/18
 * Time: 4:00 PM
 */
?>


<form id="password-reset" action="">


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
        <input type="password" id="password-confirmation" aria-describedby="password-help" name="password-confirmation"
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

    <div class="form-group form-submit col-12">
        <button type="submit" class="btn btn-primary" aria-label="שיחזור סיסמא">
			<?php echo __( 'שיחזור סיסמא' ) ?>
        </button>
    </div>

</form>