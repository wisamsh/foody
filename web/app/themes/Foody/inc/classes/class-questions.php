<?php

class Foody_Questions extends Foody_Post
{

	private $foody_search;

	/**
	 * Foody_Technique constructor.
	 *
	 * @param $technique_post_id
	 */
	public function __construct(WP_Post $post = null, $load_content = true)
    {
        parent::__construct($post,  $load_content);

		$this->foody_search = new Foody_Search('feed_channel');
	}


	private function ImageVideoBanner($type, $src, $link = null)
	{
		switch ($type) {
			case 'image':
				$rtn = isset($link) && $link != null ? '<a href=' . $link . ' target="_blank">' : '';
				$rtn .= '<img src="' . $src . '" class="comm_banner"/>';
				$rtn .= isset($link) && $link != null ? '</a>' : '';
				break;

			case 'video':
				$rtn = '<iframe width="100%" height="200" src="' . $src . '"controls=0">
			</iframe>';
				break;

			default:
				$rtn = '<img src="' . $src . '" class="comm_banner"/>';
				break;
		}
		return $rtn;
	}


	public function doCommercialBanner($PostId = null)
	{
		$pid = isset($PostId) && $PostId != null ? $PostId : get_the_ID();
		$fq_banner_enabled = get_field('fq_banner_enabled', $pid);
		if ($fq_banner_enabled) {

			$fq_banner_type = get_field('fq_banner_type', $pid);
			if ($fq_banner_type == 'image') {

				$src = get_field('fq_commercial_banner_image', $pid);

				if(get_field('fq_commercial_banner_image_url')){
					$imagelink = get_field('fq_commercial_banner_image_url' ,$pid);
				}
	 
				$rtn = $this->ImageVideoBanner('image', $src, $imagelink);
			
			}
		}
	
		return $rtn ;
	}

public function Docommorcial(){
	return $this->doCommercialBanner();
}


	public function feed()
	{
		

		echo '<article class="content">';
		echo "article";
		echo '</article>';
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

	public function the_details()
	{
		echo '<section class="technique-details-container">';
		bootstrap_breadcrumb();
		the_title('<h1 class="title">', '</h1>');
		echo '</section>';
	}

	function __clone()
	{
	}
}
