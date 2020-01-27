<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/25/19
 * Time: 4:07 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$image = $template_args['image'];
$link  = foody_get_array_default( $template_args, 'link', [ 'url' => '', 'target' => '' ] );
$title = $template_args['title'];

?>
<section class="foody-banner">
	<?php if ( ! empty( $link ) ): ?>
    <a href="<?php echo $link['url'] ?>" target="<?php echo $link['target'] ?>" data-banner-name='<?php echo $title; ?>'>
		<?php endif; ?>

        <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">

		<?php if ( ! empty( $link ) ): ?>
    </a>
<?php endif; ?>
</section>
