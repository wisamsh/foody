<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/18
 * Time: 1:44 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$notes = $template_args['notes'];
$title = $template_args['title'];

?>

<h4 class="title">
    <?php echo $title ?>
</h4>

<ul class="notes" title="הערות">

    <?php foreach ($notes as $note): ?>

        <li class="note">
            <?php echo $note['note'] ?>
        </li>

    <?php endforeach; ?>

</ul>
