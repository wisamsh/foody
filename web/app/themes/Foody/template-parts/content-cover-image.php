<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 6:20 PM
 */


if (isset($template_args)) {
    $image = $template_args;
}

if (empty($image)) {
    $image = get_header_image();
} else {
    $image = $image['url'];
}

$link = get_field('cover_link');
if (!empty($link)) {
    $a = '<a href="' . $link['url'] . '" target="' . $link['target'] . '">';
}

?>

<div class="cover-image">
    <?php if (isset($a)) {
        echo $a;
    } ?>


    <img src="<?php echo $image ?>" alt="">

    <?php if (isset($a)) {
        echo '</a>';
    } ?>

</div>
