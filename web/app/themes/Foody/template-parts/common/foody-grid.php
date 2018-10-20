<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 1:28 PM
 */

$grid = new FoodyGrid();

/** @noinspection PhpUndefinedVariableInspection */
$settings = $template_args;
$id = $settings['id'];
$posts = $settings['posts'];
$cols = $settings['cols'];
$more = $settings['more'];
$classes = foody_get_array_default($settings, 'classes', []);
$responsive = foody_get_array_default($settings, 'responsive',null);;

// TODO add sort and title
if (!empty($settings['show_header'])) {


}

?>

<section class="foody-grid <?php foody_el_classes($classes) ?>">

    <section class="row" id="<?php echo $id ?>">
        <?php $grid->loop($posts, $cols,true,null,[],$responsive) ?>
    </section>

    <?php if (!empty($posts) && $more && $grid->is_displayable($posts)): ?>

        <div class="show-more">
            <img src="<?php echo $GLOBALS['images_dir'] . 'bite.png' ?>" alt="">
            <h4>
                <?php echo __('לעוד מתכונים', 'foody') ?>
            </h4>
        </div>

    <?php endif; ?>

</section>
