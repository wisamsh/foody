<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/4/18
 * Time: 4:32 PM
 */

add_action( 'pre_get_comments', function(\WP_Comment_Query $query) {
    /* only allow 'my_custom_comment_type' when is required explicitly */
    if ( $query->query_vars['type'] !== 'how_i_did' ) {
        $query->query_vars['type__not_in'] = array_merge(
            (array) $query->query_vars['type__not_in'],
            array('how_i_did')
        );
    }
});