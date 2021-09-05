<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/3/19
 * Time: 10:31 AM
 */

/** @noinspection PhpUndefinedVariableInspection */


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
        <?php //foody_get_template_part( get_template_directory() . '/template-parts/common/foody-select.php', $select_args ); ?>
    </div>

    <section class="grid-body row gutter-10">

        <?php
        $i = 0;
        $args = array(
        'post_type' => 'foody_answer',
        'post_status' => 'publish',
        'order' => 'ASC'
        );

        $faq_loop = new WP_Query( $args );
        while ( $faq_loop->have_posts() ): $faq_loop->the_post(); ?>
            <div class="col-4 col-lg-3 item">
                <a href="<?php echo get_permalink() ?>"  >
                    <h4 class="title faq-title">
                        <?php echo the_title() ?>
                    </h4>
                </a>
            </div>
      <?php  endwhile; ?>


    </section>


</section>