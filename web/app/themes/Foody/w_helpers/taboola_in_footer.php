<?php 
 
  $site_id = get_current_blog_id();
?>
<?php 
if ($site_id == 1 ||  $_SERVER['HTTP_HOST'] == "staging.foody.co.il" || $_SERVER['HTTP_HOST'] == "foody.co.il") {

?>
<!--taboola_div-->
<div style="width:960px;margin:0 auto; position:relative" id="wt_ab">
<div id="taboola-below-article-thumbnails-1"></div>
</div>
<script type="text/javascript">
window._taboola = window._taboola || [];
_taboola.push({
mode: 'alternating-thumbnails-a',
container: 'taboola-below-article-thumbnails-1',
placement: 'Below Article Thumbnails 1',
target_type: 'mix'
});
</script>



<script id="taboola_footer" type="text/javascript">
window._taboola = window._taboola || [];
_taboola.push({flush: true});
</script>
<?php 
}
?>