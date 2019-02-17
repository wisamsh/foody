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
<!--            <img src="--><?php //echo $template_args['image']; ?><!--" alt="">-->

            <picture>
                <source media="(min-width: 415px)" srcset="<?php echo $template_args['image']; ?>"
                ">
                <source media="(max-width: 414px)" srcset="<?php echo $template_args['mobile_image']; ?>"
                ">
                <img src="<?php echo $template_args['image'] ?>" >
            </picture>
        </div>

        <h4 class="categort-listing-title"><?php echo $template_args['name']; ?></h4>
    </div>
</a>
