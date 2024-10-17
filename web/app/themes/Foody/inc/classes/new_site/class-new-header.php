<?php
class FoodyHeader_NewSite
{
    //images : 
    public $hamburger;
    public $pintres;
    public $print;
    public $share_facebook;
    public $share_instragram;
    public $share_youtube;
    public $share;
    public $foodload;
    public $menucloser;
    public $Zoomimg;
    public $Whatsup;
    public $user_icon;
    public $accessebility;
    public $whitezoom ;

    function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'my_enqueue_styles'), 1);

        $this->hamburger = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/hamburger.png";
        $this->pintres = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/pintres.png";
        $this->print = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/print.png";
        $this->share_facebook = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share-facebook.png";
        $this->share_instragram = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share-instragram.png";
        $this->share_youtube = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share-youtube.png";
        $this->share = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share.png";
        $this->foodload = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/foodloading.gif";
        $this->menucloser = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/closer.png";
        $this->Zoomimg = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/zoom.png";
        $this->Whatsup = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/zoom.png";
        $this->user_icon = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/user-icon.png";
        $this->accessebility = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/accessibility.png";
        $this->whitezoom ="https://foody-media.s3.eu-west-1.amazonaws.com/new-site/zoom-white.png";
    }
    public function my_enqueue_styles()
    {
        // Enqueue new site style

        wp_enqueue_style('new-site-style', get_template_directory_uri() . '/resources/sass/newsite/new_site_style.css', array(), '1.0.0');
        add_action('wp_enqueue_scripts', array($this, 'LazyLoadScript'));
        add_action('wp_enqueue_scripts', array($this, 'mainpage_New_SiteJSEnqueueScript'));
    }

    public function LazyLoadScript()
    {
        // Enqueue your JavaScript file
        wp_enqueue_script(
            'foody_lazyLoad',           // Handle for the script
            get_template_directory_uri() . '/resources/js/new-site/lazyload.js',  // Path to the script file
            array('jquery'),               // Dependencies (optional)
            '1.0.0',                       // Version number
            true                           // Load in the footer (true) or header (false)
        );
    }


    public function mainpage_New_SiteJSEnqueueScript()
    {
        wp_enqueue_script(
            'foody_MainPageJS',           // Handle for the script
            get_template_directory_uri() . '/resources/js/new-site/main_page_sitescript.js',  // Path to the script file
            array('jquery'),               // Dependencies (optional)
            '1.0.0',                       // Version number
            true                           // Load in the footer (true) or header (false)
        );
    }

    public function LazyLoadImage($datasrc, $beforsrc,  $alt = null, $title = null, $class = null)
    {
        $beforsrc = '' ?? $this->foodload;
        return "
        <img  data-src='{$datasrc}' src='{$beforsrc}'  alt='{$alt}' class='lazyload {$class}' />
        ";
    }

    public function GetLogo()
    {
        $rtn =  '';
        if (function_exists('the_custom_logo') && has_custom_logo()) {
            $custom_logo_id = get_theme_mod('custom_logo');
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            if ($logo) {
                $rtn =  '<a href="' . esc_url(home_url('/')) . '">';
                $rtn .=  '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="custom-logo">';
                $rtn .=  '</a>';
            }
        } else {
            // Fallback: Display site title or text logo if no custom logo is set
            $rtn .=  '<a href="' . esc_url(home_url('/')) . '" class="site-title">' . get_bloginfo('name') . '</a>';
        }
        return $rtn;
    }
    public function GetMainHeader()
    {
        $rtn = "
    <div class='container_wrapper'>
        <div id='mainmenucontainer' class='container-fluid text-center primary_menu_container'>
    <div class='row align-items-center' style='max-width:1200px;margin:0 auto'>
      <div class='col' style='text-align:right'>
      <img src='{$this->hamburger}' class='mainhamburger db'/>
      <img src='{$this->menucloser}' class='manucloser dn'/>
      </div>
      <div class='col' style='text-align:center'>
      " . $this->GetLogo() . "
      </div>

      <div class='col' style='text-align:left'>
        {$this->rightMenuSharing()}
      </div>
    </div>
    </div>
  </div>
   </div>
  ";
        return $rtn;
    }


    public function rightMenuSharing()
    {
        $rtn = "
<div class='container text-center leftsidebar desktop'>
<div class='row justify-content-end p-0 '>
<div class='coljustify-content-center  p-1 m-l-2' style='position:relative;'>
<div class='loginspn ddsm'><img src='{$this->user_icon}' class='iconsize'/></div>
<div class='loginspn'>
<img src='{$this->Zoomimg}' class='iconsize' id='searchzoom'/>
<input type='text' class='newsearchBox dn' id='searchtext'/>
<img src='{$this->menucloser}' class='closesearchbox dn' id='closesearchbox' style='height:12px;' />
</div>
<div class='loginspn'> <img src='{$this->share}' class='iconsize'/></div>
<div class='loginspn'>
<img src='{$this->accessebility}' class='iconsize icon-acces'/></div>
</div>
</div>
</div>
    ";

//mobiles and ipads : 
$rtn .= "
<div class='container text-center leftsidebar mobile'>
<div class='row justify-content-end p-0 '>
<div class='coljustify-content-center  p-1 m-l-2' style='position:relative;'>
<div class='loginspn ddsm'>
<img src='{$this->user_icon}' class='iconsize'/></div>
<div class='loginspn'>
<img src='{$this->Zoomimg}' class='iconsize' id='searchzoommbl'/>
</div>
<div class='loginspn'>
 <img src='{$this->share}' class='iconsize'/></div>
<div class='loginspn'>
<img src='{$this->accessebility}' class='iconsize icon-acces'/></div>
</div>
</div>
<div class='searchWrapper '>
<input type='text' class='searchtextmobile' id='searchtextmobile' placeholder='חפש...  '/>

</div>
</div>


";


        return $rtn;
    }
} //END CLASS
