<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 10:14 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$image        = $template_args['image'];
$mobile_image = foody_get_array_default( $template_args, 'mobile_image', $image );

$name = isset( $template_args['name'] ) ? $template_args['name'] : '';
if ( empty( $name ) ) {
	$name = isset( $template_args['title'] ) ? $template_args['title'] : '';
}

$target = isset( $template_args['target'] ) ? $template_args['target'] : '_self';

?>

<a href="<?php /** @noinspection PhpUndefinedVariableInspection */
echo $template_args['link']; ?>" target="<?php echo $target ?>" class="col">
    <div class="category-listing">
        <div class="image-container">
            <picture class="lazyload">
                <source media="(min-width: 415px)" data-srcset="<?php echo $image; ?>"
                        srcset="<?php echo $GLOBALS['images_dir'] . 'category-placeholder.svg' ?>">
                <source media="(max-width: 414px)"
                        srcset="<?php echo $GLOBALS['images_dir'] . 'category-placeholder.svg' ?>"
                        data-srcset="<?php echo $mobile_image; ?>">
                <img class="lazyload" data-foody-src="<?php echo $image ?>"
                     src="<?php echo $GLOBALS['images_dir'] . 'category-placeholder.svg' ?>">
            </picture>
        </div>

        <h2 class="categort-listing-title"><?php echo $name; ?></h2>
    </div>
</a>
