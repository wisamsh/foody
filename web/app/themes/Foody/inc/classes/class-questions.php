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

				$rtn = isset($link) ? '<a href=' . $link . ' target="_blank">' : '';
				$rtn .= '<picture>';
				$rtn .= '<source media="(min-width: 800px)" srcset="' . $src . '"> ';
				$rtn .= '<source media=" (max-width: 799px)" srcset="' . $src_mob . '"> ';
				$rtn .= '<img src="' . $src . '"/>';
				$rtn .= '</picture>';
				$rtn .= '</a>';

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
		}

		return $rtn;
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
		return $rtn ;
	}

	public function the_categories()
    {
        echo '<h2 class="title">' . __('קטגוריות') . '</h2>';
        echo get_the_category_list('', '', $this->pid());
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

public function getQuestionMainCategory(){
	$category = get_the_category();
	return $category[0]->term_id;


}
	public function the_details()
	{
		echo '<section class="technique-details-container">';
		bootstrap_breadcrumb();
		echo '</section>';
	}




	
	public function the_accessories()
    {
//         $posts = [];
//         $title = '';
// $pid = $this->pid();
//         while (have_rows('accessories',$pid)): the_row();
//             //$posts = get_sub_field('accessories');
//            // $title = get_sub_field('title');
//         endwhile;

      
    }





}
