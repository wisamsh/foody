<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 12/23/18
 * Time: 12:57 PM
 */

$sidebar = $template_args['sidebar'];
$wrap = isset($template_args['wrap']) && $template_args['wrap'];

?>


<div class="filter-mobile d-block d-lg-none no-print">
    <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer"
            data-target="#dw-p2">
        <?php echo __('סינון', 'foody'); ?>
    </button>
</div>

<div class="mobile-filter d-lg-none no-print">

    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>


    <?php

    if ($wrap) {
        echo '<aside class="sidebar"> <section class="sidebar-content">';
    }

    if (is_callable($sidebar)) {
        $sidebar();
    }

    if ($wrap) {
        echo '</section></aside>';
    }

    ?>

    <div class="show-recipes-container">

        <button class="btn show-recipes">
            <?php echo __('הצג מתכונים', 'foody') ?>
        </button>
    </div>
</div>

