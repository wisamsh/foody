<?php 
class Foody_Substitutes_Ingredients extends Foody_Ingredient{
	
	
	
	//WISAM========================Substitue Ingredients=========================================
	public function GetAlternativeIngredients(){
		$RecipeID =  get_the_ID();
		global $wpdb;
		$rtn = array();
		$Table_Perfix = $wpdb->prefix;
		$Get_The_Alternatives = ("SELECT ID from " . $Table_Perfix . "posts where post_type = 'replacements' and  post_title like '%" . $RecipeID  . "%' and post_status='publish'");
		//print_r($Get_The_Alternatives ) ;
		$Res = $wpdb->get_results($Get_The_Alternatives);
		foreach($Res as $Res){
			$rtn[] =$Res->ID;
		}
		return $rtn;
		
	}
	
	
	public function The_Sub_ingredients($id){
		
		$PostIds = $id;
		print_r($PostIds );
		$rtn = array();
		$i = 0 ;
		foreach($PostIds as $PostIds){	
			$rtn[$i]['ing_id'] = get_field('orginal_ing', $PostIds);
			$rtn[$i]['ing_post_id'] = $PostIds;
			$i ++;
		}
		
		return ($rtn);
		
		
	}
	
	
	public function Get_Substitutes_Details($sub_pid){
		
		$swap_type = get_field('swap_type', $sub_pid);
		$swap =array();
		
		switch($swap_type){
			case 'המרה מלאה' :
			$swap['full'] = get_field('full_swap', $sub_pid);
			
			break;
			case 'המרה חלקית' :
			$swap['part'] = get_field('part_swap', $sub_pid);
			break;
		}
		
		return $swap;
	}
	
	
	public function SwapStyle(){
		$rtn = '<style>
		@media only screen and (max-width: 600px) {
			.Swaploader img{
			margin-top: 60%;
			width:150px;
			height: 150px;
			
		}
		}
		
		@media only screen and (min-width: 600px) {
			.Swaploader img{
			margin-top: 18%;
			width:150px;
			height:150px;
			
		}
		}
		
		
		
		.dn{display:none !important;}
		.Swap_Color {background:#c8d7de !important;}
		.alter_content h2 {
    font-family: Ploni;
    color: #579fba;
    color: var(--color__primary);
    font-weight: 900;
    font-size: 24px;
	padding-right:10px;
	padding-top:5px;
}
		.alter_content img{
			padding:15px;
		}
		
			.Swaploader{
			width: 100%;
			text-align: center;
			position: fixed;
			top: 0;
			right: 0;
			left: 0;
			background: none;
			width: 100%;
			height: 100%;
			z-index: 1;
			text-align: center;
background:#ffffffa6;
			}
		
		
		
		
		
		.swap_text_in {
			font-size: 18px;
			color: #000;
			position: absolute;
			margin-left: 0px;
			left: 6px;
			margin-top: -23px;
			cursor: pointer;
			z-index: 1;
			 transition: all 0.5s ease;
			float:left;
			margin-top: 2px;
		display:inline-block;
		    text-decoration: underline;
			font-weight:bold;
		
		}
		
		.swap_text_out{
			font-size: 18px;
			color: #000;
			position: absolute;
			margin-left: 0px;
			left: 6px;
			margin-top: -23px;
			cursor: pointer;
			z-index: 1;
			 transition: all 0.5s ease;
			float:left;
			margin-top: 2px;
		display:inline-block;
		    text-decoration: underline;
			font-weight:bold;
		}
		
		.swap_text_in:hover{
			color: #579fba;
		}
		.swap_text_out:hover{
			color: #579fba;
		}
		.ing_wrapp{
			border:solid 1px #ffffff40;
			padding-bottom:0px !important;
		}
		.swap_img{
			max-width:26px;
			max-height:26px;
			cursor:pointer;
		}
		
		.ing_wrapp Swap_Color{
			overflow: initial !important;
			max-height: 500px !important;
		}

			.extra-ingredients{
				transition: all 0.5s ease;
			}	
		
	.commorcial_logo{
	max-width:60px;
	image-rendering: auto;
  image-rendering: crisp-edges;
  image-rendering: pixelated;
  image-rendering: -webkit-optimize-contrast;
  
  
image-resolution: 600dpi;
	
  
	}
		.cmtext{
			font-size:15px !important;
		}
		
		</style>';

return $rtn;
	}
	
	
	
	
	public function get_Swap_Type($sub_pid, $pid){
		$rtn = '';
		$Swap_Type = get_field('swap_type', $sub_pid);
		
		$PractionUnit = array(
		'1/2' => '0.5',
		'1/4' => '0.25',
		'1/3' => '0.3',
		'1/8' => '0.125',
		'3/4' => '0.75'
		
		);
		
		
		switch ($Swap_Type){
		case 'המרה מלאה':
		$Full_Swap = get_field('full_swap',$sub_pid);
		
		
		$rtn = '' ;
		$Swap_Title = $Full_Swap['swaped_ing']->post_title;
		$Swap_url = $Full_Swap['swaped_ing']->guid ;
		$Swap_Masurin_Unit = $Full_Swap['swaped_ing_masure']->name ;
		$Swap_Masurin_Unit_Many_ID = $Full_Swap['swaped_ing_masure']->term_id ;
		$term_tax = $Full_Swap['swaped_ing_masure']->taxonomy;
		$Swap_plural_name = get_field('plural_name', 'units_' . $Swap_Masurin_Unit_Many_ID);
		
		$Swap_Amount = $Full_Swap['swaped_ing_amount'];
		$orignal_Amount = $Full_Swap['swaped_ing_amount'];
		
		if($Swap_Amount > 1){
		$Swap_Masurin_Unit = $Swap_plural_name ;
		}
		
		if(in_array($Swap_Amount, $PractionUnit)){
		$Swap_Amount = array_search($Swap_Amount, $PractionUnit);
		}
		
		$Swap_Commorcial_Logo_Approve = $Full_Swap['abs_logo'];
		$Swap_Commorcial_Text_Approve = $Full_Swap['abs_text'];
		$Swap_Commorcial_Logo = get_field('client_com_logo',$sub_pid) ;
		$Swap_Commorcial_Text = get_field('client_com_text',$sub_pid) ;
		if($Swap_Commorcial_Logo_Approve && trim($Swap_Commorcial_Logo) != ''){
		$Go_Com_Logo = "<span class='cmlogo'><img src='$Swap_Commorcial_Logo' class='commorcial_logo'/></span>";
		}
		else{
		$Go_Com_Logo ='';
		}
		
		if($Swap_Commorcial_Text_Approve && trim($Swap_Commorcial_Text) != ''){
		$Go_Com_Text = "<span class='cmtext'>$Swap_Commorcial_Text</span>";
		}
		else{
		$Go_Com_Text ='';
		}
		
		
		$rtn = '
		
		<div class="extra-ingredients swapper_'.$sub_pid.' dn">
		<span class="ingredient-container"><b>
		<span dir="ltr" class="amount" data-amount="'.$orignal_Amount.'" data-original="'.$Swap_Amount.'"
		data-plural="" data-singular="'.$Swap_Title.'" data-unit="'.$Swap_Masurin_Unit.'"
		data-calories="0"
		data-carbohydrates="0" data-sugar="0" data-fats="0" data-sodium="0" data-protein="0"
		data-fibers="0" data-saturated_fat="0" data-cholesterol="0" data-calcium="0"
		data-iron="0" data-potassium="0" data-zinc="0">'.$Swap_Amount .'</span>
		<span class="ingredient-data"> <span class="unit">'.$Swap_Masurin_Unit.'</span>
		<span class="name"><a target="_self" title="'.$Swap_Title.'" class="foody-u-link"
		href="'.$Swap_url.'">
		'.$Swap_Title .'
		</a>
		</span>
		</span>
		</span></b>
		'. $Go_Com_Logo . $Go_Com_Text .'
		</div>
		';
		
		
		return $rtn;
		
		break;
		
		
		case 'המרה חלקית':
		$Part_Swap = get_field('part_swap',$sub_pid);
		//print_r($Part_Swap);
		$ingrediant_involved = $Part_Swap['ingrediant_involved'];
		//print_r($ingrediant_involved);
		foreach($ingrediant_involved as $ings){
		
		
		$Swap_Title = $ings['ing_inv']->post_title;
		$Swap_url = $ings['ing_inv']->guid;
		$Swap_Masurin_Unit =$ings['ing_inv_unit']->name ;
		$Swap_Amount = $ings['ing_inv_amount'];
		$orignal_Amount = $ings['ing_inv_amount'];
		
		$Swap_Masurin_Unit_Many_ID = $ings['ing_inv_unit']->term_id ;
		
		$Swap_plural_name = get_field('plural_name', 'units_' . $Swap_Masurin_Unit_Many_ID);
		
		if($Swap_Amount > 1){
		$Swap_Masurin_Unit = $Swap_plural_name ;
		}
		
		if(in_array($Swap_Amount, $PractionUnit)){
		$Swap_Amount = array_search($Swap_Amount, $PractionUnit);
		}
		
		$Swap_Commorcial_Logo_Approve = $ings['trt_logo'];
		$Swap_Commorcial_Text_Approve = $ings['trt_text'];
		$Swap_Commorcial_Logo = get_field('client_com_logo',$sub_pid) ;
		$Swap_Commorcial_Text = get_field('client_com_text',$sub_pid) ;
		
		if($Swap_Commorcial_Logo_Approve && trim($Swap_Commorcial_Logo) != ''){
		$Go_Com_Logo = "<span class='cmlogo'><img src='$Swap_Commorcial_Logo' class='commorcial_logo'/></span>";
		}
		else{
		$Go_Com_Logo ='';
		}
		
		if($Swap_Commorcial_Text_Approve && trim($Swap_Commorcial_Text) != ''){
		$Go_Com_Text = "<span class='cmtext'>$Swap_Commorcial_Text</span>";
		}
		else{
		$Go_Com_Text ='';
		}
		
		
		$rtn .= '
		
		<div class="extra-ingredients swapper_'.$sub_pid.' dn" style="width:100%;background:none;">
		<ul>
		<li style="display:block; background:none; margin-bottom:1px;border-bottom:solid 1px #ffffff40;">
		<span class="ingredient-container"> <b>
		<span dir="ltr" class="amount" data-amount="'.$orignal_Amount.'" data-original="'.$Swap_Amount.'"
		data-plural="" data-singular="'.$Swap_Title.'" data-unit="'.$Swap_Masurin_Unit.'"
		data-calories="0"
		data-carbohydrates="0" data-sugar="0" data-fats="0" data-sodium="0" data-protein="0"
		data-fibers="0" data-saturated_fat="0" data-cholesterol="0" data-calcium="0"
		data-iron="0" data-potassium="0" data-zinc="0">'.$Swap_Amount .'</span>
		<span class="ingredient-data"> <span class="unit">'.$Swap_Masurin_Unit.'</span>
		<span class="name"><a target="_self" title="'.$Swap_Title.'" class="foody-u-link"
		href="'.$Swap_url.'">
		'.$Swap_Title .'
		</a>
		</span>
		</span>
		</span></b>
		'. $Go_Com_Logo . $Go_Com_Text .'
		</li>
		</ul>
		
		</div>
		';
		
		
		
		$Go_Com_Text = '';
		$Go_Com_Logo = '' ;
		
		
		}
		
		return $rtn;
		break;
		
		}
		
		
		
	}
	




	
	
	public function get_text_image_Swap($sub_pid){
		$rtn = '' ;
		$Swap_prop = get_field('swap_link_type', $sub_pid);
		if($Swap_prop == 'Text'){
			$rtn = '<div class="swap_text_in" id="sw_'.$sub_pid.'" onclick="swapthis('.$sub_pid.');">'. get_field('text_link_swap_from' , $sub_pid) . '</div>' .
					'<div class="swap_text_out dn" id="prev_'.$sub_pid.'" onclick="swapthis('.$sub_pid.');">' . get_field('text_link_swap_two', $sub_pid) . '</div>';
		}
		
		if($Swap_prop == 'Image'){
			$rtn = '<div class="swap_text_in" id="sw_'.$sub_pid.'" onclick="swapthis('.$sub_pid.');"><img class="swap_img" src="'. get_field('img_before_swap' , $sub_pid) . '"/></div>' .
					'<div class="swap_text_out dn" id="prev_'.$sub_pid.'" onclick="swapthis('.$sub_pid.');"><img class="swap_img" src="' . get_field('img_after_swap', $sub_pid) . '"/></div>';
	
		}
		 return $rtn ;
		
		
	}
	
	
	
	
	public function swap_Script(){
		echo '
		
		<script>
		jQuery( document ).ready(function() {
   localStorage.setItem("CallerRecipe","")
});
		
		function swapthis(id){
			jQuery(".Swaploader").removeClass("dn");
			let stockers = [];
			let st = localStorage.getItem("CallerRecipe") ? JSON.parse(localStorage.getItem("CallerRecipe")) : [];
			if(!jQuery("#sw_" + id).hasClass("dn") && jQuery("#prev_" + id).hasClass("dn")){
				jQuery("#sw_" + id).addClass("dn");
				jQuery("#prev_" + id).removeClass("dn");
				jQuery(".Orginal_swapper_" +id).addClass("dn");
				jQuery(".swapper_" +id).removeClass("dn");
				jQuery("*[data-ingid=" + id  + "]").addClass("Swap_Color");

		
		st.push(id);

						
		localStorage.setItem("CallerRecipe", JSON.stringify(st));
				
						
				
			}
			
			
			else{
				jQuery("#sw_" + id).removeClass("dn");
				jQuery("#prev_" + id).addClass("dn");
				jQuery(".Orginal_swapper_" +id).removeClass("dn");
				jQuery(".swapper_" +id).addClass("dn");
				jQuery("*[data-ingid=" + id  + "]").removeClass("Swap_Color");
				
				//removing item from localk storge : 
				
						for( var i = 0; i < st.length; i++){ 

						if ( st[i] === id) { 

						st.splice(i, 1); 
						}
							
						}
				
				localStorage.setItem("CallerRecipe", JSON.stringify(st));
				
				
				
				
				
			}
			
			
			
			jQuery.ajax({
						type : "POST",
						url :"/wp/wp-admin/admin-ajax.php",
						
						data : {
						"action": "Substitute_Ajax_Call",
						"recipeID": '.get_the_ID().' , 
						"objects" : [localStorage.getItem("CallerRecipe")]
						}
						,
						success: function(response, status, jqXHR) {
							//jQuery(".alter_content").html("");
							
							let res = (jqXHR.getResponseHeader("ofenhakhana"));
							
							if( res !="ok") {
								jQuery(".alter_content").addClass("dn");
								jQuery(".alter_content").html(response)
								jQuery(".original_content").removeClass("dn");
								
								
							}
							if( res =="ok") {
								jQuery(".original_content").addClass("dn");
								jQuery(".alter_content").removeClass("dn");
								jQuery(".alter_content").html(response);
							}
						jQuery(".Swaploader").addClass("dn");
						
						}
						});
			
			
			
			
			
		}
		
		</script>
		
		
		
		';
		
	}
	
	
	
	
	
}

?>