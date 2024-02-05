<?php
class Foody_wsitemap
{

    private function get_posts_map_private($type, $xml = false)
    {

        global $wpdb;
        $custom_post_type = $type;

        // A sql query to return all post titles
        $results = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title , post_date, post_name FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish' order by ID desc ", $custom_post_type), ARRAY_A);

        // Return null if we found no results
        if (!$results)
            return;
        if ($xml == false) {
            // HTML for our select printing post titles as loop
            $output = '<ul style="list-style:square;">';

            foreach ($results as $index => $post) {
                $postID = $post['ID'];
                $url = get_permalink($postID);
                $output .= '<li  value="' . $post['ID'] . '"><a href="' . $url . '">' . $post['post_title'] . '</a></li>';
            }

            $output .= '</ul>'; // end of select element

            // get the html
            return $output;
        }

        if ($xml == true) {
            // HTML for our select printing post titles as loop
            $output = '';

            foreach ($results as $index => $post) {
                $postID = $post['ID'];
                $post_date = $post['post_date'];
                $url = get_permalink($postID);
                $output .= '<url>';
                $output .= '<loc>'.htmlspecialchars($url);
                $output .= '</loc>';
                $output .= '<lastmod>'. $post_date ;
                $output .= '</lastmod>';
                $output .= '</url>';
                
            }

           

            // get the html
            return $output;

        }
    }

    public function get_posts_map($type, $xml=false)
    {
        return $this->get_posts_map_private($type, $xml);
    }

    public function Do_FoodyBeadcrumbs()
    {
        echo '<section class="accessory-details-container">';
        bootstrap_breadcrumb();
        echo '</section>';
    }

    public function get_the_tags()
    {
        $AllTags = get_tags();
        echo '<ul>';
        foreach ($AllTags as $k => $t) {
            echo '<li><a href="/tag/' . $t->slug . '">' . $t->name . '</a></li>';
        }
        echo '</ul>';
    }

    public function MobileattrMap()
    {
        if (wp_is_mobile()) {
            echo '<style> 
				#masthead{display:none;}
				#content {
				padding-top: 0px; 
				}
                .uniq{
                    width: 100%;
    text-align: center;
    font-size: 45px;
    font-weight: bold;
    margin-top: -10px;
                }
				</style>';
        }
    }

    public function get_the_sitemap_categories()
    {
        $allcats = get_categories();
        echo '<ul>';
        foreach ($allcats as $k => $t) {
            echo '<li><a href="/category/' . $t->slug . '">' . $t->name . '</a></li>';
        }
        echo '</ul>';
    }
} //END CLASS
