<div class="search-bar search-bar-container">      
<?php
 if(function_exists('get_search_form')  && $_SERVER['HTTP_HOST'] != 'foody.co.il' &&  get_post_type() != 'foody_recipe')
{get_search_form();} 
//foody_recipe
//echo $_SERVER['HTTP_HOST'] ;
//echo get_post_type();
?>
</div>