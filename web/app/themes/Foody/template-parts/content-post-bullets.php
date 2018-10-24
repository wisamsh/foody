<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 3:33 PM
 */


/** @var Foody_Post $foody_page */
/** @noinspection PhpUndefinedVariableInspection */
$foody_page = $template_args['foody_page'];

$dynamic = !empty($template_args['dynamic']) ? $template_args['dynamic'] : [];

?>

<ul class="content-details-bullets">
    <li>
        <?php echo $foody_page->getAuthorName() ?>
    </li>
    <li>
        <?php echo $foody_page->getViewCount() ?>
    </li>
    <li>
        <?php echo $foody_page->getPostedOn() ?>
    </li>

    <?php foreach ($dynamic as $item): ?>
        <li>
            <?php echo $item; ?>
        </li>
    <?php endforeach; ?>

    <?php if (isset($template_args['show_favorite']) && $template_args['show_favorite']): ?>

        <li class="no-bullet d-none d-xl-inline-block">
            <?php
            foody_get_template_part(
                get_template_directory() . '/template-parts/common/favorite.php',
                array(
                    'post' => $foody_page
                )
            )
            ?>
        </li>

    <?php endif; ?>
</ul>
