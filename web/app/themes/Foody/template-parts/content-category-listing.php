<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 10:14 PM
 */

?>

<a href="<?php /** @noinspection PhpUndefinedVariableInspection */
echo $template_args['link']; ?>" class="col">
    <div class="category-listing">
        <div class="image-container">
            <img src="<?php echo $template_args['image']; ?>" alt="">
        </div>

        <h4 class="categort-listing-title"><?php echo $template_args['name']; ?></h4>
    </div>
</a>
