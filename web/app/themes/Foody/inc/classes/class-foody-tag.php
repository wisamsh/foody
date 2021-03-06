<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/19/18
 * Time: 12:11 PM
 */
class Foody_Tag extends Foody_Term implements Foody_ContentWithSidebar {


	// === Foody_ContentWithSidebar === //
	/**
	 * Foody_Tag constructor.
	 */
	public function __construct( $id ) {
		parent::__construct( $id );
	}


	/**
	 * @see Foody_ContentWithSidebar interface
	 */
	function the_details() {
		bootstrap_breadcrumb();
	}


	// === Foody_Term === //

	/**
	 * Extending classes should implement the
	 * relevant query function in @return string
	 * @see Foody_Query
	 * and return here the name of said function.
	 *
	 */
	protected function get_foody_query_handler() {
		return 'tag';
	}

	/**
	 * Arguments will be merged with the default args (@return array arguments to use in grid rendering.
	 * @see Foody_Term::feed()).
	 * Arguments will override default args.
	 * Note: returned arguments must contain the 'id' and 'header['title']'
	 * keys.
	 */
	protected function get_grid_args() {
		return [
			'id'     => 'tag-feed',
			'header' => [
				// TODO title copy with Boris
				'title' => sprintf( 'מתכוני %s', $this->title )
			]
		];
	}

    public function the_css()
    {
        if (isset($_GET) && isset($_GET['referer']) && $_GET['referer']) {
            $titles_color = get_field('titles_color', $_GET['referer']);
            if (!empty($titles_color)) {

                if (preg_match('/^#/', $titles_color) === false) {
                    $titles_color = "#$titles_color";
                }

                ?>
                <style id="feed-style">
                    .title {
                        color: <?php echo $titles_color?>;
                    }

                    a:hover {
                        color: <?php echo $titles_color ?>;
                    }

                    .block-see-more a {
                        color: <?php echo $titles_color ?>;
                    }
                </style>
                <?php
            }
        }
    }
}