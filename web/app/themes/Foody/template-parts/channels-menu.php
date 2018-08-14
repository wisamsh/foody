<?php
/**
 * Created by PhpStorm.
 * User: omri
 * Date: 08/07/2018
 * Time: 10:47
 */
$items_for_display = $template_args['items'];

$classes = "container-fluid channels-menu";
if(wp_is_mobile()){
	$classes .=  ' collapse navbar-collapse';
}
?>

<div class="<?php echo $classes?>" id="channels-menu">
    <h1 class="channels-menu-title">
        <?php echo foody_get_menu_title("channels-menu"); ?>
        <span class="d-inline d-sm-none channels-menu-close-btn" data-toggle="collapse" data-target="#channels-menu">&times;</span>
    </h1>
    <div class="row channels-grid">
        <?php foreach ($items_for_display as $item_for_display): ?>
            <div class="col-6 col-lg-3 channel-item-container">
                <div class="channel-item">
                    <a href="<?php echo $item_for_display['link']; ?>" class="link">
                        <div class="image-gradient"><img src="<?php echo $item_for_display['image']; ?>" class="img-fluid channel-image"></div>
                        <div class="channel-title"><h1><?php echo $item_for_display['title']; ?></h1></div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>