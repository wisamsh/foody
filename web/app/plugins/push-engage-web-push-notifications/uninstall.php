<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit ();
}

delete_post_meta_by_key( '_pe_override' );
delete_post_meta_by_key( '_pushengage_custom_text' );
delete_post_meta_by_key( '_sedule_notification' );
delete_post_meta_by_key( 'pe_override_scheduled' );
delete_post_meta_by_key( '_pe_draft_segments' );

if ( is_multisite() ) {
    delete_site_option( 'pushengage_settings' );
}
else {
    delete_option( 'pushengage_settings' );
}
