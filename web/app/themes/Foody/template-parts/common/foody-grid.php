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
$responsive = foody_get_array_default($settings, 'responsive', null);;
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


?>

<section class="foody-grid <?php foody_el_classes($classes) ?>">


    <?php if (!empty($settings['header'])):

        $grid_header = $settings['header'];
        ?>
        <section class="grid-header row">
            <?php if (!empty($grid_header['title'])): ?>
                <h2 class="title col">
                    <?php echo $grid_header['title'] ?>
                </h2>
            <?php endif; ?>
            <?php if (!empty($grid_header['sort'])):
                $sort = $grid_header['sort'];
                $options = empty($sort['options']) ? $default_sort_options : $sort['options'];
                $sort_args = [
                    'id' => 'sort-' . $id,
                    'options' => $options,
                    'placeholder' => 'סדר על פי'
                ];
                ?>

                <section class="grid-sort col">
                    <?php foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $sort_args); ?>
                </section>

            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="row" id="<?php echo $id ?>">
        <?php $grid->loop($posts, $cols, true, null, [], $responsive) ?>
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
