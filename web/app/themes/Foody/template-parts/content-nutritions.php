<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 11:52 AM
 */

$nutritions = $template_args['nutritions'];
$title = $template_args['title'];

?>

<h2 class="title">
    <?php echo $title ?>
</h2>

<div class="nutrition-container">
    <div class="nutritions row">

        <?php foreach ($nutritions as $nutrition): ?>

            <div class="col-sm-4 col-12 nutrition">

                <?php foreach ($nutrition as $values): ?>
                    <div class="nutrition-row">
                    <span class="name">
                        <?php echo $values['name'] ?>
                    </span>

                        <span class="value <?php echo $values['positive_negative'] ?>">
                        <?php echo $values['value'] ?>
                    </span>
                    </div>
                    <div class="clearfix"></div>

                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>
    </div>
</div>