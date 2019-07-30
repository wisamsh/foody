<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/15/19
 * Time: 1:44 PM
 */
error_reporting(E_ALL);
define('WXR_VERSION', '1.2');
/**
 * Generates the WXR export file for import.
 *
 * Default behavior is to export all content, however, note that post content will only
 * be exported for post types with the `can_export` argument enabled. Any posts with the
 * 'auto-draft' status will be skipped.
 *
 * @param $newBlogId int newly create blog to import content into
 *
 * @throws Exception
 *@since 2.1.0
 *
 *
 */
function export_import_foody_wp($newBlogId)
{
    global $wpdb, $foody_auto_synced_post_types;
    // Add more types if relevant
    $post_types = $foody_auto_synced_post_types;

    $esses = array_fill(0, count($post_types), '%s');
    $where = $wpdb->prepare("{$wpdb->posts}.post_type IN (" . implode(',', $esses) . ')', $post_types);
    $join = '';
    // Grab a snapshot of post IDs, just in case it changes during the export.
    /** @noinspection SqlResolve */
    $post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} $join WHERE $where");
    /*
     * Get the requested terms ready
     */
    $cats = $tags = $terms = array();
    $categories = (array)get_categories(array('get' => 'all'));
    $tags = (array)get_tags(array('get' => 'all'));
    $custom_taxonomies = get_taxonomies(array('_builtin' => false));
    $custom_terms = (array)get_terms($custom_taxonomies, array('get' => 'all'));

    // Put categories in order with no child going before its parent.
    while ($cat = array_shift($categories)) {
        if ($cat->parent == 0 || isset($cats[$cat->parent]))
            {$cats[$cat->term_id] = $cat;}
        else
            {$categories[] = $cat;}
    }

    // Put terms in order with no child going before its parent.
    while ($t = array_shift($custom_terms)) {
        if ($t->parent == 0 || isset($terms[$t->parent]))
            {$terms[$t->term_id] = $t;}
        else
            {$custom_terms[] = $t;}
    }

    unset($categories, $custom_taxonomies, $custom_terms);

    // Actual export starting
    $export_file = write_foody_wxr($newBlogId, $cats, $tags, $terms, $post_ids, $wpdb);


    /**
     * if data is not empty switch to
     * the new blog and import
     */
    if (!empty($export_file)) {
        try {

            // get site url for new blog
            switch_to_blog($newBlogId);
            $url = get_option('siteurl');
            restore_current_blog();

            /**
            * use wp cli to import the created export file.
            * @see Foody_WhiteLabelCmdExport::import()
            */
            $foody_import_cmd = "wp foody import $export_file --url=\"$url\" > /dev/null &";

            Foody_WhiteLabelLogger::info("starting wp foody import with command: $foody_import_cmd");
            $result = exec($foody_import_cmd);
            Foody_WhiteLabelLogger::info("wp foody import command finished",['result'=>$result]);

            // duplicate acf field groups to new blog
            foody_copy_acf($url);

        } catch (Exception $e) {
            Foody_WhiteLabelLogger::info("error exporting to $export_file", ['error' => $e]);
        }

    } else {
        Foody_WhiteLabelLogger::error('invalid data to import');
        throw new Exception('invalid data to import');
    }
}


/**
* Copies all acf field groups from the main site to the new blog
*
* @param $destination_blog_url string the new blog url
*/
function foody_copy_acf($destination_blog_url){

    // acf cli plugin activation for network
    // this needs to be done in order to use
    // the cli on the new blog
    $acf_activate_cmd = "wp plugin activate --network advanced-custom-fields-wpcli";

    // path to use for exported acf json
    $export_path = PLUGIN_DIR . 'acf-exports';

    // always use main site url for export.
    // this means no new custom fields can be added to the site
    $main_site_url = foody_get_main_site_url();

    // export command
    $acf_export_cmd = "wp acf export --export_path=$export_path --url=$main_site_url";

    /**
    * when using wp acf import with the --all option the cli
    * iterates over the directories listed in @see \ACFWPCLI\CLI::$paths
    * This filter overrides (priority 100000) the path provided by foody in order
    * to prevent importing from the wrong directory
    */
    add_filter('acfwpcli_fieldgroup_paths',function ($paths) use ($export_path){
        $paths['foody'] = $export_path;
    },100000,1);

    // import command uses the destination blog's url
    $acf_import_cmd = "wp acf import --all --url=$destination_blog_url";

    /*
     * Run start
     * */

    Foody_WhiteLabelLogger::info("starting acf copy to blog: $destination_blog_url");

    // activate plugin network wide.
    // doesn't matter if it's already activated
    $result = exec($acf_activate_cmd);
    Foody_WhiteLabelLogger::info("acf activation result",$result);

    // clear previous exports
    clear_directory($export_path);
    // export to export directory
    exec($acf_export_cmd);
    // import to destination blog.
    exec($acf_import_cmd);

    Foody_WhiteLabelLogger::info("finished acf copy to blog: $destination_blog_url");
}


