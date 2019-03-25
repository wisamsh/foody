<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/25/18
 * Time: 4:37 PM
 */

?>

<form class="navbar-form foody-search-form" role="search" method="get" action="<?php echo home_url() ?>"  >
    <div class="search-bar d-none d-lg-block">
        <input name="s" type="text" class="search search-autocomplete" placeholder="<?php echo __('חפשו מתכון או כתבה…','foody')?>">
        <label class="icon" for="textbox">
            <img src="<?php echo $GLOBALS['images_dir'] .'icons/search-gray.png' ?>" />
        </label>
    </div>
</form>
