<?php 
class FoodyHeader_NewSite{
//images : 
public $hamburger;
public $pintres ;
public $print ; 
public $share_facebook;
public $share_instragram;
public $share_youtube;
public $share ;

function __construct()
{
    add_action( 'wp_enqueue_scripts', array($this,'my_enqueue_styles'), 1 );
    
$this->hamburger = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/hamburger.png";
$this->pintres = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/pintres.png";
$this->print = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/print.png";
$this->share_facebook ="https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share-facebook.png";
$this->share_instragram = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share-instragram.png";
$this->share_youtube="https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share-youtube.png";
$this->share = "https://foody-media.s3.eu-west-1.amazonaws.com/new-site/share.png";
}
public function my_enqueue_styles() {
    // Enqueue new site style
    wp_enqueue_style( 'new-site-style', get_template_directory_uri() . '/resources/sass/newsite/new_site_style.css', array(), '1.0.0' );
    add_action( 'wp_enqueue_scripts', array( $this, 'LazyLoadScript' ) );
}

public function LazyLoadScript() {
    // Enqueue your JavaScript file
    wp_enqueue_script(
        'foody_lazyLoad',           // Handle for the script
        get_template_directory_uri() . '/resources/js/new-site/lazyload.js',  // Path to the script file
        array('jquery'),               // Dependencies (optional)
        '1.0.0',                       // Version number
        true                           // Load in the footer (true) or header (false)
    );
}

public function LazyLoadImage($src, $alt, $title , $class){
return "<img data-src='{$src}' alt='{$alt}' class='lazyload {$class}'>";
}

}//END CLASS
?>
