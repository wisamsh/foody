<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/3/19
 * Time: 10:38 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$item = $template_args;

$image = $item['image'];


?>

<div class="col-4 col-lg-3">
    <a href="<?php echo $item['link'] ?>">

        <?php if ($image): ?>

            <div class="image-container">
                <img src="<?php echo $image['url'] ?>" alt="<?php echo $image['alt'] ?>">
            </div>

        <?php endif; ?>

        <h4 class="title">
            <?php echo $item['title'] ?>
        </h4>
    </a>
</div>
