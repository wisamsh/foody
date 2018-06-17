<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/16/18
 * Time: 5:33 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$list = $template_args;

?>

<ul class="managed-list">

    <?php
    foreach ($list as $item) {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-user-managed-list-item.php',
            $item
        );
    }
    ?>


</ul>
