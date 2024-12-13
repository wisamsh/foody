<?php 
 $domain = 'https://' . $_SERVER['HTTP_HOST'] ;
 $target1 = "foody.co.il";

?>
<?php 
if (strpos($domain, $target) !== false) {

?>
<!--taboola_div-->

<div id="taboola-below-article-thumbnails-1"></div>
<script type="text/javascript">
window._taboola = window._taboola || [];
_taboola.push({
mode: 'alternating-thumbnails-a',
container: 'taboola-below-article-thumbnails-1',
placement: 'Below Article Thumbnails 1',
target_type: 'mix'
});
</script>

Flush, place this code at the end of your <body> tag:

<script id="taboola_footer" type="text/javascript">
window._taboola = window._taboola || [];
_taboola.push({flush: true});
</script>
<?php 
}
?>