/**
 * Creates the wxr xml string for the provided post.
*
* @param $post WP_Post
*
* @return string wxr representation of $post
*/
function foody_get_export_post($post){

        global $wpdb;
        $is_sticky = is_sticky($post->ID) ? 1 : 0;
        $post_content =
        '<item>
        <title>'.apply_filters("the_title_rss", $post->post_title) .
        '</title>
        <link>'.apply_filters("the_permalink_rss",get_permalink()).'</link>
        <pubDate>'. mysql2date("D, d M Y H:i:s +0000", get_post_time("Y-m-d H:i:s", true), false).'</pubDate>
        <dc:creator>' .foody_cdata(get_the_author_meta("login")).'</dc:creator>
        <guid isPermaLink="false">'. get_the_guid().'</guid>
        <description></description>
        <content:encoded>
            '.
             foody_cdata(apply_filters("the_content_export", $post->post_content))
            .'
        </content:encoded>
        <excerpt:encoded>
            '.
             foody_cdata(apply_filters("the_excerpt_export", $post->post_excerpt))
            .'
        </excerpt:encoded>
        <wp:post_id>'. intval($post->ID).'</wp:post_id>
        <wp:post_date>'. foody_cdata($post->post_date).'</wp:post_date>
        <wp:post_date_gmt>'. foody_cdata($post->post_date_gmt).'</wp:post_date_gmt>
        <wp:comment_status>'. foody_cdata($post->comment_status).'</wp:comment_status>
        <wp:ping_status>'. foody_cdata($post->ping_status).'</wp:ping_status>
        <wp:post_name>'. foody_cdata($post->post_name).'</wp:post_name>
        <wp:status>'. foody_cdata($post->post_status).'</wp:status>
        <wp:post_parent>'. intval($post->post_parent).'</wp:post_parent>
        <wp:menu_order>'. intval($post->menu_order).'</wp:menu_order>
        <wp:post_type>'. foody_cdata($post->post_type).'</wp:post_type>
        <wp:post_password>'. foody_cdata($post->post_password).'</wp:post_password>
        <wp:is_sticky>'. intval($is_sticky).'</wp:is_sticky>
        '. foody_post_taxonomy($post,false);


        // get all post meta from db
        $post_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d", $post->ID));
        foreach ($post_meta as $meta) {
            /**
             * Filters whether to selectively skip post meta used for WXR exports.
             *
             * Returning a truthy value to the filter will skip the current meta
             * object from being exported.
             *
             * @param bool $skip Whether to skip the current post meta. Default false.
             * @param string $meta_key Current meta key.
             * @param object $meta Current meta object.
             *
             *@since 3.3.0
             *
             */
            if (apply_filters('foody_export_skip_postmeta', false, $meta->meta_key, $meta)){
                continue;
            }

            $post_content .= '<wp:postmeta>
            <wp:meta_key>'. foody_cdata($meta->meta_key).'</wp:meta_key>
            <wp:meta_value>'. foody_cdata($meta->meta_value).'</wp:meta_value>
            </wp:postmeta>';
        }

        $post_content .=  '</item>';

        return $post_content;
    }

/**
 * Wrap given string in XML CDATA tag.
 *
 * @param string $str String to wrap in XML CDATA tag.
 *
 * @return string
 *@since 2.1.0
 *
 */
function foody_cdata($str)
{
    if (!empty($str)){
        if (!seems_utf8($str)) {
            $str = utf8_encode($str);
        }
        // $str = ent2ncr(esc_html($str));

        $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';
    }
    return $str;
}

/**
 * Return the URL of the site
 *
 * @return string Site URL.
 *@since 2.5.0
 *
 */
function foody_site_url()
{
    // Multisite: the base URL.
    if (is_multisite())
        {return network_home_url();}
    // WordPress (single site): the blog URL.
    else
        {return get_bloginfo_rss('url');}
}

