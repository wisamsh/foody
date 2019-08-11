<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 5:33 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$buttons = $template_args['buttons'];
$classes = $template_args['classes'];
?>

<ul class="purchase-buttons nolist row <?php echo $classes ?>">

	<?php foreach ( $buttons as $button ) : ?>
        <li class="purchase-button-container col-6 col-lg-auto">
            <a href="<?php echo $button['link']['url'] ?>"
               target="<?php echo ! empty( $button['link']['target'] ) ? $button['link']['target'] : '_blank' ?>">
				<?php Foody_PurchaseButtons::the_button( $button ) ?>
            </a>
        </li>
	<?php endforeach; ?>

</ul>
