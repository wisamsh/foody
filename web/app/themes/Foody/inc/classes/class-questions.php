<?php

class Foody_Questions extends Foody_Post
{

	private $foody_search;
	private function pid($request_pid = null)
	{
		return $pid = isset($request_pid) ?  $request_pid : get_the_ID();
	}

	public function Title()
	{
		return $Title = get_the_title();
	}

	public function __construct(WP_Post $post = null, $load_content = true)
	{
		parent::__construct($post,  $load_content);

		$this->foody_search = new Foody_Search('questions');
	}


	private function ImageVideoBanner($type, $src, $link = null, $src_mob = null)
	{
		switch ($type) {
			case 'image':

				$rtn = isset($link) && $link != null ? '<a href=' . $link . ' target="_blank">' : '';
				$rtn .= '<picture>';
				$rtn .= '<source media="(min-width: 800px)" srcset="' . $src . '"> ';
				$rtn .= '<source media=" (max-width: 799px)" srcset="' . $src_mob . '"> ';
				$rtn .= '<img src="' . $src . '"/>';
				$rtn .= '</picture>';
				$rtn .= isset($link) && $link != null ? '</a>' : '';

				break;




			case 'video':
				$src = 'https://www.youtube.com/embed/' . get_field('fq_type_video_embed', $this->pid());
				$VideoHeight = wp_is_mobile() ? '200' : '380';
				$rtn = '<iframe id="questionvideo" width="100%" height="' . $VideoHeight . '" src="' . $src . '"controls=0">
			</iframe>';
				break;
		}
		return $rtn;
	}

	public function Do_FoodyBeadcrumbs()
	{
		echo '<section class="accessory-details-container">';
		bootstrap_breadcrumb();
		echo '</section>';
	}

	public function doCommercialBanner($PostId = null)
	{
		$imagelink = '';
		$fq_banner_enabled = get_field('fq_banner_enabled', $this->pid());
		if ($fq_banner_enabled) {

			$fq_banner_type = get_field('fq_banner_type', $this->pid());
			if ($fq_banner_type == 'image') {

				$src = get_field('fq_commercial_banner_image', $this->pid());

				$src_mobile = get_field('fq_commercial_banner_image_mobile', $this->pid());
				if (get_field('fq_commercial_banner_image_url')) {
					$imagelink = get_field('fq_commercial_banner_image_url', $this->pid());
				}

				$rtn = $this->ImageVideoBanner('image', $src, $imagelink, $src_mobile);
			}
			return $rtn;
		}
	}

	public function MainQuestionImage()
	{

		$illustration_type = get_field('illustration_type', $this->pid());
		$MainPhoto = get_field('fq_type_image', $this->pid());
		$MainPhotoMobile = get_field('fq_type_image_mobile', $this->pid());
		$MainPhotoUrl = get_field('fq_type_image_url', $this->pid());
		$MainVideo = !empty(get_field('fq_type_video_embed', $this->pid())) ? get_field('fq_type_video_embed', $this->pid()) : '';
		switch ($illustration_type) {
			case 'image':
				$rtn = $this->ImageVideoBanner('image', $MainPhoto, $MainPhotoUrl, $MainPhotoMobile);
				break;
			case 'video':
				$rtn = $this->ImageVideoBanner('video', $MainVideo, '', '');
				break;
		}
		return $rtn;
	}

	public function the_categories()
	{
		echo '<h2 class="title">' . __('קטגוריות') . '</h2>';
		echo '<div class="categories">' . get_the_category_list('', '', $this->pid()) . '</div>';
	}


	public function the_featured_content($shortcode = false)
	{
	}

	public function the_sidebar_content($args = array())
	{
		echo '<section class="sidebar-section foody-search-filter">sidebar';

		//$foody_query = SidebarFilter::get_instance();
		//$foody_query->the_filter();

		echo '</section>';
		//dynamic_sidebar('questions');
		//dynamic_sidebar( 'foody-social' );
	}

