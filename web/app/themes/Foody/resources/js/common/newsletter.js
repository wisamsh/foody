/**
 * Created by moveosoftware on 13/8/19.
 */

jQuery(document).ready(($) => {

    $( '.wpcf7' ).on( 'wpcf7invalid', function( event ) {
       let messageSelector = $('.wpcf7-not-valid-tip');
        messageSelector.remove();
        messageSelector.insertBefore($('#wpcf7-f3101-o2 .bmd-form-group'));

    });

});