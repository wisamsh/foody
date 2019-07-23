<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/3/19
 * Time: 10:31 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$items = $template_args['items'];

$select_args = array(
	'id'          => 'items-sort',
	'placeholder' => 'סדר על פי',
	'options'     => array(
		array(
			'value' => 1,
			'label' => 'א-ת'
		),
		array(
			'value' => - 1,
			'label' => 'ת-א'
		)
	)
);


?>


<section class="grid">

    <div class="grid-header">
		<?php foody_get_template_part( get_template_directory() . '/template-parts/common/foody-select.php', $select_args ); ?>
    </div>

    <section class="grid-body row gutter-10">

		<?php
		$i = 0;
		foreach ( $items as $item ) {
			$item['order'] = $i;
			foody_get_template_part( get_template_directory() . '/template-parts/content-items-page-item.php', $item );
			$i ++;
		}
		?>


    </section>


</section>