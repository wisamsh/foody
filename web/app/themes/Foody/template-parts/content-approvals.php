<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/7/19
 * Time: 8:13 PM
 */

$approved_marketing = get_user_meta( get_current_user_id(), 'marketing', true );
$registration_page  = get_page_by_title( 'הרשמה' );
$show               = get_field( 'show', $registration_page );

$welcome_text = get_field( 'welcome_text', $registration_page );

$redirect = isset( $template_args['redirect'] );


if ( empty( $welcome_text ) ) {
	$welcome_text = __( 'איזה כיף! אתם כבר רשומים לאתר Foody! אשרו קבלת דיוור וספר מתכונים מפנק לפסח יחכה לכם במייל איתו נרשמתם', 'foody' );
}

?>
<section class="approvals-container">
    <h1 class="title">
		<?php echo get_the_title() ?>
    </h1>
    <div class="welcome-text">
		<?php echo $welcome_text; ?>
    </div>
    <form id="approvals" method="post">
		<?php if ( ! Foody_User::user_has_meta( 'marketing' ) ): ?>
            <div class="md-checkbox col-12">
                <input id="check-marketing" type="checkbox" checked name="marketing">
                <label for="check-marketing">
					<?php echo __( 'הריני לאשר בזה קבלת דואר מאתר Foody הכולל מתכונים ומידע מהאתר, וכן דואר שיווקי גם של מפרסמים הקשורים עם האתר' ) ?>
                </label>
            </div>
		<?php endif; ?>
		<?php

		if ( $show ):
			?>
            <div class="md-checkbox col-12">
                <input id="check-e-book" type="checkbox" checked name="e-book">
                <label for="check-e-book">
					<?php
					$text = get_field( 'text', $registration_page );
					if ( empty( $text ) ) {
						$text = __( 'ברצוני לקבל את ספר המתכונים לשבועות' );
					}
					echo $text;
					?>
                </label>
            </div>
		<?php endif; ?>
		<?php if ( $redirect ): ?>
            <input type="hidden" name="redirect" value="1">
		<?php endif; ?>
        <input class="btn btn-primary" type="submit" value="<?php echo __( 'סיום הרשמה' ) ?>">

    </form>
</section>
