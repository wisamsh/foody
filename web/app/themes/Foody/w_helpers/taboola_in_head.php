


<?php
$MySiteID =  (get_blog_details()->blog_id);
if ($MySiteID == 1 && get_post_type() == 'foody_recipe') {
?>
  <script id="taboola_head" type="text/javascript">
    window._taboola = window._taboola || [];
    _taboola.push({
      article: 'auto'
    });
    ! function(e, f, u, i){
    if (!document.getElementById(i)) {
      e.async = 1;
      e.src = u;
      e.id = i;
      f.parentNode.insertBefore(e, f);
    }
    }(document.createElement('script'),
      document.getElementsByTagName('script')[0],
      '//cdn.taboola.com/libtrc/foody-foody/loader.js',
      'tb_loader_script');
    if (window.performance && typeof window.performance.mark == 'function') {
      window.performance.mark('tbl_ic');
    }
  </script>
<?php
}
?>