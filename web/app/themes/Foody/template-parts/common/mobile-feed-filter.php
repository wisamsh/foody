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
    <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer" data-target="#dw-p2" aria-label="<?php echo __('סינון', 'foody'); ?>">
        <?php echo __('סינון', 'foody'); ?>
    </button>
</div>

<div class="mobile-filter d-lg-none no-print">
    <aside class="sidebar sidebar-mobile">
        <section class="sidebar-content">
            <section class="sidebar-section foody-search-filter">
                <?php
                $foody_query = SidebarFilter::get_instance();
                $foody_query->the_filter();
                ?>
            </section>
        </section>
    </aside>

    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>


    <?php

    if ($wrap) {
        echo '<aside class="sidebar sidebar-mobile"> <section class="sidebar-content">';
    }

    if (is_callable($sidebar)) {
        $sidebar('aside.sidebar-mobile .sidebar-content');
    }

    if ($wrap) {
        echo '</section></aside>';
    }

    ?>

    <div class="show-recipes-container">

        <button class="btn show-recipes" aria-label="<?php echo __('הצג מתכונים', 'foody') ?>">
            >
        </button>
    </div>
</div>