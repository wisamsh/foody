<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/7/19
 * Time: 8:13 PM
 */

$registration_page = get_page_by_title( 'הרשמה' );

$welcome_text = get_field( 'extra_details_campaign_subtitle', $registration_page );
$terms_text   = get_field( 'extra_details_campaign_terms', $registration_page );
$button_text  = get_field( 'extra_details_campaign_button_text', $registration_page );

$redirect = isset( $template_args['redirect'] );


if ( empty( $welcome_text ) ) {
	$welcome_text = __( 'איזה כיף! אתם כבר רשומים לאתר Foody! אשרו קבלת דיוור וספר מתכונים מפנק לפסח יחכה לכם במייל איתו נרשמתם', 'foody' );
}
if ( empty( $button_text ) ) {
	$button_text = __( 'סיום הרשמה', 'foody' );
}

$today = date( 'Y-m-d' );

?>
<section class="campaign-approvals-container">
    <h1 class="title">
		<?php echo get_the_title() ?>
    </h1>
    <div class="welcome-text">
		<?php echo $welcome_text; ?>
    </div>
    <form class="row" id="campaign-approvals" method="post">
        <div class="form-group col-5 required-input">
            <label for="street">
				<?php echo __( 'רחוב', 'foody' ) ?>
            </label>
            <input type="text" id="street" name="street" required>
        </div>
        <div class="form-group col-2 required-input">
            <label for="street-number">
				<?php echo __( 'מספר', 'foody' ) ?>
            </label>
            <input type="text" id="street-number" name="street-number" required>
        </div>
        <div class="form-group col-5 required-input">
            <label for="city">
				<?php echo __( 'עיר', 'foody' ) ?>
            </label>
            <input type="text" id="city" name="city" required>
        </div>
        <div class="form-group col-5 required-input">
            <label for="birthday">
				<?php echo __( 'תאריך לידה', 'foody' ) ?>
            </label>
            <input dir="rtl" type="date" id="birthday" name="birthday" max="<?php echo $today ?>" required>
        </div>
        <div class="form-group col-7">
            <!-- Empty Cell -->
        </div>
        <div class="form-group col-12 required-input">
            <label for="gender">
				<?php echo __( 'מין', 'foody' ) ?>
            </label>
            <label class="custom-radio-container">זכר
                <input type="radio" id="gender-male" name="gender" required value="male">
                <span class="custom-radio"></span>
            </label>
            <label class="custom-radio-container">נקבה
                <input type="radio" id="gender-female" name="gender" required value="female">
                <span class="custom-radio"></span>
            </label>
            <label class="custom-radio-container">אחר
                <input type="radio" id="gender-other" name="gender" required value="other">
                <span class="custom-radio"></span>
            </label>
        </div>
        <!--		--><?php //if ( ! Foody_User::user_has_meta( 'extended-campaign-terms' ) ): ?>
        <div class="md-checkbox col-12">
            <input id="extended-campaign-terms" type="checkbox" checked name="extended-campaign-terms">
            <label for="extended-campaign-terms">
				<?php echo $terms_text ?>
            </label>
        </div>
        <!--		--><?php //endif; ?>

		<?php if ( $redirect ): ?>
            <input type="hidden" name="redirect" value="1">
		<?php endif; ?>
        <input class="btn btn-primary" type="submit" value="<?php echo $button_text ?>">

    </form>
</section>
