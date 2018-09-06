<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/13/18
 * Time: 11:12 AM
 */


get_header();
$category_id = get_queried_object_id();

$category = new Foody_Category($category_id);



?>




<?php

get_footer();


