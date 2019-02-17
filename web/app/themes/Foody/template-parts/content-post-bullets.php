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

if (!empty($template_args['hide'])) {
    $hide_views = empty($template_args['hide']['views']);
    $hide_date = empty($template_args['hide']['date']);
}

?>

<ul class="content-details-bullets">
    <li>
        <?php echo $foody_page->getAuthorName() ?>
    </li>
    <?php if (!empty($hide_views)): ?>
        <li>
            <?php echo $foody_page->getViewCount() ?>
        </li>
    <?php endif; ?>

    <?php if (!empty($hide_date)): ?>
        <li>
            <?php echo $foody_page->getPostedOn() ?>
        </li>
    <?php endif; ?>
    <?php foreach ($dynamic as $item): ?>
        <li>
            <?php echo $item; ?>
        </li>
    <?php endforeach; ?>
</ul>

