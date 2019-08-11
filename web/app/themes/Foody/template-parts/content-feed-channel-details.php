<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/25/19
 * Time: 4:28 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$feed_channel = $template_args['feed_channel'];

?>

<section class="feed-channel-details row">
    <section class="socials">
		<?php foody_get_template_part( get_template_directory() . '/template-parts/content-social-actions.php' ) ?>
    </section>
    <section class="follow">
		<?php
		foody_get_template_part(
			get_template_directory() . '/template-parts/content-follow-button.php',
			[ 'topic' => $feed_channel ]
		);
		?>
    </section>
</section>
