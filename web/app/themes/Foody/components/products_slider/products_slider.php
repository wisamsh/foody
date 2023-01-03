<?php
$api_accessories_array = $recipe->row_accessories();

$api_accessories = implode(',', $api_accessories_array);

$ShoppingCart = "https://foody-media.s3.eu-west-1.amazonaws.com/w_images/shopping-cart.png";
$inDomain = $_SERVER['HTTP_HOST'];

$BuyProdText = "לחץ לרכישה";
$OnsaleTEXT = "במבצע";

$Author = get_the_author();
$FeedChannel = "";
$product_cats = array();
if (get_field("shop_include_ids", "option") == "") {
	$shop_include_ids = get_field("shop_include_ids", "option");
} else {
	$shop_include_ids = get_field("shop_include_ids", "option") . ",";
}

foreach (get_the_category(get_the_ID()) as $cats_in_recipe) {
	$product_cats[] = $cats_in_recipe->name;
}



$recipes_discluded_from_shop = get_field("recipes_discluded_from_shop", "option");




//rules for feed channel comes first on hirarchi
$recipe_channel = get_field("recipe_channel", get_the_ID());
if (!trim($recipe_channel) == "") {

	$api_rules_feed_channel_repeater = get_field("api_rules_feed_channel_repeater", "option");





	foreach ($api_rules_feed_channel_repeater as $api_r_f_c_r) {
		if ($api_r_f_c_r['api_rules_feed_channel'] == $recipe_channel) {



			$FeedChannel = "feed";
			$shop_include_ids .= $api_r_f_c_r['api_rules_feed_channel_product_ids'];
			$api_rules_feed_channel_disclude_mark = $api_r_f_c_r["api_rules_feed_channel_disclude_mark"];
			$recipes_discluded_TRADEMARK_from_shop = implode(",", $api_rules_feed_channel_disclude_mark);
			$disclude_supplier = implode(",", $api_r_f_c_r["api_rules_feed_channel_disclude_supplier"]);
			$tarde_mark = $api_r_f_c_r['api_rules_feed_trade_mark_import'];

			break;
		}
	}
}


$shop_block_title = get_field("shop_block_title", "option");
$shutdown_shop_api = get_field("shutdown_shop_api", "option");
$pages_to_display = get_field("carousle_page_number", "option");

if (trim($pages_to_display) == "") {
	$pages_to_display = 9;
}



$queryAPI = "?chunk=3&page=" . $pages_to_display . "&accessories=" . $api_accessories . "&feed=" . $FeedChannel;

if ($recipes_discluded_TRADEMARK_from_shop != "") {

	$queryAPI .= "&disclude_trademark=" . $recipes_discluded_TRADEMARK_from_shop;
}

if ($disclude_supplier != "") {

	$queryAPI .= "&disclude_supplier=" . $disclude_supplier;
}

if ($Author != "") {
	$queryAPI .= "&author=" . trim($Author);
}

if ($tarde_mark != "") {
	$queryAPI .= "&trademark=" . $tarde_mark;
}
//if(!empty($product_cats)){
// $queryAPI .="&categories=". implode(",",$product_cats);
//}




if ($shutdown_shop_api == 0) {

	if (!in_array(get_the_ID(), $recipes_discluded_from_shop)) {



		$post_query = '';
		//if (!$shop_include_ids == "") {
		$post_query = "&product=" . $shop_include_ids;
		//}


		if ($inDomain == 'foody.moveodevelop.com' || $inDomain == 'foody-local.co.il') {
			$ApiDomain = 'https://shop-staging.foody.co.il/foodyapi' . $queryAPI . $post_query;
			//$ApiDomain = 'https://shop.foody.co.il/foodyapi' . $queryAPI . $post_query;
		} else {
			$ApiDomain = 'https://shop.foody.co.il/foodyapi' . $queryAPI . $post_query;
		}
		if (!wp_is_mobile()) {
			include(get_template_directory() . '/components/products_slider/desktop_slider.php');
		} else {
			include(get_template_directory() . '/components/products_slider/mobile_slider.php');
		}
?>



		<script>
			jQuery(document).ready(function() {


				jQuery.ajax({
					type: "GET",
					url: "<?php echo $ApiDomain; ?>",
					dataType: "json",

					success: function(res) {

						if (res.length > -1) {
							jQuery("#FoodyShopCarousel").show();
						}

						let indicators = '';
						let wrapper = '';
						let closer = '</div>';

						jQuery.each(res, function(n, rec) {

							if (n > 0) {
								firstactive = '';
							} else {
								firstactive = 'active';
							}

							let indicators = '<li data-target="#FoodyShopCarousel" data-slide-to="' + n + '" ' + firstactive + ' ></li>';
							//console.log("item---",res);

							jQuery(".carousel-indicators").append(indicators);



							wrapper = '<div class="carousel-item ' + firstactive + '" id="doc_' + n + '"></div>';

							jQuery(".carousel-inner").append(wrapper);
							//LOOPING ITEMS :

							jQuery.each(rec, function(i, item) {

								let title = item.title;
								let product_brand = item.product_brand;
								let Fetured_Image = item.Fetured_Image;
								let second_title = ''; //item.second_title;
								let regular_price = item.regular_price;
								let sale_price = item.sale_price;
								let price = item.price;
								let product_url = item.product_url;
								let currency = item.currency;



								let onsale = '';
								let onsale_div = '';
								let active = i > 0 ? '' : 'active';



								if (sale_price) {
									onsale = '<div class="onsale"><?php echo $OnsaleTEXT; ?></div>';

									onsale_div = '<div class="prod_price"><span class="price onsaleprice">' + regular_price + ' </span> <span class="sale">' + currency + sale_price + ' </span></div>';
								} else {

									onsale = '';
									onsale_div = '<div class="prod_price"><span class="price">' + currency + price + '</span></div>';
								}




								inner = '<a class="product_picker" target="_blank" href="' + product_url + '">' +
									'<div class="product_wrapper"><div class="dingo"><img class="shopping_cart" src="<?php echo $ShoppingCart; ?>"/>' + onsale + "</div>" +
									' <div class="product_holder">' +
									'<img src="' + Fetured_Image + '"/></div>' +
									'<div class="prod_title">' + title + '</div>' +
									'<div class="prod_brand">' + product_brand + '</div>' +
									'<div class="prod_second_title">' + second_title + '</div>' +
									onsale_div +
									'<div class="buy_prod"><?php echo $BuyProdText; ?></div>' +
									'</div></div></a>';


								jQuery("#doc_" + n).append(inner);

							});






						});




					},


				});




			});
		</script>


<?php
	} // close inarray recipe
} //close api closer from admin
?>