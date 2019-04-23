<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/13/18
 * Time: 11:43 AM
 */

$brands          = $template_args['brands'];
$brands_title    = $template_args['brands_title'];

?>
    <h1 class="title">
		<?php echo $brands_title ?>
    </h1>
<?php

if ( is_array( $brands ) ):

	$count = ceil( count( $brands ) / 3 );
	$count       = min( 3, count( $brands ) );
	$slider_data = [
		'slidesToShow'   => 3,
		'rtl'            => true,
		'prevArrow'      => '<i class="icon-arrowleft prev"></i>',
		'nextArrow'      => '<i class="icon-arrowleft next"></i>',
		'slidesToScroll' => $count,
		'infinite'       => true,
		'responsive'     => [
			[
				'breakpoint' => 1441,
				'settings'   => [
					'slidesToShow'   => 3,
					'arrows'         => true,
					'slidesToScroll' => $count
				]
			],
			[
				'breakpoint' => 1025,
				'settings'   => [
					'slidesToShow'   => 3,
					'arrows'         => true,
					'slidesToScroll' => $count
				]
			],
			[
				'breakpoint' => 768,
				'settings'   => [
					'slidesToShow'   => 1,
					'arrows'         => true,
					'slidesToScroll' => 1
				]
			],
			[
				'breakpoint' => 415,
				'settings'   => [
					'slidesToShow'   => 1,
					'arrows'         => true,
					'slidesToScroll' => 1
				]
			]
		]
	];


	?>
    <ul class="brands-slider" data-slick='<?php echo json_encode( $slider_data, ENT_QUOTES ) ?>'>
		<?php

		foreach ( $brands as $brand ): ?>

            <li class="foody-slider-item brands-slider-item">
				<?php if ( ! empty( $brand['link'] ) ) {
					echo '<a href="' . $brand['link']['url'] . '" target="' . $brand['link']['target'] . '">';
				} ?>

                <img src="<?php echo $brand['image']['url'] ?>"
                     alt="<?php echo $brand['image']['alt'] ?>">
				<?php if ( isset( $brand['link'] ) ) {
					echo '</a>';
				} ?>
            </li>

		<?php endforeach; ?>
    </ul>

<?php endif;
