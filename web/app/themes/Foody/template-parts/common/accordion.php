<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 6:26 PM
 */


/** @noinspection PhpUndefinedVariableInspection */
// these three are required
$title = $template_args['title'];
$content = $template_args['content'];
$id = $template_args['id'];

// TODO change impl to wp_parse_args

/** @var array $classes */
$classes = foody_get_array_default($template_args, 'classes', []);
if (!is_array($classes)) {
    $classes = explode(' ', $classes);
}

/** @var array $title_classes */
$title_classes = foody_get_array_default($template_args, 'title_classes', []);
if (!is_array($title_classes)) {
    $title_classes = explode(' ', $title_classes);
}
/** @var string $title_icon */
$title_icon = foody_get_array_default($template_args, 'title_icon', '');

/** @var array $collapse_classes */
$collapse_classes = foody_get_array_default($template_args, 'collapse_classes', []);
if (!is_array($collapse_classes)) {
    $collapse_classes = explode(' ', $collapse_classes);
}

/**
 * The initial state of the accordion ( closed or open. defaults to open).
 * If open, adds the 'show' class to the collapsed element.
 * @var string $start_state
 */
$start_state = foody_get_array_default($template_args, 'start_state', 'show');


$classes = array_merge($classes, ['foody-accordion']);

if ($start_state != 'show') {
//    $title_classes[] = 'collapsed';
}

$collapse_classes[] = $start_state;

?>

<div id="accordion-<?php echo $id ?>" role="tablist" class="foody-accordion <?php foody_el_classes($classes) ?>">
    <div class="foody-accordion-content">
        <div class="foody-accordion-title" role="tab" id="heading-<?php echo $id ?>">
            <h5 class="mb-0">
                <?php if ($title_icon != ''): ?>
                    <i class="<?php echo $title_icon ?>"></i>
                <?php endif; ?>
                <a class="<?php foody_el_classes($title_classes) ?>" data-toggle="collapse" href="#<?php echo $id ?>"
                   aria-expanded="true"
                   aria-controls="<?php echo $id ?>">
                    <?php echo $title ?>
                </a>
                <i class="icon-side-arrow arrow" data-toggle="collapse" aria-controls="<?php echo $id ?>"></i>

            </h5>
        </div>

        <div id="<?php echo $id ?>" class="<?php foody_el_classes($collapse_classes) ?>" role="tabpanel"
             aria-labelledby="heading-<?php echo $id ?>" data-parent="#accordion-<?php echo $id ?>">
            <div class="card-body">
                <?php
                if (is_callable($content)) {
                    call_user_func($content);
                } else {
                    echo $content;
                }
                ?>
            </div>
        </div>
    </div>
</div>