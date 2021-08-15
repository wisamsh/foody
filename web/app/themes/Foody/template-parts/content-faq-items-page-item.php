<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/3/19
 * Time: 10:38 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$item = $template_args;

$title = $item['title'];

$link = '';

if ( ! empty( $item['link'] ) ) {
    if ( is_array( $item['link'] ) ) {
        $link   = $item['link']['url'];
        $target = foody_get_array_default( $item['link'], 'target', '' );
        if ( empty( $title ) ) {
            $title = $item['link']['title'];
        }
    }
}


?>

<div class="col-4 col-lg-3 item" data-sort="<?php echo $title ?>" data-order="<?php echo $item['order'] ?>">
    <a href="<?php echo $link ?>" <?php if ( ! empty( $target ) ) {
        echo 'target="' . $target . '"';
    } ?> >


        <h4 class="title">
            <?php echo $title ?>
        </h4>
    </a>
</div>
