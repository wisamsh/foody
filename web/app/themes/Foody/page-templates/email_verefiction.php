<?php 
/**
 * Template Name: Email Verefication
 * Created by PhpStorm.
 * User: Wisam Shomar
 * Date: 5/8/2023
 */
get_header();


//bk : https://foody-media.s3.eu-west-1.amazonaws.com/w_images/background.png
$BackHomebutton = "https://foody-media.s3.eu-west-1.amazonaws.com/w_images/button_backhome.png";
//check :https://foody-media.s3.eu-west-1.amazonaws.com/w_images/checkok.png
$animated  = 'https://foody-media.s3.eu-west-1.amazonaws.com/w_images/ani_coock.gif';
?>

<div class="containerWrapp">
   
<?php
$Foody_Verfication = new Foody_Verfication ;
$e = ($_GET['e']);
$v = ($_GET['v']);
$resultsArr = [];
$res = $Foody_Verfication->CheckVerefictionCode($e, $v);
foreach($res as $res){
$resultsArr[] = $Foody_Verfication->UpdateAndValidUser($res->email);
}
if(!empty($resultsArr)){
echo "<img src='{$animated}' class='animated_cook'/>";
echo '<h3>' .'האימות הושלם בהצלחה' . '</h3>';
echo '<p>כיף שהצטרפת אלינו!</p>';
echo "<img src='{$BackHomebutton}' onclick='gohome();' class='homebtn'/>";

}
else{
    echo '<script>
    setTimeout(function() {
    window.location.href = "/";
}, 1000);
    </script>';
}
?>


</div> 
<style>
    .animated_cook{
        width:260px;
        margin: 0 auto;
        margin-left: 45px;
    }
    .containerWrapp p {
        font-size: 20px;
    }
    .containerWrapp{
        position: relative;
        text-align: center;
        max-width: 750px;
        min-height: 400px;
        height: auto;
        padding: 10px;
        margin : 0 auto;
        margin-top: 30px;
        margin-bottom: 30px;
        color: #57A0BB;
       /* background-image: url('https://foody-media.s3.eu-west-1.amazonaws.com/w_images/background.png');
        */
    background-repeat: no-repeat;
    background-position: bottom;
    background-position: bottom;
    background-size: contain;
    }
    .containerWrapp h3{
        text-align: center;
        font-size: 38px;
    }
    .homebtn{
        cursor: pointer;
    }
</style>

<script>
    function gohome(){
        window.location.href = "/";
    }
</script>
<?php 
get_footer();
?>
