<?php
add_action( 'wp_ajax_Poll_Ajax_Call', 'Poll_Ajax_Call' );
add_action( 'wp_ajax_nopriv_Poll_Ajax_Call', 'Poll_Ajax_Call' );

function Poll_Ajax_Call(){

           $data = explode("=", $_POST["data"]);

if (count($data) < 3){
echo ('<p class="no_res">לא מצאנו תרפיטים עבורך 
<img src="https://foody-media.s3.eu-west-1.amazonaws.com/w_images/sceptic.png" style="width:20px;"/>
יכול להיות שלא ענית על כל השאלות?
</p><br><br>');
die();
}
          $mydata = array();
 $combain = array();
 $pid = end($data);
 
  unset($data[0]);
 $count = count($data);
 unset($data[ $count ]);
 foreach($data as $k=>$v){
$postID = substr($v, 0, strpos($v, "&"));
$mydata[] = $postID;
 }
 


$poll_result = get_field("poll_result",$pid);


foreach( $poll_result as $res_key=>$res_val){

$diff = array_diff($res_val['question_poll_res'] , $mydata);

$diff2 = array_diff($mydata , $res_val['question_poll_res'] );

//if(count($diff) == 0){
if($diff == $diff2){

$combain = $res_val['question_poll_posts'] ;
}




}
 
 
 if(count($combain) == 0){
echo ('<p class="no_res">לא מצאנו תרפיטים עבורך 
<img src="https://foody-media.s3.eu-west-1.amazonaws.com/w_images/sceptic.png" style="width:20px;"/>
יכול להיות שלא ענית על כל השאלות?
</p><br><br>');
 }
 else{
 
$class= !wp_is_mobile() ? 'pol_res_desktop_image' : 'poll_res_mobile_image' ;
$rtn .= '<div class="container">';
            $rtn .= '<div class="row">';
$rtn .='<div class="col-12 pol_res_Main_title"><p>תפריטים במיוחד עבורך:</p></div>' ;
foreach($combain as $post){
$title = get_the_title($post);
$tumb =  '<img class="'.$class.'" src="'.get_the_post_thumbnail_url($post).'" />';


$rtn .= '<div class="col-12 col-lg-6 col-xl-6 text-center"><a target="_blank" href="/?p='.$post.'">'.$tumb;
$rtn .= '<span class="pol_res_title">'.$title.'</span>';
$rtn .= '<span class="pol_res_arrow">››</span>';
 $rtn .= '</a></div>'; //col

}

 $rtn .= '</div>'; //row
            $rtn .= '</div>'; //container

echo $rtn;

 }
 
 
 //print_r($mydata);
// echo $pid;


        die();
        }

?>