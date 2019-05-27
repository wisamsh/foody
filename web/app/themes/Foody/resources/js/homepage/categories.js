/**
 * Created by moveosoftware on 5/20/18.
 */

jQuery(document).ready(($) => {
    showMoreList('.categories-listing:not(.block-more):not(.showing-more) a:last-child', $('.categories-list-widget .title a').attr('href'));
});