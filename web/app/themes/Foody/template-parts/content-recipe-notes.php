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
$is_print = $template_args['print'];

?>

<section class="recipe-notes box <?php echo $is_print ? 'print' : 'no-print'?>">

    <h2 class="title">
		<?php echo $title ?>
    </h2>

    <ul class="notes" title="הערות">

		<?php foreach ( $notes as $note ): ?>

            <li class="note">
				<?php echo $note['note'] ?>
            </li>

		<?php endforeach; ?>

    </ul>

</section>