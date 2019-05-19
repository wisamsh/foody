<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 6:04 PM
 */

class Foody_Mailer {


	public static function send( $subject, $body, $to, $is_html = true ) {
		$headers = [];
		if ( $is_html ) {
			$registration_page = get_page_by_title( 'הרשמה' );

			$attachment   = get_field( 'campaign_mail_attachment', $registration_page );
			$use_template = get_field( 'campaign_mail_use_template', $registration_page );

			if ( ! $use_template ) {
				if ( empty( $attachment ) ) {
					$attachment = 'https://s3-eu-west-1.amazonaws.com/foody-media/FOODY-Passover-Ebook-2019.pdf';
				} else {
					$attachment = $attachment['url'];
				}

				$image     = get_field( 'campaign_mail_image', $registration_page );
				$image_alt = '';
				if ( empty( $image ) ) {
					$image_alt = __( 'ספר מתכונים לפסח' );
					$image     = $GLOBALS['images_dir'] . 'e-book-email-desktop.png';
				} else {
					$image_alt = $image['alt'];
					$image     = $image['url'];
				}

				$body = foody_get_template_part( get_template_directory() . "/email-templates/$body.php",
					[
						'subject'    => $subject,
						'return'     => true,
						'attachment' => $attachment,
						'image'      => $image,
						'image_alt'  => $image_alt
					] );
			} else {
				$body = get_field( 'campaign_mail_template', $registration_page );
			}

			$headers[]                        = 'Content-Type: text/html; charset=UTF-8';
			$GLOBALS["use_html_content_type"] = true;
		}

		wp_mail( $to, $subject, $body, $headers );
	}
}