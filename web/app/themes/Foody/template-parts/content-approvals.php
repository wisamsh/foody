<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/7/19
 * Time: 8:13 PM
 */

$approved_marketing = get_user_meta( get_current_user_id(), 'marketing', true );
$registration_page  = get_page_by_title( 'הרשמה' ); //gets a post object 
$show               = get_field( 'show', $registration_page );
$GetHarshamaID = (get_page_by_title('הרשמה'));
$thispageID = get_the_ID();
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
             <div class="md-checkbox col-12" style="display:none;">
                <input id="check-marketing" type="checkbox" checked name="marketing">
                <label for="check-marketing">
					<?php //echo __( 'הריני לאשר בזה קבלת דואר מאתר Foody וחברת מזרח ומערב הכולל מתכונים ומידע מהאתר, וכן דואר שיווקי גם של מפרסמים הקשורים עם האתר' ) ?>
                </label>
            </div> 
		<?php endif; ?>
		


		<?php

		if ( $show ):
			?>
            <div class="md-checkbox col-12" id="content-app">
                <input id="check-e-book" type="checkbox" checked name="e-book">
                <label for="check-e-book">
					<?php
					$text = get_field( 'text', $GetHarshamaID->ID );
					if ( empty( $text ) ) {
						$text = __( 'ברצוני לקבל את ספר המתכונים לשבועות' );
					}
					echo $text;
					?>
                </label>
            </div>

		<?php endif; ?>
		<?php 
			
			if(is_user_logged_in() && ($thispageID != $GetHarshamaID->ID)){
				
			if(get_field('second_check_text', $GetHarshamaID->ID)){?>
				<div class="md-checkbox col-12">		
				<input id="ebook_sec" type="checkbox" checked name="ebook_sec"/>
						<label for="ebook_sec">
						<?php
						$text2 = get_field( 'second_check_text', $registration_page );
						
						echo $text2;
						?>
						</label>

			</div>

				<?php 
			}
			}?>	




		<?php if ( $redirect ): ?>
            <input type="hidden" name="redirect" value="1">
		<?php endif; ?>
        <input class="btn btn-primary" id="register_btn_ebook" type="submit" value="<?php echo __( 'לקבלת החוברת לחץ כאן' ) ?>">

    </form>
</section>
