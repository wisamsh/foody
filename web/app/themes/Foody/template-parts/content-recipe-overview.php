<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:07 PM
 */

$labels = array(
    'preparation_time' => 'זמן הכנה',
    'total_time' => 'זמן כולל',
    'difficulty_level' => 'רמת קושי',
    'ingredients_count' => 'מרכיבים'
);

?>

<ul class="overview row">

<?php foreach ($template_args as $key=>$value) :?>

    <li class="overview-item col-sm-4 col-6">
        <div class="key">
            <?php echo $labels[$key] ?>
        </div>
        <div class="value">
            <?php echo $value ?>
        </div>
    </li>


<?php endforeach;?>

</ul>