<?php
/**
 * Template Name: Items
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */

$items_page = new Foody_ItemsPage();

get_header();
?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div class="site-content" role="main">

                <?php $items_page->cover() ?>

                <div class="container container-max-960 ">
                    <?php if (function_exists('bootstrap_breadcrumb')): ?>

                        <?php bootstrap_breadcrumb(); ?>

                    <?php endif; ?>

                    <?php echo the_title('<h1 class="title mt-0 mb-0">', '</h1>') ?>


                    <?php the_content(); ?>

                    <?php $items_page->items() ?>
                </div>

            </div><!-- #content -->

        </div><!-- #primary -->
    </div><!-- #main-content -->


<?php

get_footer();