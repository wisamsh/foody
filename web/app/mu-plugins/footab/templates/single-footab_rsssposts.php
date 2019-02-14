<?php header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
$postCount = 100; // The number of posts to show in the feed
$posts = query_posts('showposts=' . $postCount);
$prefix = '_footab_';
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
     xmlns:thr="http://purl.org/syndication/thread/1.0"
     xmlns:media="http://search.yahoo.com/mrss/"
    <?php do_action('rss2_ns'); ?>>
    <channel>
        <title><?php the_title(); ?></title>
        <atom:link href="<?php the_permalink(); ?>" rel="self" type="application/rss+xml"/>
        <link><?php bloginfo('url'); ?></link>
        <description><?php bloginfo('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters('rss_update_frequency', '1'); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>

        <?php
        $footab_postsrss_cache = get_post_meta(get_the_ID(), $prefix . 'posts_list_cache', true);
        $footab_postsrss_array = array();
        if (!isset($footab_postsrss_cache) || !$footab_postsrss_cache ){
            $posts_list = get_post_meta(get_the_ID(), $prefix . 'posts_list', true);
            foreach ((array)$posts_list as $key => $entry) {
                $post_title = "";
                $post_link = "";

                if (isset($entry[$prefix . 'post_title'])) {
                    $post_title = esc_html($entry[$prefix . 'post_title']);
                }

                if (isset($entry[$prefix . 'post_link'])) {
                    $post_link = esc_html($entry[$prefix . 'post_link']);
                }

                if (isset($post_link) && $post_link) {
                    $response = wp_remote_get($post_link, array( 'timeout' => 20 ));
                    $body = wp_remote_retrieve_body($response);
                    $json = json_decode(trim($body));
                    $mediaid = $json->featured_media;
                    $medialink = $json->_links->{'wp:featuredmedia'}[0]->href;
                    $mediajson = json_decode(trim(wp_remote_retrieve_body(wp_remote_get($medialink, array( 'timeout' => 20 )))));
                    $authorid = $json->author;
                    $authorlink = $json->_links->{'author'}[0]->href;
                    $authorjson = json_decode(trim(wp_remote_retrieve_body(wp_remote_get($authorlink, array( 'timeout' => 20 )))));
                    /*var_dump($json);*/

                    $footab_postsrss_array[] = array(
                        'title' => $json->title->rendered,
                        'mediaurl' => $mediajson->link ? $mediajson->source_url : '',
                        'mediatitle' => $mediajson->link ? 'thumbnail' : '',
                        'link' => $json->link,
                        'pubDate' => mysql2date('D, d M Y H:i:s +0000', $json->date, false),
                        'creator' => $authorjson->name,
                        'guid' => $json->guid->rendered,
                        'description' => strip_tags($json->excerpt->rendered),
                    );

                }


            }
            update_post_meta( get_the_ID(), $prefix . 'posts_list_cache', maybe_serialize($footab_postsrss_array) );
        } else {
            $footab_postsrss_array = maybe_unserialize($footab_postsrss_cache);
        }


        foreach ($footab_postsrss_array as $footab_postsrss_item) {
            ?>
            <item>
                <title><?php echo $footab_postsrss_item['title']; ?></title>
                <?php if ($footab_postsrss_item['mediaurl']) {
                    ?>
                    <media:content url="<?php echo $footab_postsrss_item['mediaurl']; ?>">
                    </media:content>
                <?php } ?>
                <link><?php echo $footab_postsrss_item['link']; ?></link>
                <pubDate><?php echo $footab_postsrss_item['pubDate']; ?></pubDate>
                <dc:creator><?php echo $footab_postsrss_item['creator']; ?></dc:creator>
                <guid isPermaLink="false"><?php echo $footab_postsrss_item['guid']; ?></guid>
                <description><![CDATA[<?php echo $footab_postsrss_item['description']; ?>]]></description>
                <content:encoded><![CDATA[<?php echo $footab_postsrss_item['description']; ?>]]></content:encoded>
                <?php rss_enclosure(); ?>
                <?php do_action('rss2_item'); ?>
            </item>
            <?php
        }


        ?>
        <?php /*while (have_posts()) : the_post(); ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <?php if (has_post_thumbnail()) {
                    $thumbnail_id = get_post_thumbnail_id();
                    $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                    ?>
                    <media:content url="<?php echo get_the_post_thumbnail_url(); ?>">
                        <media:title type="html">thumbnail</media:title>
                    </media:content>
                <?php } ?>
                <link><?php the_permalink_rss(); ?></link>
                <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <dc:creator><?php the_author(); ?></dc:creator>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
                <content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
                <?php rss_enclosure(); ?>
                <?php do_action('rss2_item'); ?>
            </item>
        <?php endwhile;*/ ?>
    </channel>
</rss>