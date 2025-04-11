<?php
class FB_Site_Banner_Campaign
{

    public $PopUpGroup;

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_assets'), 20);
        $this->PopUpGroup = get_field("add_popup_campaign_repeater", "options");
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


    private function BannerTemplate($banner = array())
    {
        if (!empty($banner)) {
            foreach ($banner as $banner) {



                $image = !wp_is_mobile() ? $banner['fb_desktop_banner']  : $banner['fb_mobile_banner'];
                $url = !wp_is_mobile() ? $banner['fb_desktop_link']  : $banner['fb_mobile_link'];
                $html = "<div class='banner_wrapper' id='{$banner["id"]}' data-url='{$url}'";
                $html .= "<div class='banner_image'>";
                $html .= "<div class='close_banner'>X</div>";
                $html .= "<img class='image_banner' src='{$image}'/>";
                $html .= "</div>"; //Wrapper Closer 
                return $html;



            }
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
