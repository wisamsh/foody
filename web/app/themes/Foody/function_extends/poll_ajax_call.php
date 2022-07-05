<?php 
add_action( 'wp_ajax_Poll_Ajax_Call', 'Poll_Ajax_Call' );
add_action( 'wp_ajax_nopriv_Poll_Ajax_Call', 'Poll_Ajax_Call' );

		function Poll_Ajax_Call(){
           $data = implode("&", $_POST);
           print_r($data);
            

        die();
        }

?>