<?php
class FB_Site_Banner_Campaign
{

    public $PopUpGroup;

    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_assets'), 20);
        $this->PopUpGroup = get_field("fb_campagin", "options");
        
    }

    public function CheckBannerCamp()
    {
        $rtn = false;
        if ($this->PopUpGroup && !empty($this->PopUpGroup)) {
            return true;
        }
    }

    private function BannerTemplate($banner = array())
    {
        if (!empty($banner)) {

            $html = "<div class='banner_wrapper' id='{$banner["id"]}' ";
            $html .= "<div class='banner_image' onclick='gotolink({$banner['fb_desktop_link']})'>";
            $html .= "<img class='desktop_banner' src='{$banner['fb_desktop_banner']}'/>";
            $html .= "</div>"; //Wrapper Closer 
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
