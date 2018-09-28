<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Malam WordPress 1.0
 */


get_header();

$search = new Foody_Search();

/*
        * {
        *  search:'asfgag',
        *  'types':[{
        *      type:'category|ingredient|technique|accessory',
        *      exclude:false,
        *      id:8
        *  }]
        * }
        * */

$args = [
    'types' => [
//        [
//            'type' => 'ingredient',
//            'id' => 2665,
//            'exclude' => false
//        ],
//        [
//            'type' => 'category',
//            'id' => 3,
//            'exclude' => false
//        ],
//        [
//            'type' => 'category',
//            'id' => 8,
//            'exclude' => false
//        ]
        [
            'id' => 236,
            'type' => 'limitation',
            'exclude' => true
        ]
    ]

];


//$search->query($args);

$homepage = new HomePage();
?>

    <div class="homepage">

        <?php $homepage->cover_photo() ?>


        <div class="content">
            <div class="row recipes-grid gutter-10 featured">
                <?php $homepage->featured() ?>
            </div>

            <?php $homepage->categories_listing() ?>

            <?php echo do_shortcode('[foody_team max="7" show_title="true"]') ?>

            <section class="feed-container row">

                <div class="feed-header d-none d-sm-block">
                    <h3 class="title d-sm-inline-block">
                        <?php __('Our Recommendations', 'foody') ?>
                        ההמלצות שלנו
                    </h3>
                </div>


                <aside class="sidebar col d-none d-sm-block pl-0">
                    <input name="search" type="text" class="search" title="search" placeholder="חיפוש מתכון…">
                    <div class="sidebar-content">
                        <?php $homepage->filter() ?>
                    </div>
                </aside>

                <section class="content-container col-sm-9 col-12">

                    <article class="feed row gutter-3 recipes-grid">
                        <?php $homepage->feed() ?>
                    </article>
                    <div class="show-more">
                        <img src="<?php echo $GLOBALS['images_dir'] . 'bite.png' ?>" alt="">
                        <h4>
                            לעוד מתכונים
                        </h4>
                    </div>

                </section>


            </section>


        </div>

        <!--        mobile filter -->
        <div class="filter-mobile d-block d-sm-none">
            <button class="navbar-toggler filter-btn" type="button" data-toggle="drawer"
                    data-target="#dw-p2">
                סינון
            </button>


        </div>
    </div>
<?php
get_footer();
