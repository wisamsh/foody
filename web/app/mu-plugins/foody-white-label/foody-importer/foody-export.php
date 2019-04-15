<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/15/19
 * Time: 1:44 PM
 */
/** @noinspection PhpUnusedParameterInspection */

/**
 * Generates the WXR export file for download.
 *
 * Default behavior is to export all content, however, note that post content will only
 * be exported for post types with the `can_export` argument enabled. Any posts with the
 * 'auto-draft' status will be skipped.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @global WP_Post $post Global `$post`.
 *
 * @param array $args {
 *     Optional. Arguments for generating the WXR export file for download. Default empty array.
 *
 * @type string $content Type of content to export. If set, only the post content of this post type
 *                                  will be exported. Accepts 'all', 'post', 'page', 'attachment', or a defined
 *                                  custom post. If an invalid custom post type is supplied, every post type for
 *                                  which `can_export` is enabled will be exported instead. If a valid custom post
 *                                  type is supplied but `can_export` is disabled, then 'posts' will be exported
 *                                  instead. When 'all' is supplied, only post types with `can_export` enabled will
 *                                  be exported. Default 'all'.
 * @type string $author Author to export content for. Only used when `$content` is 'post', 'page', or
 *                                  'attachment'. Accepts false (all) or a specific author ID. Default false (all).
 * @type string $category Category (slug) to export content for. Used only when `$content` is 'post'. If
 *                                  set, only post content assigned to `$category` will be exported. Accepts false
 *                                  or a specific category slug. Default is false (all categories).
 * @type string $start_date Start date to export content from. Expected date format is 'Y-m-d'. Used only
 *                                  when `$content` is 'post', 'page' or 'attachment'. Default false (since the
 *                                  beginning of time).
 * @type string $end_date End date to export content to. Expected date format is 'Y-m-d'. Used only when
 *                                  `$content` is 'post', 'page' or 'attachment'. Default false (latest publish date).
 * @type string $status Post status to export posts for. Used only when `$content` is 'post' or 'page'.
 *                                  Accepts false (all statuses except 'auto-draft'), or a specific status, i.e.
 *                                  'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', or
 *                                  'trash'. Default false (all statuses except 'auto-draft').
 * }
 */
