<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/25/18
 * Time: 4:37 PM
 */

?>

<form class="navbar-form foody-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="search-bar d-none d-lg-block">
        <input name="s" type="text" class="search search-autocomplete" placeholder="חיפוש מתכון…">
        <input type="hidden" name="post_type" value="foody_recipe"/>
    </div>
</form>