/**
 * Output a cat_name XML tag from a given category object
 *
 * @param object $category Category Object
 *
 *@since 2.1.0
 *
 */
function foody_cat_name($category)
{
    if (empty($category->name))
        {return;}

    echo '<wp:cat_name>' . foody_cdata($category->name) . "</wp:cat_name>\n";
}

/**
 * Output a category_description XML tag from a given category object
 *
 * @param object $category Category Object
 *
 *@since 2.1.0
 *
 */
function foody_category_description($category)
{
    if (empty($category->description))
        {return;}

    echo '<wp:category_description>' . foody_cdata($category->description) . "</wp:category_description>\n";
}

/**
 * Output a tag_name XML tag from a given tag object
 *
 * @param object $tag Tag Object
 *
 *@since 2.3.0
 *
 */
function foody_tag_name($tag)
{
    if (empty($tag->name))
        {return;}

    echo '<wp:tag_name>' . foody_cdata($tag->name) . "</wp:tag_name>\n";
}

/**
 * Output a tag_description XML tag from a given tag object
 *
 * @param object $tag Tag Object
 *
 *@since 2.3.0
 *
 */
function foody_tag_description($tag)
{
    if (empty($tag->description))
        {return;}

    echo '<wp:tag_description>' . foody_cdata($tag->description) . "</wp:tag_description>\n";
}

/**
* Output list of taxonomy terms, in XML tag format, associated with a post
*
* @param bool $echo
*
* @return string
*@since 2.3.0
*/
function foody_post_taxonomy($post,$echo = true)
{
    $content = '';
    $taxonomies = get_object_taxonomies($post->post_type);
    if (empty($taxonomies)){
        return '';
    }

    $terms = wp_get_object_terms($post->ID, $taxonomies);

    if (!is_wp_error($terms)) {


        foreach ((array)$terms as $term) {
            $content .= "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . foody_cdata($term->name) . "</category>\n";
        }

        if ($echo){
            echo $content;
        }
    }

    return $content;
}

/**
 * Output a term_name XML tag from a given term object
 *
 * @param object $term Term Object
 *
 *@since 2.9.0
 *
 */
function foody_term_name($term)
{
    if (empty($term) || empty($term->name))
        {return;}

    echo '<wp:term_name>' . foody_cdata($term->name) . "</wp:term_name>\n";
}

/**
 * Output a term_description XML tag from a given term object
 *
 * @param object $term Term Object
 *
 *@since 2.9.0
 *
 */
function foody_term_description($term)
{
    if (empty($term->description))
        {return;}

    echo "\t\t<wp:term_description>" . foody_cdata($term->description) . "</wp:term_description>\n";
}

/**
 * Output term meta XML tags for a given term object.
 *
 * @param WP_Term $term Term object.
 *
 *@since 4.6.0
 *
 */
function foody_term_meta($term)
{
    global $wpdb;

    $term_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id));

    $source_term = new stdClass();
    $source_term->meta_key = 'source_term';
    $source_term->meta_value = $term->term_id;
    $term_meta[] = $source_term;

    foreach ($term_meta as $meta) {
        /**
         * Filters whether to selectively skip term meta used for WXR exports.
         *
         * Returning a truthy value to the filter will skip the current meta
         * object from being exported.
         *
         * @param bool $skip Whether to skip the current piece of term meta. Default false.
         * @param string $meta_key Current meta key.
         * @param object $meta Current meta object.
         *
         *@since 4.6.0
         *
         */
        if (!apply_filters('foody_export_skip_termmeta', false, $meta->meta_key, $meta)) {
            $meta_key = foody_cdata($meta->meta_key);
            $meta_value = foody_cdata($meta->meta_value);
            echo "\t\t<wp:termmeta>\n\t\t\t<wp:meta_key>$meta_key</wp:meta_key>\n\t\t\t<wp:meta_value>$meta_value</wp:meta_value>\n\t\t</wp:termmeta>\n";
        }
    }
}

function get_wxr_head()
{
    echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
    ?>
    <?php the_generator('export'); ?>
    <!--suppress HtmlUnknownTag -->
    <rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/"
    >
    <?php
     return null;
}

