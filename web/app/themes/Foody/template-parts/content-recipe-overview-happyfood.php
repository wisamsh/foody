<?php
$carbons = round($nutrients[1]['valuePerDish']);
$color_green = "#00800052";
$color_yellow = "#d1bf0652";
$color_red = "#fd090973";
$clr = '';
$clrMB = '';
$carbIcon = 'https://foody-media.s3.eu-west-1.amazonaws.com/carb.svg';
//mobile secssion ========================================================
?>

<div class="overview-lists-container">
    <ul class="overview-with-icons row">
        <?php foreach ($overview as $key => $value) : ?>

            <?php if ($key !== 'difficulty_level') {

                if ($key == 'calories_per_dish' && ($carbons <= 30)) {
                    $clrMB = $color_green;
                }
                if ($key == 'calories_per_dish' && ($carbons) > 30 && ($carbons) <= 45) {
                    $clrMB = $color_yellow;
                }
                if ($key == 'calories_per_dish' && ($carbons) > 45) {
                    $clrMB = $color_red;
                }
               
            ?>



                <li class="overview-item col-sm-3 col-3" style="background:<?php echo $clrMB; ?>">
                <?php if ($key == 'calories_per_dish') { ?>
                    <img class="carbicon" src="<?php echo $carbIcon ;?>"/>
                    <?php }?>
                   
                <?php if ($key === 'time') { ?>
                        <img src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['preparation_time']['icon'] ?>">
                        <div class="item-container">
                            <div class="key-value">
                                <span class="key"><?php echo $labels['preparation_time'] ?></span><?php echo ' ' . $value['preparation_time']['text'] ?>
                            </div>
                            <div class="key-value">
                                <span class="key"><?php echo $labels['total_time'] ?></span><?php echo ' ' . $value['total_time']['text'] ?>
                            </div>
                        </div>
                    <?php } elseif ($key == "calories_per_dish") {
                    ?>
                        <img style="visibility:hidden;" src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['icon-desktop'] ?>">
                        <div class="item-container">
                            <div class="key">
                                <?php echo 'פחמימות'; ?>
                            </div>
                            <div class="value <?php echo $key ?>">
                                <?php echo $carbons . ' גרם '  ; ?>
                            </div>
                        </div>
                    <?php


                    } else { ?>
                        <img src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['icon'] ?>">
                        <div class="item-container">
                            <div class="key">
                                <?php echo $labels[$key] ?>
                            </div>
                            <div class="value <?php echo $key ?>">
                                <?php echo $value['text'] ?>
                            </div>
                        </div>
                    <?php } ?>
                </li>


        <?php }
        endforeach; ?>

    </ul>
    <ul class="overview-no-icons row">
        <li class="overview-item col-sm-2 col-6">
            <div class="difficulty-container">
                <div class="key">
                    <?php echo $labels['difficulty_level'] ?>
                </div>
                <div class="value <?php echo 'difficulty_level' ?>">
                    <?php echo ' ' . $overview['difficulty_level']['text'] ?>
                </div>
            </div>
        </li>
        <li class="overview-item col-sm-2 col-6">
            <div class="value kosher">
                <?php echo __('כשר'); ?>
            </div>
        </li>
    </ul>
    <!-- <div class="ramzor_explain">
    <img src="https://foody-media.s3.eu-west-1.amazonaws.com/m_ramzor-fea.jpg"/>
</div> -->
    <?php
    if (get_current_blog_id() !== 2) { ?>
        <ul class="overview-nutrients row">
            <li class="overview-item col-sm-1 col-6">
                <div class="value open">
                    <?php echo __('עוד ערכים תזונתיים'); ?>
                </div>
                <section class="recipe-nutrition box no-print">

                    <?php $recipe->the_nutrition() ?>

                </section>
            </li>
        </ul>
    <?php } ?>
</div>
<?php // desktop secssion=======================================
?>
<div class="overview-lists-container-desktop">
    <ul class="overview row">
        <?php foreach ($overview as $key => $value) : ?>


            <?php if ($key !== 'difficulty_level') {

                if ($key == 'calories_per_dish' && ($carbons <= 30)) {
                    $clr = $color_green;
                }
                if ($key == 'calories_per_dish' && ($carbons) > 30 && ($carbons) <= 45) {
                    $clr = $color_yellow;
                }
                if ($key == 'calories_per_dish' && ($carbons) > 45) {
                    $clr = $color_red;
                }
            ?>
                <li class="overview-item col-1" style="background:<?php echo $clr; ?>">
                
                <?php if ($key == 'calories_per_dish') { ?>
                    <img class="carbicon" src="<?php echo $carbIcon ;?>"/>
                    <?php }?>
                    <?php if ($key === 'time') { ?>
                        <img src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['preparation_time']['icon-desktop'] ?>">
                        <div class="item-container">
                            <div class="key-value preparation-time">
                                <span class="key"><?php echo $labels['preparation_time'] ?></span><?php echo ' ' . $value['preparation_time']['text'] ?>
                            </div>
                            <div class="key-value">
                                <span class="key"><?php echo $labels['total_time'] ?></span><?php echo ' ' . $value['total_time']['text'] ?>
                            </div>
                        </div>
                    <?php } elseif ($key == "calories_per_dish") {
                    ?>
                        <img style="visibility:hidden;" src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['icon-desktop'] ?>">
                        <div class="item-container">
                            <div class="key">
                                <?php echo 'פחמימות'; ?>
                            </div>
                            <div class="value <?php echo $key ?>">
                                <?php echo $carbons . ' גרם '; ?>
                            </div>
                        </div>
                    <?php


                    } else {
                    ?>
                        <img src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['icon-desktop'] ?>">
                        <div class="item-container">
                            <div class="key">
                                <?php echo $labels[$key] ?>
                            </div>
                            <div class="value <?php echo $key ?>">
                                <?php echo $value['text'] ?>
                            </div>
                        </div>


                    <?php } ?>
                </li>


        <?php
            }
        endforeach; ?>

        <li class="overview-item difficulty col-1">
            <div class="difficulty-container">
                <div class="key">
                    <?php echo $labels['difficulty_level'] ?>
                </div>
                <div class="value <?php echo 'difficulty_level' ?>">
                    <?php echo ' ' . $overview['difficulty_level']['text'] ?>
                </div>
            </div>
        </li>
        <li class="overview-item col-1">
            <div class="value kosher">
                <span>
                    <?php echo __('כשר'); ?>
                </span>
            </div>
        </li>
    </ul>
    <!-- <div class="ramzor_explain">
    <img src="https://foody-media.s3.eu-west-1.amazonaws.com/m_ramzor-fea.jpg"/>
</div> -->
    <?php
    if (get_current_blog_id() !== 2) { ?>
        <ul class="overview-nutrients row">
            <li class="overview-item col-sm-1 col-6">
                <div class="value open">
                    <?php echo __('עוד ערכים תזונתיים'); ?>
                </div>
                <section class="recipe-nutrition box no-print">

                    <?php $recipe->the_nutrition() ?>

                </section>
            </li>
        </ul>
    <?php } ?>
</div>
<style>
.ramzor_explain{
    width:100%;
    text-align:center;
    padding:10px;
}

</style>