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

$slider_data = [
    'slidesToShow' => 7,
    'rtl' => true,
    'prevArrow' => '<i class="icon-arrowleft prev"></i>',
    'nextArrow' => '<i class="icon-arrowleft next"></i>',
    'adaptiveHeight' => true,
    'slidesToScroll' => 3,
    'infinite' => false,
    'responsive' => [
        [
            'breakpoint' => 1441,
            'settings' => [
                'slidesToShow' => 5,
            ]
        ],
        [
            'breakpoint' => 415,
            'settings' => [
                'slidesToShow' => 4,
                'arrows' => false
            ]
        ]
    ]
]

?>

<h1 class="title">
    <?php echo sprintf('קטגוריות %s', $category->title) ?>
</h1>


<ul class="foody-slider categories-slider" data-slick='<?php echo json_encode($slider_data, ENT_QUOTES) ?>'>
    <?php

    /** @var Foody_Category[] $sub_categories */
    $sub_categories = $category->get_sub_categories();

    // TODO remove debug
    for ($i = 0; $i < 8; $i++) {
        $sub_categories[] = $sub_categories[0];
    }

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