function get_wxr_channel_head()
{
    echo '<channel>';
    ?>
    <title><?php bloginfo_rss('name'); ?></title>
    <!--suppress HtmlExtraClosingTag -->
    <link><?php bloginfo_rss('url'); ?></link>
    <description><?php bloginfo_rss('description'); ?></description>
    <pubDate><?php echo date('D, d M Y H:i:s +0000'); ?></pubDate>
    <language><?php bloginfo_rss('language'); ?></language>
    <wp:wxr_version><?php echo WXR_VERSION; ?></wp:wxr_version>
    <wp:base_site_url><?php echo foody_site_url(); ?></wp:base_site_url>
    <wp:base_blog_url><?php bloginfo_rss('url'); ?></wp:base_blog_url>
    <?php

    return null;
}

/**
 * @param $newBlogId
 * @param $cats
 * @param $tags
 * @param $terms
 * @param $post_ids
 * @param $wpdb wpdb
 *
 * @return string
 */
function write_foody_wxr($newBlogId, $cats, $tags, $terms, $post_ids, $wpdb)
{
    $date = str_replace(' ', '-', date('d.m.Y H:i:s'));
    $file_name = plugin_dir_path(__FILE__) . "/exports/foody-wl-export-blog-$newBlogId-{$date}.xml";

    ob_start();
    get_wxr_head();
    $head_content = ob_get_contents();
    ob_end_clean();

    file_put_contents($file_name,$head_content,FILE_APPEND);

    ob_start();
    get_wxr_channel_head();
    $channel_head_content = ob_get_contents();
    ob_end_clean();
    file_put_contents($file_name,$channel_head_content,FILE_APPEND);

    // categories
    ob_start();

    foreach ($cats as $c) {
        ?>
         <wp:category>
        <wp:term_id><?php echo intval($c->term_id); ?></wp:term_id>
        <wp:category_nicename><?php echo foody_cdata($c->slug); ?></wp:category_nicename>
        <wp:category_parent><?php echo foody_cdata($c->parent ? $cats[$c->parent]->slug : ''); ?></wp:category_parent>
        <?php foody_cat_name($c);
        foody_category_description($c);
        foody_term_meta($c); ?>
        </wp:category>
        <?php
    }
    unset($cats);

    $categories_content = ob_get_contents();
    ob_end_clean();
    file_put_contents($file_name,$categories_content,FILE_APPEND);

    // tags
    ob_start();

    foreach ($tags as $t) {
        ?>
        <wp:tag>
            <wp:term_id><?php echo intval($t->term_id); ?></wp:term_id>
            <wp:tag_slug><?php echo foody_cdata($t->slug); ?></wp:tag_slug>
            <?php foody_tag_name($t);
            foody_tag_description($t);
            foody_term_meta($t); ?>
        </wp:tag>
        <?php
    }
    unset($tags);

    $tags_content = ob_get_contents();
    ob_end_clean();
    file_put_contents($file_name,$tags_content,FILE_APPEND);

    // custom taxonomies
    ob_start();

    foreach ($terms as $t) {
        ?>
        <wp:term>
            <wp:term_id><?php echo foody_cdata($t->term_id); ?></wp:term_id>
            <wp:term_taxonomy><?php echo foody_cdata($t->taxonomy); ?></wp:term_taxonomy>
            <wp:term_slug><?php echo foody_cdata($t->slug); ?></wp:term_slug>
            <wp:term_parent><?php echo foody_cdata($t->parent ? $terms[$t->parent]->slug : ''); ?></wp:term_parent>
            <?php foody_term_name($t);
            foody_term_description($t);
            foody_term_meta($t); ?>
        </wp:term>
        <?php
    }

    $terms_content = ob_get_contents();
    ob_end_clean();
    file_put_contents($file_name,$terms_content,FILE_APPEND);

    // posts

    if ($post_ids) {

        /**
         * @global WP_Query $wp_query
         */
        global $wp_query;

        // Fake being in the loop.
        $wp_query->in_the_loop = true;

        // Fetch 20 posts at a time rather than loading the entire table into memory.
        while ($next_posts = array_splice($post_ids, 0, 20)) {
            $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
            $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");
            $posts_content = array_map('foody_get_export_post',$posts);
            $posts_content = implode("\n",$posts_content);
            file_put_contents($file_name,$posts_content,FILE_APPEND);
            unset($posts_content);
        }

        $wp_query->in_the_loop = false;
        unset($post_ids);
    }

    // footer
    $footer = '</channel></rss>';
    file_put_contents($file_name,$footer,FILE_APPEND);


    return $file_name;
}