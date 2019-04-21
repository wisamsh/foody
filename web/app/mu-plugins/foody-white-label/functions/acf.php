<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/14/19
 * Time: 5:37 PM
 */

function my_acf_load_post_object_value($value, $post_id, $field)
{
    return $value;
}


add_filter('acf/load_value/type=post_object', 'my_acf_load_post_object_value', 10, 3);


if (!is_main_site()) {
    function my_acf_load_taxonomy_value($value, $post_id, $field)
    {
        if (!empty($value)) {

            $tax = $field['taxonomy'];

            if (!is_array($value)) {
                $value = [$value];
                $single = true;
            }

            $value = array_map(function ($val) {
                if ($val instanceof WP_Term) {
                    $val = $val->term_id;
                }
                return $val;
            }, $value);

            $global_taxonomies = [
                'units',
                'pans',
                'limitations',
            ];

            if (in_array($tax, $global_taxonomies)) {
                $current_terms = array_map(function ($term) use ($tax) {

                    // try to get the term from the current
                    // blog's database
                    $current_term = get_term($term, $tax);

                    // if we receive error or empty value
                    // try to find the value by the source id
                    // in the main site's database
                    if (is_wp_error($current_term) || empty($current_term)) {
                        $args = array(
                            'hide_empty' => false, // also retrieve terms which are not used yet
                            'meta_query' => array(
                                array(
                                    'key' => 'source_term',
                                    'value' => $term,
                                    'compare' => '='
                                )
                            ),
                            'taxonomy' => $tax
                        );

                        $current = get_current_blog_id();
                        switch_to_blog(get_main_site_id());
                        $terms = get_terms($args);
                        switch_to_blog($current);

                        if (!empty($terms)) {
                            $term = $terms[0];
                            if ($term instanceof WP_Term) {
                                $term = $terms[0]->term_id;
                            }
                        }
                    }

                    return $term;
                }, $value);
                if (!empty($current_terms)) {
                    $value = $current_terms;
                }
            }

            if (isset($single) && $single) {
                $value = $value[0];
            }
        }


        return $value;
    }


    add_filter('acf/load_value/type=taxonomy', 'my_acf_load_taxonomy_value', 10, 3);
}