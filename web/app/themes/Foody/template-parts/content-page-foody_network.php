<?php
/**
 * The template used for displaying page content
 *
 * @package WordPress
 * @subpackage Minimag
 * @since Minimag 1.0
 */
?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
    if (get_field('fn_top_title')) {
        ?>
        <div class="top_title">
            <?php the_field('fn_top_title'); ?>
        </div>
        <?php
    }
    ?>

    <div class="entry-content">
        <section class="advantages">
            <div class="container">
                <?php
                if (have_rows('fn_advantages')) {
                    while (have_rows('fn_advantages')) {
                        the_row();
                        $icon = get_sub_field('icon');
                        if (get_sub_field('hilight')) {
                            $hclass = ' hilight';
                        } else {
                            $hclass = '';
                        }
                        ?>
                        <section class="advantage">
                            <div class="icon"><img src="<?php echo $icon['url']; ?>" alt="<?php echo $icon['alt']; ?>">
                            </div>
                            <h3 class="title<?php echo $hclass; ?>"><?php the_sub_field('title') ?></h3>
                            <div class="content"><?php the_sub_field('content') ?></div>
                        </section>
                        <?php
                    }
                }
                ?>
                <?php
                if (get_field('fn_image')) {
                    $main_image = get_field('fn_image');
                    ?>
                    <div class="main_image">
                        <img src="<?php echo $main_image['url']; ?>" alt="<?php echo $main_image['alt']; ?>">
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>

        <section class="video">
            <div class="container">
                <?php
                if (get_field('fn_video')) {
                    $video = get_field('fn_video');
                    ?>
                    <div class="video_wrapper">
                        <video width="100%" controls>
                            <source src="<?php echo $video['url']; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>

        <?php
        if (!get_field('hide_icons')) {
            ?>
            <section class="logos">
                <div class="container">
                    <?php
                    if (have_rows('fn_icons')) {
                        ?>

                        <?php
                        if (get_field('fn_icon_title')) {
                            ?>
                            <h2 class="icon_title">
                                <?php the_field('fn_icon_title'); ?>
                            </h2>
                            <?php
                        }
                        ?>

                        <div class="logos_wrapper">
                            <?php
                            while (have_rows('fn_icons')) {
                                the_row();
                                $logo = get_sub_field('image');
                                ?>
                                <div class="logo">
                                    <div class="logo_inner">
                                        <?php if (get_sub_field('link')) {
                                            ?>
                                            <a href="<?php echo get_sub_field('link'); ?>"><img
                                                        src="<?php echo $logo['url']; ?>"
                                                        alt="<?php echo $logo['alt']; ?>"></a>
                                            <?php
                                        } else {
                                            ?>
                                            <img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </section>
            <?php
        }
        ?>


        <section class="form">
            <div class="container">
                <h3 class="form_title">
                    <?php the_field('fn_form_title'); ?>
                </h3>
                <div class="form_wrapper">
                    <?php
                    $form_object = get_field('fn_form');
                    gravity_form_enqueue_scripts($form_object['id'], true);
                    gravity_form($form_object['id'], false, false, false, '', true, 1);
                    ?>
                </div>
            </div>
        </section>

        <section class="bottom_image">
            <div class="container">
                <h3><?php the_field('fn_bottom_image_title'); ?></h3>
                <?php
                $d_image = get_field('fn_bottom_image');
                $m_image = get_field('fn_bottom_image_mobile');
                ?>
                <div class="bottom_image_desktop">
                    <img src="<?php echo $d_image['url']; ?>" alt="<?php echo $d_image['alt']; ?>">
                </div>
                <div class="bottom_image_mobile">
                    <img src="<?php echo $m_image['url']; ?>" alt="<?php echo $m_image['alt']; ?>">
                </div>
            </div>
        </section>

        <div class="container">
            <?php the_content(); ?>

            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', "minimag") . '</span>',
                'after' => '</div>',
                'link_before' => '<span>',
                'link_after' => '</span>',
                'pagelink' => '<span class="screen-reader-text">' . esc_html__('Page', "minimag") . ' </span>%',
                'separator' => '<span class="screen-reader-text">, </span>',
            ));
            ?>
        </div>

    </div><!-- .entry-content -->

    <?php edit_post_link(esc_html__('Edit', "minimag"), '<div class="container no-padding"><div class="entry-footer"><span class="edit-link">', '</span></div><!-- .entry-footer --></div>'); ?>

</div><!-- #post-## -->