<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/20/18
 * Time: 9:12 PM
 */

global $foody_page;

/** @var Foody_Playlist $playlist */
/** @noinspection PhpUndefinedVariableInspection */
$playlist = $template_args['page'];

$recipe = $playlist->get_current_recipe();


// set global post as current recipe
// so comments and globals will load
// correctly
global $post;

$post = $recipe->post;
?>

    <section class="d-none d-lg-block">
		<?php
		foody_get_template_part(
			get_template_directory() . '/template-parts/content-recipe-display.php',
			[
				'recipe' => $recipe
			]
		);
		?>
    </section>
    <section class="d-block d-lg-none">
		<?php
		$playlist->the_mobile_sidebar_content();
		?>
    </section>

<?php
// reset global post.
wp_reset_query();
