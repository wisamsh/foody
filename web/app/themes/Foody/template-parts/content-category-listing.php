<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 10:14 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$image = $template_args['image'];
$mobile_image = foody_get_array_default($template_args, 'mobile_image', $image);

$name = isset($template_args['name']) ? $template_args['name'] : '';
if (empty($name)) {
    $name = isset($template_args['title']) ? $template_args['title'] : '';
}

?>

<a href="<?php /** @noinspection PhpUndefinedVariableInspection */
echo $template_args['link']; ?>" class="col">
    <div class="category-listing">
        <div class="image-container">
            <picture>
                <source media="(min-width: 415px)" srcset="<?php echo $image; ?>">
                <source media="(max-width: 414px)" srcset="<?php echo $mobile_image; ?>">
                <img src="<?php echo $image ?>">
            </picture>
        </div>

        <h2 class="categort-listing-title"><?php echo $name; ?></h2>
    </div>
</a>
