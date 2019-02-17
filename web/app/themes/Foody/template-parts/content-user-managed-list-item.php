<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/16/18
 * Time: 5:30 PM
 */
/** @noinspection PhpUndefinedVariableInspection */
$item = $template_args;

?>

<li class="managed-list-item" data-id="<?php echo $item['id'] ?>" data-type="<?php echo $item['type'] ?>">

    <!--    <img src="--><?php //echo $item['image'] ?><!--" alt="--><?php //echo $item['name'] ?><!--">-->
    <?php echo $item['image'] ?>

    <a href="<?php echo $item['link'] ?>">
        <?php echo $item['name'] ?>
    </a>

    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>

</li>
