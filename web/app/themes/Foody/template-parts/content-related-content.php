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


<ul class="row related-content <?php echo $content_classes ?>">

    <?php foreach ($items as $item) {

        foody_get_template_part(
            get_template_directory() . '/template-parts/content-related-' . $type . '.php',
            $item
        );
    }

    ?>

</ul>
