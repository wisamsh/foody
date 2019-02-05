<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/13/18
 * Time: 11:43 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Category $category */
$category = $template_args['category'];

?>

    <h1 class="title">
        <?php echo sprintf('קטגוריות %s', $category->title) ?>
    </h1>

<?php
/** @var Foody_Category[] $sub_categories */
$sub_categories = $category->get_sub_categories();
if (is_array($sub_categories)):

    $count = ceil(count($sub_categories) / 3);

    $slider_data = [
        'slidesToShow' => 4,
        'rtl' => true,
        'prevArrow' => '<i class="icon-arrowleft prev"></i>',
        'nextArrow' => '<i class="icon-arrowleft next"></i>',
        'slidesToScroll' => $count,
        'infinite' => false,
        'responsive' => [
            [
                'breakpoint' => 1441,
                'settings' => [
                    'slidesToShow' => 5,
                    'arrows' => false,
                    'slidesToScroll' => $count
                ]
            ],
            [
                'breakpoint' => 1025,
                'settings' => [
                    'slidesToShow' => 5,
                    'arrows' => false,
                    'slidesToScroll' => $count
                ]
            ],
            [
                'breakpoint' => 415,
                'settings' => [
                    'slidesToShow' => 3,
                    'arrows' => false,
                    'slidesToScroll' => $count
                ]
            ]
        ]
    ]

    ?>

    <ul class="foody-slider categories-slider" data-slick='<?php echo json_encode($slider_data, ENT_QUOTES) ?>'>
        <?php


        foreach ($sub_categories as $sub_category):?>

            <li class="foody-slider-item category-slider-item">
                <a href="<?php echo $sub_category->link ?>">
                    <div>
                        <img src="<?php echo $sub_category->get_image() ?>" alt="<?php echo $sub_category->title ?>">
                    </div>
                    <h4 class="title category-title">
                        <?php echo $sub_category->title ?>
                    </h4>

                </a>
            </li>

        <?php endforeach; ?>
    </ul>

<?php endif;
