<?php 
/**
 * Template Name: Email Verefication
 * Created by PhpStorm.
 * User: Wisam Shomar
 * Date: 5/8/2023
 */
get_header();
//TODO : need to change this page after first verefiction to be a panel for the user
//=====================================================================================

?>
<h1>הקטגוריות בהם אתם רשומים</h1>
<div class="container">
    <div class="row">
<?php
$Foody_Verfication = new Foody_Verfication ;
$e = ($_GET['e']);
$v = ($_GET['v']);
$res = $Foody_Verfication->CheckVerefictionCode($e, $v);
foreach($res as $res){
$Foody_Verfication->UpdateAndValidUser($res->email);
?>
<div class="col">
    <ul>
        <li>
            <label for="category_<?php echo $res->category_id ;?>"><?php echo $res->category_name ;?></label>
            <input type="checkbox" id="cat_<?php  echo $res->category_id  ;?>" name="category_<?php echo $res->category_id ;?>" checked/>
        </li>
    </ul>
</div>
<?php
}
?>

</div>
</div> 

<?php 
get_footer();
?>