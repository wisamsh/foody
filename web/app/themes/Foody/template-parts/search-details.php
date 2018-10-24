<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/26/18
 * Time: 1:07 PM
 */
?>
<div>
    <?php bootstrap_breadcrumb(); ?>

    <div class="search-results-count">
        <?php
        global $wp_query;
        $count = $wp_query->found_posts;
        printf('תוצאות חיפוש (%s):', $count);
        ?>
    </div>
</div>

