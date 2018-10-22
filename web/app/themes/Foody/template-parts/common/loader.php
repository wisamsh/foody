<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/15/18
 * Time: 1:00 PM
 */

$load = !empty($template_args['load']);

if ($load) {
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/4.13.0/bodymovin_light.min.js"></script>';
}

?>

<div class="foody-loader"></div>
<script>

//    jQuery(document).ready(() => {
//        lottie.loadAnimation({
//            container: $('.foody-loader')[0], // the dom element that will contain the animation
//            renderer: 'svg',
//            loop: true,
//            autoplay: true,
//            path: '/app/themes/Foody/resources/lottie/loader.json' // the path to the animation json
//        });
//    });


</script>
