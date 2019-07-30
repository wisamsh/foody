<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/26/18
 * Time: 1:29 PM
 */
$grid = new FoodyGrid();


/** @var Foody_SearchPage $search */
/** @noinspection PhpUndefinedVariableInspection */
$search = $template_args['search'];
?>

<div class="container search-results">

    <div class="row gutter-3">

		<?php

		if ( have_posts() ) {
			$search->the_results();
		} else {
			$search->no_results();
		}
		?>

    </div>


</div>
