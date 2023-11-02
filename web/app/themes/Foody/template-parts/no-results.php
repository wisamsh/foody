<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/9/18
 * Time: 8:17 PM
 */
?>

<section class="no-results">

    <div>
        <img src="<?php echo $GLOBALS['images_dir'] . 'empty-recipes.svg' ?>"
             alt="<?php echo __('לא נמצאו תוצאות') ?>">

    </div>

    <h2>
        <?php echo __('לא נמצאו תוצאות', 'foody') ?>
        </br>
        </br>
        <?php echo __('אבל אולי יעניין אותך גם…', 'foody'); ?>
    </h2>

    <?php
    if(!is_array($_REQUEST) || !isset($_REQUEST['action']) || $_REQUEST['action'] != 'foody_filter') {
        if(is_array($_GET) && isset($_GET['s'])) {
            $args = ['have_sidebar' => true];
            foody_get_template_part(get_template_directory() . '/template-parts/common/not-found-suggestion.php', $args);
        }
    }
    ?>

</section>
