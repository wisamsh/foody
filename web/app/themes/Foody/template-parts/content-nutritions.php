<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 11:52 AM
 */

$nutritions = $template_args;

?>

<div class="nutritions row">

    <?php foreach ($nutritions as $nutrition): ?>

        <div class="col nutrition">

            <?php foreach ($nutrition as $values): ?>
                <div>
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
