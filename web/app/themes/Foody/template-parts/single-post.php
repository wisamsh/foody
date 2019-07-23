<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/14/18
 * Time: 1:26 PM
 */

global $foody_page;

/** @var Foody_Article $playlist */
/** @noinspection PhpUndefinedVariableInspection */
$article = $template_args['page'];


foody_get_template_part(
	get_template_directory() . '/template-parts/content-article-display.php',
	[
		'article' => $article
	]
);


