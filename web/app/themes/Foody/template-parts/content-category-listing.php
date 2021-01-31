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
$disable_referrer = isset($template_args['disable_referrer']) && $template_args['disable_referrer'] ? 1 : 0;

$linked_area_id = '';
$linked_area_name = '';
if($disable_referrer && strpos($template_args['link'], 'areas')){
    $linked_area_id = url_to_postid($template_args['link']);
    if($linked_area_id){
        $linked_area_name = get_the_title($linked_area_id);
    }
}
?>

<?php
if($linked_area_id && $linked_area_id != 0){ ?>
<a href="<?php /** @noinspection PhpUndefinedVariableInspection */
echo $template_args['link']; ?>" target="<?php echo $target ?>" class="col category-link" data-disable_referrer="<?php echo $disable_referrer ?>" data-area-id="<?php echo $linked_area_id ?>" data-area-name="<?php echo $linked_area_name ?>">
<?php } else { ?>
    <a href="<?php /** @noinspection PhpUndefinedVariableInspection */
    echo $template_args['link']; ?>" target="<?php echo $target ?>" class="col" data-disable_referrer="<?php echo $disable_referrer ?>">
<?php } ?>

    <div class="category-listing">
        <div class="image-container">
            <picture class="lazyload">
                <source media="(min-width: 415px)" data-srcset="<?php echo $image; ?>"
                        srcset="<?php echo $GLOBALS['images_dir'] . 'category-placeholder.svg' ?>">
                <source media="(max-width: 414px)"
                        srcset="<?php echo $GLOBALS['images_dir'] . 'category-placeholder.svg' ?>"
                        data-srcset="<?php echo $mobile_image; ?>">
                <img class="lazyload" data-foody-src="<?php echo $image ?>"
                     src="<?php echo $GLOBALS['images_dir'] . 'category-placeholder.svg' ?>" alt="category-placeholder">
            </picture>
        </div>

        <div class="categort-listing-title"><?php echo $name; ?></div>
    </div>
</a>
