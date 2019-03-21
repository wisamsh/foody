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
$responsive = foody_get_array_default($settings, 'responsive', null);
$default_sort_options = [
    [
        'value' => 'popular_desc',
        'label' => 'פופולארי'
    ],
    [
        'value' => 'date_desc',
        'label' => 'חדש לישן'
    ],
    [
        'value' => 'date_asc',
        'label' => 'ישן לחדש'
    ],
    [
        'value' => 'title_asc',
        'label' => 'א-ת'
    ],
    [
        'value' => 'title_desc',
        'label' => 'ת-א'
    ]
];
$item_args = foody_get_array_default($settings, 'item_args', []);
$title_el = foody_get_array_default($template_args, 'title_el', 'h3');


?>

<section class="foody-grid <?php foody_el_classes($classes) ?>">


    <?php if (!empty($settings['header'])):

    $grid_header = $settings['header'];
    ?>
    <section class="grid-header row">

        <<?php echo $title_el ?> class="title col-12 col-lg-6">
        <?php
        if (!empty($grid_header['title'])) {
            echo $grid_header['title'];
        }
        ?>
    </<?php echo $title_el ?>>

    <?php if (!empty($grid_header['sort']) && !empty($posts)):
        $sort = $grid_header['sort'];
        $options = empty($sort['options']) ? $default_sort_options : $sort['options'];
        $sort_args = [
            'id' => 'sort-' . $id,
            'options' => $options,
            'placeholder' => 'סדר על פי'
        ];
        ?>

        <section class="grid-sort col-12 col-lg-6">
            <?php foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $sort_args); ?>
        </section>

    <?php endif; ?>
</section>
<?php endif; ?>

<section class="row" id="<?php echo $id ?>">
    <?php if (!empty($posts) && $grid->is_displayable($posts)): ?>
        <?php $grid->loop($posts, $cols, true, null, [], $responsive, $item_args) ?>
    <?php else: ?>
        <?php foody_get_template_part(get_template_directory() .'/template-parts/no-results.php') ?>
    <?php endif; ?>
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
