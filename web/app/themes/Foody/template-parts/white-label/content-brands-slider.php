<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/13/18
 * Time: 11:43 AM
 */

$brands = $template_args['brands'];
$brands_title = $template_args['brands_title'];

?>
    <h1 class="title">
        <?php echo $brands_title ?>
    </h1>
<?php

if (is_array($brands)):

    $count = ceil(count($brands) / 3);
    $count = min(3, count($brands));

    $slides_to_show = 3;

    $slider_data = [
        'slidesToShow' => $slides_to_show,
        'rtl' => true,
        'prevArrow' => '<i class="icon-arrowleft prev"></i>',
        'nextArrow' => '<i class="icon-arrowleft next"></i>',
        'slidesToScroll' => $count,
        'infinite' => true,
        'responsive' => [
            [
                'breakpoint' => 1441,
                'settings' => [
                    'slidesToShow' => $slides_to_show,
                    'arrows' => true,
                    'slidesToScroll' => $count
                ]
            ],
            [
                'breakpoint' => 1025,
                'settings' => [
                    'slidesToShow' => $slides_to_show,
                    'arrows' => true,
                    'slidesToScroll' => $count
                ]
            ],
            [
                'breakpoint' => 768,
                'settings' => [
                    'slidesToShow' => 1,
                    'arrows' => true,
                    'slidesToScroll' => 1
                ]
            ]
        ]
    ];

    ?>
    <ul class="brands-slider" data-slick='<?php echo json_encode($slider_data, ENT_QUOTES) ?>'>
        <?php
        // FIXME
        $height = 200;
        $width = 50;
        $image = [
            'title' => 'asdgasgf',
            'alt' => 'sd',
            'url' => "https://dummyimage.com/{$height}x{$width}/000/fff.png"
        ];
        foreach ($brands as $brand): ?>
            <?php $brand['image'] = $image; ?>
            <li class="foody-slider-item brands-slider-item"
                title="<?php echo isset($brand['image']['title']) ? $brand['image']['title'] : '' ?>">
                <?php if (!empty($brand['link'])) {
                    echo '<a href="' . $brand['link']['url'] . '" target="' . $brand['link']['target'] . '">';
                } ?>

                <img src="<?php echo $brand['image']['url'] ?>"
                     alt="<?php echo $brand['image']['alt'] ?>">
                <?php if (isset($brand['link'])) {
                    echo '</a>';
                } ?>
            </li>

        <?php endforeach; ?>
    </ul>

<?php endif;
