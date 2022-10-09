<span class="related-content-btn ">מתכונים נוספים</span>
<div class="Conductor_overlay hidden"></div>
<div class="related-recipes-container hidden">
    
	<div class="related_title">
        מתכונים נוספים שכדאי לכם לנסות    </div>
	<div class="close_related_btn">X</div>
	
	<?php 
	if(is_category()){
		$cat_id = get_query_var('cat');
		$list = array();
		
		  
$args = array(

			'post_status' => 'publish',
			'orderby' => 'post_date',
			'order' => 'DESC',
			'numberposts'      => 4,
			'category'         => $cat_id,
			'orderby'          => 'rand',
			'post_type'        => 'foody_recipe',
			'suppress_filters' => true,

);
		$recipes = get_posts($args);
		?>
						<div class="container">
						<div class="row">
				<?php
		
		foreach($recipes as $r){
			$img = get_the_post_thumbnail_url($r->ID);
			$title= $r->post_title;
			$lnk = get_permalink($r->ID);
			?>
				
				<div class="colish">
				<a href="<?php echo $lnk ;?>">
				<img class="related_img" src="<?php echo $img;?>"/>
				<div class="related_spn" ><?php echo $title;?></div>
				</a>
				</div>
				
			<?php
		}
		
		?>
				</div>
				</div>
		<?php
		
	}
	
	
	?>
	
	
	
</div>