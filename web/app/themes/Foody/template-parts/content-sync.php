<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 5:01 PM
 */

$sites = get_sites(['site__not_in' => get_current_blog_id()]);

?>

<?php /** @var WP_Site $site */
foreach ($sites as $site) : ?>

    <div>

        <input id="<?php echo $site->id ?>" type="checkbox" name="<?php echo $site->id ?>">
        <label for="<?php echo $site->id ?>">
            <?php echo $site->blogname; ?>
        </label>
    </div>

<?php endforeach; ?>