	public function Mobileattr()
	{
		if (wp_is_mobile()) {
			echo '<style> 
				#masthead{display:none;}
				#content {
				padding-top: 0px; 
				}
				</style>';
		}
	}

	public function getQuestionMainCategory()
	{
		$category = get_the_category();
		return $category[0]->term_id;
	}

	public function getQuestionAllCats()
	{
		$catsarr = array();
		$category = get_the_category();

		foreach ($category as $k => $cat) {
			$catsarr[$k]['term_id'] = $cat->term_id;
			$catsarr[$k]['name'] = $cat->name;
		}

		return $catsarr;
	}



	public function the_details()
	{
		echo '<section class="technique-details-container">';
		bootstrap_breadcrumb();
		echo '</section>';
	}





	public function the_accessories()
	{

		$accessoriesARR = get_field('accessories', $this->pid());
		$accessories = $accessoriesARR['accessories'];
		if (!empty($accessories)) {
			$rtn = '<h2 class="title">אביזרים</h2>';
			$rtn .= '<div class="accessories"><ul class="post-categories">';
			foreach ($accessories as $accessory) {
				$rtn .= '<li>';
				$rtn .= '<a href="' . $accessory->guid . '"/>' . $accessory->post_title . '</a>';
				$rtn .= '</li>';
			}
			$rtn .= '</ul></div>';
			return $rtn;
		}
	}



public function the_categories_RAW(){
	$rtn = array();
	$categories = wp_get_post_categories(get_the_ID());
    foreach($categories as $k=>$category) {
		$rtn[] = get_the_category_by_ID($category);
	}
	return $rtn;
}



	public function the_accessories_RAW($with_theID = null)
	{
		$rtn = array();
		$rr= array();
		$accessoriesARR = get_field('accessories', $this->pid());
		$raw = $accessoriesARR['accessories'];
		if(!empty($raw)){
		foreach ($raw as $k => $acc) {
			if($with_theID != null){
			$rtn[$k]['ID'] = $acc->ID;
			}
			$rtn[$k] = $acc->post_title;
		}
		return array_merge($rr, $rtn);
	}
	else{
		return $rtn;
	}
	}


	public function the_Technics_RAW($with_theID = null)
	{
		$rtn = array();
		$rr= array();

		$accessoriesARR = get_field('techniques', $this->pid());
		
		$raw = $accessoriesARR['techniques'];
		if(!empty($raw)){
		foreach ($raw as $k => $acc) {
			if($with_theID != null){
			$rtn[$k]['ID'] = $acc->ID;
			}
		
			$rtn[$k] = $acc->post_title;
		}
		return array_merge($rr, $rtn);
		}
		else{
			return $rtn;
		}
		
		}
		
	

	public function the_Tags_RAW($with_theID = null)
	{
	$rtn=array();	
		
	$tags = get_the_tags($this->pid());
	if( !empty($tags) ){
		foreach($tags as $tag){
			$rtn[] = $tag->name;
		}
	}
		return $rtn ;
	}

	public function the_techniques()
	{
		$techniques = get_field('techniques', $this->pid());
		$accessories = $techniques['techniques'];
		if (!empty($accessories)) {
			$rtn = '<h2 class="title">טכניקות</h2>';
			$rtn .= '<div class="technics"><ul class="post-categories">';
			foreach ($accessories as $accessory) {
				$rtn .= '<li>';
				$rtn .= '<a href="' . $accessory->guid . '"/>' . $accessory->post_title . '</a>';
				$rtn .= '</li>';
			}
			$rtn .= '</ul></div>';
			return $rtn;
		}
	}


	public function the_tags()
	{
		$tags = get_the_tags($this->pid());

		if (!empty($tags)) {
			$rtn = '<h2 class="title">תגיות</h2>';
			$rtn .= '<div class="tags"><ul class="post-categories">';
			foreach ($tags as $tag) {
				$rtn .= '<li>';
				$rtn .= '<a href="/tag/' . $tag->slug . '"/>' . $tag->name . '</a>';
				$rtn .= '</li>';
			}
			$rtn .= '</ul></div>';
			return $rtn;
		}
	}

	public function get_answers($pid)
	{
		$answers_array = get_field("answers", $pid);
		return $answers_array;
	}

	private function questions_args()
	{
		$defaults = array(
			'numberposts'      => 500,
			'category'         => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => array(),
			'exclude'          => array(),
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'questions',
			'post_status'      => array('publish'),
			'suppress_filters' => true,

		);

		return $defaults;
	}


	public function get_all_Questions($args = null)
	{
		$items = array();
		if (!$args) {
			$defaults = $this->questions_args();
		} else {
			$defaults = $args;
		}

		$All_Questions = get_posts($defaults);
		foreach ($All_Questions as $k => $question) {
			$answerarra = get_field('answers', $question->ID);
			$items[$k]['ID'] = $question->ID;
			$items[$k]['post_title'] =  str_replace('?', '', $question->post_title);
			$items[$k]['post_name'] = $question->post_name;

			$items[$k]['thumbnail'] = get_the_post_thumbnail_url($question->ID);
			$items[$k]['answer'] = $answerarra[0]['answer_ind'];
		}



		return $items;
	}





}//class ends here