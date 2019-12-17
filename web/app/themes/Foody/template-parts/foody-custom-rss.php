<?php
/**
 * Template Name: custom-rss
 */
global $post;
$limitCount = 3; // The posts limit to show
$page_id = $post->ID;
$posts_list = get_field('post_list', $page_id);

$posts = array_map(function ($post){
    return $post['recipe'];
}, $posts_list);

// Setting up content type and charset headers
header('Content-Type: '.feed_content_type('rss-http').';charset='.get_option('blog_charset'), true);

// Setting up valid XML encoding
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<!-- Declaring XML Validators namespaces -->
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php do_action('rss2_ns'); ?>>
    <!-- Declaring channel with articles data -->
    <?php $dateTimeFormat = 'D, M d Y H:i:s'; ?>
    <channel>
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date($dateTimeFormat, get_lastpostmodified(), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'daily' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php foreach ($posts as $post): the_post() ?>
            <item>
                <image>
                    <url><?php echo get_the_post_thumbnail_url($post->ID,'post-thumbnail');?></url>
                </image>
                <title><?php the_title_rss(); ?></title>
<!--                <pubDate>--><?php //echo mysql2date($dateTimeFormat, get_post_time('Y-m-d H:i:s', true), false); ?><!--</pubDate>-->
                <dc:creator><?php the_author(); ?></dc:creator>
                <link><?php the_permalink_rss(); ?></link>
<!--                <guid isPermaLink="false">--><?php //the_guid(); ?><!--</guid>-->
<!--                <description><![CDATA[--><?php //the_excerpt_rss() ?><!--]]></description>-->
<!--                <content:encoded><![CDATA[--><?php //the_excerpt_rss() ?><!--]]></content:encoded>-->
                <?php rss_enclosure(); ?>
                <?php do_action('rss2_item'); ?>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>