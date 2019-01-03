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
    'id' => 'team-sort',
    'placeholder' => 'סדר על פי',
    'options' => array(
        array(
            'value' => 1,
            'label' => 'א-ת'
        ),
        array(
            'value' => -1,
            'label' => 'ת-א'
        )
    )
);


?>


<section class="grid">

    <div class="grid-header">
        <?php foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $select_args); ?>
    </div>

    <section class="grid-body row">

        <?php foreach ($items as $item){
            foody_get_template_part(get_template_directory() . '/template-parts/content-items-page-item.php',$item);
        } ?>


    </section>


</section>