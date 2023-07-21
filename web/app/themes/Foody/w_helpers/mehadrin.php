<?php 
$mehadrin_scrip_disable = '<script type="text/javascript" src="//acc.magixite.com/license/la?litk=guwoig46a1m"></script>';
$google_mehadrin_tag_manager =`<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KQK843H');</script>
<!-- End Google Tag Manager -->

Additionally, paste this code immediately after the opening <body> tag:

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KQK843H"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->`;

if($_SERVER['HTTP_HOST'] == "mehadrin.foody.co.il") {
    echo $google_mehadrin_tag_manager ; 
    echo $mehadrin_scrip_disable ;


}
?>