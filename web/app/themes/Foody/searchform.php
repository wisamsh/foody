<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/25/18
 * Time: 4:37 PM
 */

?>

<form class="navbar-form foody-search-form" role="search" method="get" action="<?php echo home_url() ?>">
    <div class="search-bar d-none d-lg-block">
        <input name="s" type="text" class="search search-autocomplete" maxlength="50"
               placeholder="<?php echo get_theme_mod( 'foody_text_search_placeholder', __( 'חפשו מתכון או כתבה…', 'foody' ) ) ?>">
        <label class="icon" for="textbox">
            <img src="<?php echo $GLOBALS['images_dir'] . 'icons/search-gray.png' ?>" alt="search-icon"/>
        </label>
    </div>
</form>
