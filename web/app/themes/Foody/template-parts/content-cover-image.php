<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 6:20 PM
 */


if(isset($template_args)){
    $image = $template_args;
}

if(empty($image)){
    $image = get_header_image();
}else{
    $image = $image['url'];
}
?>

<div class="cover-image">
    <img src="<?php echo $image ?>" alt="">
</div>
