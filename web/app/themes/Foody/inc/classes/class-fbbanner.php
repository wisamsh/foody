<?php
class FB_Site_Banner_Campaign
{

    public $PopUpGroup;

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_assets'), 20);
        $this->PopUpGroup = get_field("add_popup_campaign_repeater", "options");
        // print_r($this->PopUpGroup);
    }


    public function GetCampagains()
    {
        $campaigns = [];
        $campaigns_Fetch =  $this->PopUpGroup;
        foreach ($campaigns_Fetch as $key => $camps) {
            $campaigns[] = $camps['fb_campagin'];
        }
        return $campaigns;
    }


    public function CheckBannerCamp()
    {
        $rtn = false;
        if ($this->PopUpGroup && !empty($this->PopUpGroup)) {
            return true;
        }
    }


    private function site_location()
    {
        $post_type = get_query_var('post_type');
        if (empty($post_type)) {
            // fallback for default post type archives
            if (is_singular()) {
                $post_type = get_post_type();
            } else {
                $post_type = 'post';
            }
        }
        return $post_type;
    }

    public function ShowPopUp()
    {
        return ($this->BannerTemplate($this->GetCampagains()));
    }


    public function CheckCampArea($area, $specified=null)
    {

        switch ($area) {
            case "all_site":
                    break;
                    case "foody_recipe":
                        if ( get_post_type() === 'foody_recipe' ) {
                            return true;
                        }
                    break;
                    case "posts":
                        if ( get_post_type() === 'posts' ) {
                            return true;
                        }
                    break;
                    case "pages":
                        if(is_page()){
                            return true;
                        }
                    break;
                    case "Home Page":
                        if(is_front_page() || is_home()){
                            return true;
                        }
                    break;
                    case "Specified":
                        if($specified !="" || !empty($specified)){
                            if(in_array(get_the_ID(), $specified)){
                                return true;
                            }
                        }
                    break;
                    default :
                    return false;
                    break;

        }



        // $all_site
        // $foody_recipe
        // $posts
        // $pages
        // $Home Page
        // $Specified
    }



    private function BannerTemplate($banner = array())
    {

        if (!empty($banner)) {
            $html = '';
            foreach ($banner as $banner) {
                $bannerID =  strtolower(str_replace(' ', '_', $banner['fp_campain_name']));
               

    if($banner['fb_area_picker'][0]!= 'Specified'){
            if($this->CheckCampArea($banner['fb_area_picker'][0])){
                $image = !wp_is_mobile() ? $banner['fb_desktop_banner']  : $banner['fb_mobile_banner'];
                $url = !wp_is_mobile() ? $banner['fb_desktop_link']  : $banner['fb_mobile_link'];
                $html .= "<div class='banner_wrapper' id='{$bannerID}' data-url='{$url}'";
                $html .= "<div class='banner_image'>";
                $html .= "<div class='close_banner' data-close='{$bannerID}'>X</div>";
                $html .= "<img class='image_banner' src='{$image}'/>";
                $html .= "</div>"; //Wrapper Closer 
                return $html;
            }
        }
            
        

        if($banner['fb_area_picker'][0]== 'Specified'){
            if($this->CheckCampArea($banner['fb_area_picker'][0], $banner['fb_location'])){
                $image = !wp_is_mobile() ? $banner['fb_desktop_banner']  : $banner['fb_mobile_banner'];
                $url = !wp_is_mobile() ? $banner['fb_desktop_link']  : $banner['fb_mobile_link'];
                $html .= "<div class='banner_wrapper' id='{$bannerID}' data-url='{$url}'";
                $html .= "<div class='banner_image'>";
                $html .= "<div class='close_banner' data-close='{$bannerID}'>X</div>";
                $html .= "<img class='image_banner' src='{$image}'/>";
                $html .= "</div>"; //Wrapper Closer 
                return $html;
            }
        }




            }
            return $html;
        }
    }

    public function enqueue_front_assets()
    {

        wp_enqueue_style(
            'foody_banner_style',
            get_template_directory_uri() . '/components/css/banner.css',
            '',
            '1.2.4'
        );

        // JS
        wp_enqueue_script(
            'foody_banner_script',
            get_template_directory_uri() . '/components/js/banner.js',
            '', // Dependencies
            '1.4.5',
            true // Load in footer
        );
    }
} //END CLASS
