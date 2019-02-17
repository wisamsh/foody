/**
 * Created by moveosoftware on 12/19/18.
 */


jQuery(document).ready(($) => {

    if (foodyGlobals.isMobile == false) {
        let $whatsappShareLink = $('.essb_link_whatsapp a');

        if ($whatsappShareLink.length) {
            $whatsappShareLink.attr('target', '_blank');
            let original = $whatsappShareLink.attr('href');

            original = original.replace('whatsapp://', 'https://web.whatsapp.com/');
            $whatsappShareLink.attr('href', original);
        }
    }

});