<?php 
function add_canonical_to_archives() {
    $canonical_url = '';

    if (is_tag()) {
        // Canonical for tag archive
        $canonical_url = get_term_link(get_queried_object());
    } elseif (is_category()) {
        // Canonical for category archive
        $canonical_url = get_term_link(get_queried_object());
    } elseif (is_author()) {
        // Canonical for author archive
        $canonical_url = get_author_posts_url(get_queried_object_id());
    }

    // Output the canonical tag if the canonical URL is valid and not a WP error
    if (!is_wp_error($canonical_url) && !empty($canonical_url)) {
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    }
}
add_action('wp_head', 'add_canonical_to_archives');

?>