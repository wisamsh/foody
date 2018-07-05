<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/18
 * Time: 1:44 PM
 */
$notes = $template_args['notes'];

?>

<h4 class="title">
    הערות
</h4>

<ul class="notes" title="הערות">

    <?php foreach ($notes as $note): ?>

        <li class="note">
            <?php echo $note['note'] ?>
        </li>

    <?php endforeach; ?>

</ul>
