<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:07 PM
 */

$overview = $template_args['overview'];
$recipe = $template_args['recipe'];
$kcalories =  $overview['calories_per_dish']['text'];

$labels = array(
    'preparation_time' => 'הכנה',
    'total_time' => 'כולל',
    'difficulty_level' => 'רמת קושי:',
    'ingredients_count' => 'מרכיבים',
    'calories_per_dish' => 'פחמימות'
);

switch ($_SERVER['HTTP_HOST']) {
    case 'staging.foody.co.il' :
        include_once('content-recipe-overview-happyfood.php');
        break;
        default : 
        include_once('content-recipe-overview-foody.php');
        break;
}
?>