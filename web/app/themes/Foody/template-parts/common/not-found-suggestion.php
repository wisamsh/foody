<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 28/1/2020
 * Time: 2:41 PM
 */
/** @noinspection PhpUndefinedVariableInspection */
$already_have_sidebar = isset($template_args['have_sidebar']) ? $template_args['have_sidebar'] : false;

$homepage = new Foody_HomePage();
$homepage->init();
?>
<div class="homepage">

    <div class="content">

        <?php $homepage->promoted_items(); ?>

        <?php
        if (!is_multisite() || is_main_site()) {
            $num = wp_is_mobile() ? 4 : 6;
            echo do_shortcode('[foody_team max="' . $num . '" show_title="true" type="team"]');
        }
        ?>

        <section class="feed-container row">

            <?php if (!$already_have_sidebar) { ?>
                <section class="sidebar-container d-none d-lg-block">
                    <?php
                    echo "<aside class=\"sidebar col pl-0\">";

                    echo "<div class=\"sidebar-content\">";
                    dynamic_sidebar('foody-social');
                    echo "</div></aside>";
                    ?>
                </section>
            <?php } ?>

            <?php if ($already_have_sidebar){ ?>
            <section class="content-container col-lg-12 col-12">
                <?php }
                else{ ?>
                <section class="content-container col-lg-9 col-12">
                    <?php } ?>
                    <?php $homepage->feed(); ?>

                </section>

            </section>


    </div>

</div>