<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 5:37 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$images = $template_args['images'];
?>

<picture>
    <source media="(min-width: 415px)" srcset="<?php echo $images['image']['url']; ?>"
    ">
    <source media="(max-width: 414px)" srcset="<?php echo $images['mobile_image']['url']; ?>"
    ">
    <img src="<?php echo $images['image']['url'] ?>">
</picture>
