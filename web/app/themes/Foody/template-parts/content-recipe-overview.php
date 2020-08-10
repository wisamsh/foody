<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:07 PM
 */

$overview = $template_args['overview'];
$recipe = $template_args['recipe'];

$labels = array(
    'preparation_time' => 'הכנה',
    'total_time' => 'כולל',
    'difficulty_level' => 'רמת קושי:',
    'ingredients_count' => 'מרכיבים',
    'calories_per_dish' => 'קלוריות'
);

?>
<div class="overview-lists-container">
    <ul class="overview-with-icons row">
        <?php foreach ($overview as $key => $value) : ?>
            <?php if ($key !== 'difficulty_level') { ?>
                <li class="overview-item col-sm-3 col-3">
                    <?php if ($key === 'time') { ?>
                        <img src="<?php echo $GLOBALS['images_dir'] . 'icons/' . $value['preparation_time']['icon'] ?>">
                        <div class="item-container">
                            <div class="key-value">
                                <span class="key"><?php echo $labels['preparation_time']  ?></span><?php echo  ' ' . $value['preparation_time']['text'] ?>
                            </div>
                            <div class="key-value">
                                  <span class="key"><?php echo $labels['total_time']  ?></span><?php echo ' ' . $value['total_time']['text'] ?>
                            </div>
                        </div>
                    <?php } else { ?>
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
                <?php echo $labels['difficulty_level']  ?>
            </div>
            <div class="value <?php echo 'difficulty_level' ?>">
                <?php echo  ' ' . $overview['difficulty_level']['text'] ?>
            </div>
            </div>
        </li>
        <li class="overview-item col-sm-2 col-6">
            <div class="value kosher">
                <?php echo __('כשר'); ?>
            </div>
        </li>
    </ul>
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
</div>