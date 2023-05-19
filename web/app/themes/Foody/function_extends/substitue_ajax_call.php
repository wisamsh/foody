<?php 

add_action( 'wp_ajax_Substitute_Ajax_Call', 'Substitute_Ajax_Call' );
add_action( 'wp_ajax_nopriv_Substitute_Ajax_Call', 'Substitute_Ajax_Call' );

		function Substitute_Ajax_Call(){
			
		//print_r($_POST);
		global $wpdb;
		$RecipeID = $_POST['recipeID'] ;
		$Table_Perfix = $wpdb->prefix;
				$Get_The_Actrions = ("SELECT ID from " . $Table_Perfix . "posts where post_type = 'baking_proccess' and  post_title like '%" . $RecipeID  . "%'");
				$Res = $wpdb->get_results($Get_The_Actrions);

		 $MyObjectRequest =$_POST['objects']; 
		
		
			$CleanObjectArr = str_replace(array( '[', ']' ), '', $MyObjectRequest[0]); //str_replace(']', '' ,$MyObjectRequest[0]);
			
			
			
				if(trim($CleanObjectArr) == ''){
						header("ofenhakhana: nothing");
						
				}
				
		if(trim($CleanObjectArr) !=''){
		$making_of_ing = get_field('making_of_ing',$Res[0]->ID);
		
		
		$Array_Tocheck = explode("," , $CleanObjectArr);
		
		
		foreach($making_of_ing as $val){
		$Array_Block = $val['ing_making_of_picker'];
		$ofen_hakhana = $val['ofen_hakhana'];
		
		if(count($Array_Block) == count($Array_Tocheck)){
		
			$result=array_diff($Array_Block,$Array_Tocheck);
			
			if(is_array($result) && empty($result)){
				header("ofenhakhana: ok");
				echo (($ofen_hakhana));
				break ;
			}
				
				
		}
		
		
		}
		
		
		}
		

		//Wisam Say : Important to die end of line , no wp continues
		die();
		}
