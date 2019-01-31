<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/31/19
 * Time: 8:41 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$url = $template_args['url'];
$classes = foody_get_array_default($template_args, 'classes', []);

$def_classes = ['btn'];
$classes = array_merge($classes,$def_classes);
?>

<button id="whatsapp" class="<?php foody_el_classes($classes) ?>">
    <a href="<?php echo $url ?>" target="_blank">
        <i class="icon-whatsapp"></i>
    </a>
</button>
