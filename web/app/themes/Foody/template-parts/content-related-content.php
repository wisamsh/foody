<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 11:41 AM
 *
 * Displays a list of related content.
 *
 * Required args:
 * string $type - the type of related content, e.g recipe, playlist, etc.
 * array $items - the items to show in the list.
 */
/** @noinspection PhpUndefinedVariableInspection */
$related_content = $template_args;

$content_classes = foody_get_array_default($related_content, 'content_classes', '');


/**
 * @var string $type
 * type of related content template
 * to show.
 * Equal to the registered post types
 *
 */
$type = foody_get_array_default($related_content, 'type', 'foody_recipe');


/** @var array $items
 * The items to show.
 */
$items = foody_get_array_default($related_content, 'items', array());

?>
<section class="related-content-container <?php echo $content_classes ?>">

<h3 class="title related-content-title">
    <?php echo $related_content['title'] ?>
</h3>
<ul class="related-content nolist <?php echo $content_classes ?>">

    <?php foreach ($items as $item) : ?>

        <li class="related-item playlist">

            <a href="<?php echo get_permalink($item['id']) ?>">

                <?php
                foody_get_template_part(
                    get_template_directory() . '/template-parts/content-related-' . $type . '.php',
                    $item
                );
                ?>
            </a>

            <div class="details">
                <h3 class="post-title">
                    <a href="<?php echo get_permalink($item['id']) ?>">
                        <?php echo $item['title'] ?>
                    </a>
                </h3>

                <a class="author-name" href="<?php echo $item['author']['link'] ?>">
                    <?php echo $item['author']['name'] ?>
                </a>

                <span class="view-count">
            <?php echo $item['view_count'] ?>
        </span>
            </div>

            <div class="excerpt">
                <?php echo get_the_excerpt($item['id']) ?>
            </div>

        </li>
    <?php endforeach; ?>


</ul>
</section>