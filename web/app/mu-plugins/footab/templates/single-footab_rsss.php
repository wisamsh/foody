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
        $feeds_list = get_post_meta(get_the_ID(), $prefix . 'feeds_list', true);
        foreach ((array)$feeds_list as $key => $entry) {
            $feed_title = "";
            $feed_link = "";
            $items_count = 20;

            if (isset($entry[$prefix . 'feed_title'])) {
                $feed_title = esc_html($entry[$prefix . 'feed_title']);
            }

            if (isset($entry[$prefix . 'feed_link'])) {
                $feed_link = esc_html($entry[$prefix . 'feed_link']);
            }

            if (isset($entry[$prefix . 'items_count'])) {
                $items_count = esc_html($entry[$prefix . 'items_count']);
            }

            if (isset($feed_link) && $feed_link) {
                $response = wp_remote_get($feed_link);
                $body = wp_remote_retrieve_body($response);
                $xml = simplexml_load_string(trim($body));
                $namespaces = $xml->getNamespaces(true); // get namespaces
                $feed_items = array();

                foreach ($xml->channel->item as $item) {
                    $tmp = new stdClass();
                    $tmp->title = trim((string) $item->title);
                    $tmp->link  = trim((string) $item->link);
                    $tmp->pubDate  = trim((string) $item->pubDate);
                    $tmp->guid  = trim((string) $item->guid);
                    $tmp->description  = trim((string) $item->description);

                    $tmp->media_url =    trim((string) $item->children($namespaces['media'])->content->attributes()->url);
                    $tmp->media_title = trim((string) $item->children($namespaces['media'])->content->children($namespaces['media'])->title);
                    $tmp->content_encoded = trim((string) $item->children($namespaces['content'])->encoded);
                    $tmp->dc_creator = trim((string) $item->children($namespaces['dc'])->creator);
                    // etc

                    // add parsed data to the array
                    $feed_items[] = $tmp;
                }

                $count=0;
                foreach($feed_items as $feed_item) {
                    $count++;
                    if ($count > $items_count) {
                        break;
                    }
                    ?>
                    <item>
                        <title><?php echo $feed_item->title; ?></title>
                        <?php if ($feed_item->media_url) {
                            ?>
                            <media:content url="<?php echo $feed_item->media_url; ?>">
                                <media:title type="html"><?php echo $feed_item->media_title; ?></media:title>
                            </media:content>
                        <?php } ?>
                        <link><?php echo $feed_item->link; ?></link>
                        <pubDate><?php echo $feed_item->pubDate; ?></pubDate>
                        <dc:creator><?php echo $feed_item->dc_creator; ?></dc:creator>
                        <guid isPermaLink="false"><?php echo $feed_item->guid; ?></guid>
                        <description><?php echo $feed_item->description; ?></description>
                        <content:encoded><?php echo $feed_item->content_encoded; ?></content:encoded>
                        <?php rss_enclosure(); ?>
                        <?php do_action('rss2_item'); ?>
                    </item>
                    <?php
                }

            }


            // Do something with the data
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