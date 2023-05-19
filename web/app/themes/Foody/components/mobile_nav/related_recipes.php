<span class="related-content-btn ">מתכונים נוספים</span>
<div class="Conductor_overlay hidden"></div>
<div class="related-recipes-container hidden">

	<div class="related_title">
		מתכונים נוספים שכדאי לכם לנסות </div>
	<div class="close_related_btn">X</div>

	<?php






	$cat_id = 0;
	$list = array();
	$ids = array();


	if (is_category()) {
		$cat_id = get_query_var('cat');
		$list = array();
		$ids = array();
	}
	if (is_front_page()) {
		//$cat_id = 0;
		$ids = get_field("mobile_more_recipe_main_page", 'option') ? get_field("mobile_more_recipe_main_page", 'option') : array();
	}
	if ('post' == get_post_type()) {
		$cat_id = 0;
		$ids = get_field("mobile_more_recipe_articles", 'option');
	}

	if ('foody_recipe' == get_post_type()) {
		$cat_cat = get_the_category();
		$cat_id = $cat_cat[0]->term_id;
		//$cat_name = $cat_cat[0]->term_id;
		//print_r($cat_cat);
		$ids = array();

		$term_list = wp_get_post_terms(get_the_ID(), 'category', ['fields' => 'all']);
		foreach ($term_list as $term) {
			if (get_post_meta(get_the_ID(), '_yoast_wpseo_primary_category', true) == $term->term_id) {
				// this is a primary category
				$cat_Name = $term->name;
				$cat_id = $term->term_id;
			}
		}


		$args_rec = array(

			'post__not_in' => array(get_the_ID()),
			'post_status' => 'publish',
			//'orderby' => 'post_date',
			//'order' => 'DESC',
			'numberposts' => 4,
			//'category_name' => $cat_Name,
			//'cat_' => $cat_id,
			'orderby' => 'rand',
			'post_type' => 'foody_recipe',


			'meta_query' => [
				[
					'key' => '_yoast_wpseo_primary_category',
					'compare' => 'IN',
					'value' => $cat_id,
					'type' => 'NUMERIC'
				]
			]


		);
	}



	if ('foody_feed_channel' == get_post_type()) {

		$items = array();
		$blocks = get_field('blocks', get_the_ID());
		$item_Blocks = ($blocks);



		foreach ($item_Blocks as $n => $k) {
			if (is_array($k) && $k['type'] == 'manual') {

				$manualItems = $k['items'];

				foreach ($manualItems as $j => $v) {
					$items[] = $v['post']->ID;
				}
			}
		}

		$ids = $items;
	}




	$args = array(

		'post__in' => $ids,
		'post_status' => 'publish',
		'orderby' => 'post_date',
		'order' => 'DESC',
		'numberposts' => 4,
		'category' => $cat_id,
		'orderby' => 'rand',
		'post_type' => 'foody_recipe',
		'suppress_filters' => true,

	);

	if ('foody_recipe' == get_post_type()) {

		$recipes = get_posts($args_rec);
	} else {

		$recipes = get_posts($args);
	}
	?>
	<div class="container">
		<div class="row">
			<?php

			foreach ($recipes as $r) {
				$img = get_the_post_thumbnail_url($r->ID);
				$title = $r->post_title;
				$lnk = get_permalink($r->ID);
			?>

				<div class="colish">
					<a href="<?php echo $lnk; ?>">
						<img class="related_img" src="<?php echo $img; ?>" />
						<div class="related_spn"><?php echo $title; ?></div>
					</a>
				</div>

			<?php
			}

			?>
		</div>
	</div>




</div>