function export_import_foody_wp($args = array())
{
    $sitename = sanitize_key(get_bloginfo('name'));

    if (!empty($sitename)) {
        $sitename .= '.';
    }

    $date = date('Y-m-d');
    $wp_filename = $sitename . 'wordpress.' . $date . '.xml';
    /**
     * Filters the export filename.
     *
     * @since 4.4.0
     *
     * @param string $wp_filename The name of the file for download.
     * @param string $sitename The site name.
     * @param string $date Today's date, formatted.
     */
    $filename = apply_filters('export_foody_filename', $wp_filename, $sitename, $date);

    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);

    /*
     * Get the requested terms ready
     */
    $terms = array();


    $custom_taxonomies = get_taxonomies(array('_builtin' => false));
    $custom_terms = (array)get_terms($custom_taxonomies, array('get' => 'all'));

    // Put terms in order with no child going before its parent.
    while ($t = array_shift($custom_terms)) {
        if ($t->parent == 0 || isset($terms[$t->parent]))
            $terms[$t->term_id] = $t;
        else
            $custom_terms[] = $t;
    }

    unset($custom_taxonomies, $custom_terms);


    /**
     * Wrap given string in XML CDATA tag.
     *
     * @since 2.1.0
     *
     * @param string $str String to wrap in XML CDATA tag.
     * @return string
     */
    function foody_cdata($str)
    {
        if (!seems_utf8($str)) {
            $str = utf8_encode($str);
        }
        // $str = ent2ncr(esc_html($str));
        $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';

        return $str;
    }

    /**
     * Return the URL of the site
     *
     * @since 2.5.0
     *
     * @return string Site URL.
     */
    function foody_site_url()
    {
        // Multisite: the base URL.
        if (is_multisite())
            return network_home_url();
        // WordPress (single site): the blog URL.
        else
            return get_bloginfo_rss('url');
    }

    /**
     * Output a term_name XML tag from a given term object
     *
     * @since 2.9.0
     *
     * @param object $term Term Object
     */
    function foody_term_name($term)
    {
        if (empty($term->name))
            return;

        echo '<wp:term_name>' . foody_cdata($term->name) . "</wp:term_name>\n";
    }

    /**
     * Output a term_description XML tag from a given term object
     *
     * @since 2.9.0
     *
     * @param object $term Term Object
     */
    function foody_term_description($term)
    {
        if (empty($term->description))
            return;

        echo "\t\t<wp:term_description>" . foody_cdata($term->description) . "</wp:term_description>\n";
    }

    /**
     * Output term meta XML tags for a given term object.
     *
     * @since 4.6.0
     *
     * @param WP_Term $term Term object.
     */
    function foody_term_meta($term)
    {
        global $wpdb;

        $termmeta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id));

        foreach ($termmeta as $meta) {
            /**
             * Filters whether to selectively skip term meta used for WXR exports.
             *
             * Returning a truthy value to the filter will skip the current meta
             * object from being exported.
             *
             * @since 4.6.0
             *
             * @param bool $skip Whether to skip the current piece of term meta. Default false.
             * @param string $meta_key Current meta key.
             * @param object $meta Current meta object.
             */
            if (!apply_filters('foody_export_skip_termmeta', false, $meta->meta_key, $meta)) {
                printf("\t\t<wp:termmeta>\n\t\t\t<wp:meta_key>%s</wp:meta_key>\n\t\t\t<wp:meta_value>%s</wp:meta_value>\n\t\t</wp:termmeta>\n", foody_cdata($meta->meta_key), foody_cdata($meta->meta_value));
            }
        }
    }

    echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";

    ?>
    <!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your site. -->
    <!-- It contains information about your site's posts, pages, comments, categories, and other content. -->
    <!-- You may use this file to transfer that content from one site to another. -->
    <!-- This file is not intended to serve as a complete backup of your site. -->

    <!-- To import this information into a WordPress site follow these steps: -->
    <!-- 1. Log in to that site as an administrator. -->
    <!-- 2. Go to Tools: Import in the WordPress admin panel. -->
    <!-- 3. Install the "WordPress" importer from the list. -->
    <!-- 4. Activate & Run Importer. -->
    <!-- 5. Upload this file using the form provided on that page. -->
    <!-- 6. You will first be asked to map the authors in this export file to users -->
    <!--    on the site. For each author, you may choose to map to an -->
    <!--    existing user on the site or to create a new user. -->
    <!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->
    <!--    contained in this file into your site. -->
    <?php ob_start(); ?>
    <?php the_generator('export'); ?>
    <rss version="2.0"
         xmlns:excerpt="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/excerpt/"
         xmlns:content="http://purl.org/rss/1.0/modules/content/"
         xmlns:wfw="http://wellformedweb.org/CommentAPI/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:wp="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/"
    >

        <channel>
            <title><?php bloginfo_rss('name'); ?></title>
            <link><?php bloginfo_rss('url'); ?></link>
            <description><?php bloginfo_rss('description'); ?></description>
            <pubDate><?php echo date('D, d M Y H:i:s +0000'); ?></pubDate>
            <language><?php bloginfo_rss('language'); ?></language>
            <wp:wxr_version><?php echo WXR_VERSION; ?></wp:wxr_version>
            <wp:base_site_url><?php echo foody_site_url(); ?></wp:base_site_url>
            <wp:base_blog_url><?php bloginfo_rss('url'); ?></wp:base_blog_url>
            <!--    Custom Taxonomies    -->
            <?php foreach ($terms as $t) : ?>
                <wp:term>
                    <wp:term_id><?php echo foody_cdata($t->term_id); ?></wp:term_id>
                    <wp:term_taxonomy><?php echo foody_cdata($t->taxonomy); ?></wp:term_taxonomy>
                    <wp:term_slug><?php echo foody_cdata($t->slug); ?></wp:term_slug>
                    <wp:term_parent><?php echo foody_cdata($t->parent ? $terms[$t->parent]->slug : ''); ?></wp:term_parent>
                    <?php foody_term_name($t);
                    foody_term_description($t);
                    foody_term_meta($t); ?>
                </wp:term>
            <?php endforeach; ?>
        </channel>
    </rss>
    <?php

    ob_get_clean();
}
