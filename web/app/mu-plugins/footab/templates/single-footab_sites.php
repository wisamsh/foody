<?php

$prefix = '_footab_';

$footab_site_data = array();

$taboola_code_head = htmlspecialchars("<script type=\"text/javascript\">
  window._taboola = window._taboola || [];
  _taboola.push({article:'auto'});
  !function (e, f, u, i) {
    if (!document.getElementById(i)){
      e.async = 1;
      e.src = u;
      e.id = i;
      f.parentNode.insertBefore(e, f);
    }
  }(document.createElement('script'),
  document.getElementsByTagName('script')[0],
  '//cdn.taboola.com/libtrc/foody-foody/loader.js',
  'tb_loader_script');
  if(window.performance && typeof window.performance.mark == 'function')
    {window.performance.mark('tbl_ic');}
</script>");
$taboola_code_footer = htmlspecialchars("<script type=\"text/javascript\">
                window._taboola = window._taboola || [];
                _taboola.push({flush: true});
            </script>");

$footab_analytics_id = get_post_meta(get_the_ID(), $prefix . 'analitics_id', true) ? get_post_meta(get_the_ID(), $prefix . 'analitics_id', true) : '';
if ($footab_analytics_id) {
    $footab_analytics_code = htmlspecialchars("<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . $footab_analytics_id . "\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '" . $footab_analytics_id . "');
</script>");
} else {
    $footab_analytics_code = false;
}

$footab_site_data[$prefix . 'show_module'] = get_post_meta(get_the_ID(), $prefix . 'show_module', true) ? true : false;
$footab_site_data[$prefix . 'test_mode'] = get_post_meta(get_the_ID(), $prefix . 'test_mode', true) ? true : false;
$footab_site_data[$prefix . 'top_image'] = get_post_meta(get_the_ID(), $prefix . 'top_image', true) ? get_post_meta(get_the_ID(), $prefix . 'top_image', true) : '';
$footab_site_data[$prefix . 'top_text'] = get_post_meta(get_the_ID(), $prefix . 'top_text', true) ? get_post_meta(get_the_ID(), $prefix . 'top_text', true) : '';
$footab_site_data[$prefix . 'taboola_code'] = get_post_meta(get_the_ID(), $prefix . 'taboola_code', true) ? htmlspecialchars(get_post_meta(get_the_ID(), $prefix . 'taboola_code', true)) : '';
$footab_site_data[$prefix . 'border_color'] = get_post_meta(get_the_ID(), $prefix . 'border_color', true) ? get_post_meta(get_the_ID(), $prefix . 'border_color', true) : '';
$footab_site_data[$prefix . 'border_location'] = get_post_meta(get_the_ID(), $prefix . 'border_location', true) ? get_post_meta(get_the_ID(), $prefix . 'border_location', true) : '';

$footab_site_data[$prefix . 'taboola_code_head'] = get_post_meta(get_the_ID(), $prefix . 'taboola_code_head', true) ? htmlspecialchars(get_post_meta(get_the_ID(), $prefix . 'taboola_code_head', true)) : $taboola_code_head;
$footab_site_data[$prefix . 'taboola_code_footer'] = get_post_meta(get_the_ID(), $prefix . 'taboola_code_footer', true) ? htmlspecialchars(get_post_meta(get_the_ID(), $prefix . 'taboola_code_footer', true)) : $taboola_code_footer;
$footab_site_data[$prefix . 'analytics_code'] = $footab_analytics_code;


echo json_encode($footab_site_data);


?>