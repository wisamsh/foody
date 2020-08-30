<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 12/23/18
 * Time: 12:57 PM
 */

$foody_brands = $template_args['brands'];
$foody_brands_title = $template_args['title'];
?>


<div class="brands-toggle-mobile d-block d-lg-none no-print">
    <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer"
            data-target="#dw-p2" aria-label="<?php echo __('למותגים', 'foody'); ?>">
        <?php echo __('למותגים', 'foody'); ?>
    </button>


    <div class="brands-avenue-mobile d-lg-none no-print">

        <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="brands-container-mobile">
            <?php
            foody_get_template_part(
                get_template_directory() . '/template-parts/common/foody-brands.php',
                array(
                    'brands' => $foody_brands,
                    'title' => $foody_brands_title
                )
            );
            ?>
        </div>
    </div>
</div>
