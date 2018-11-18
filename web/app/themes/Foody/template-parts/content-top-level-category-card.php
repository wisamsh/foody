<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/9/18
 * Time: 8:05 PM
 */
$main_category_id = $template_args['id'];
$foody_category = new Foody_Category($main_category_id);


$cards_per_row = $template_args['cards_per_row'];
if ($cards_per_row <= 0) {
    $cards_per_row = 1;
}

$cards_per_row = 12 / $cards_per_row;

$column_class = '';// 'col-12 col-sm-' . $cards_per_row;

$category = $template_args['category'];

$link = get_term_link($category);
if (is_wp_error($link)) {
    $link = '';
}

?>
<div>
    <div class="card p-0">
        <img class="card-img-top img-fluid"
             src="<?php echo $foody_category->get_image() ?>"
             alt="<?php echo $template_args['title'] ?>">
        <div class="card-block">
            <h2 class="card-title">
                <a href="<?php echo $link ?>">
                    <?php echo $template_args['title'] ?>
                </a>
            </h2>
            <h3 class="card-subtitle">
                <a href="<?php get_category_link($main_category_id) ?>">
                    <a href="<?php echo $link ?>">
                        <?php echo $template_args['subtitle'] ?>
                    </a>

                </a>
            </h3>
            <section class="card-text">

                <?php foreach ($template_args['categories'] as $category): ?>
                    <h4 class="sub-categories-title"><?php echo $category['title'] ?></h4>
                    <ul class="sub-categories">
                        <?php foreach ($category['categories'] as $sub_category): ?>
                            <li>
                                <a href="<?php echo $sub_category['link'] ?>">
                                    <?php echo $sub_category['title']; ?>
                                </a>

                            </li>
                        <?php endforeach; ?>
                    </ul>

                <?php endforeach; ?>

            </section>
        </div>
    </div>
</div>
