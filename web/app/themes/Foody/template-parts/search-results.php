<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/26/18
 * Time: 1:29 PM
 */
$grid = new FoodyGrid();


/** @var Foody_SearchPage $search */
$search = $template_args['search'];
?>

<header class="search-results-header">
    <div class="container-fluid">
        <div class="row justify-content-space-between">
            <h1 class="title col">
                <?php echo get_search_query(); ?>
            </h1>


            <?php if (have_posts()): ?>

                <div class="sort col">
                    <?php
                    $select_args = array(
                        'id' => 'sort-search',
                        'placeholder' => 'סדר על פי',
                        'options' => array(
                            array(
                                'value' => 1,
                                'label' => 'א-ת'
                            ),
                            array(
                                'value' => -1,
                                'label' => 'ת-א'
                            )
                        )
                    );

                    foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $select_args);
                    ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</header>
<div class="container-fluid search-results">

    <div class="row gutter-3">

        <?php

        if (have_posts()) {
            $search->the_results();
        } else {
            $search->no_results();
        }
        ?>

    </div>


</div>
