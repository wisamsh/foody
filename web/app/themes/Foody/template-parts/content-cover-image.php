<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 6:20 PM
 */


if (isset($template_args)) {
    if (!isset($template_args['image'])) {
        $image = $template_args;
    }else{
        $image = $template_args['image'];
    }
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

$mobile_image = $image;

if(isset($template_args['mobile_image'])){
    /** @noinspection PhpUndefinedVariableInspection */
    $mobile_image = $template_args['mobile_image'];
    if(!is_string($mobile_image)){
        $mobile_image = $mobile_image['url'];
    }
}

?>

<div class="cover-image">
    <?php if (isset($a)) {
        echo $a;
    } ?>


    <picture>
        <source media="(min-width: 415px)" srcset="<?php echo $image; ?>"
        ">
        <source media="(max-width: 414px)"
                srcset="<?php echo $mobile_image; ?>"
        ">
        <img src="<?php echo $image ?>">
    </picture>


<!--    <img src="--><?php //echo $image ?><!--" alt="">-->

    <?php if (isset($a)) {
        echo '</a>';
    } ?>

</div>
