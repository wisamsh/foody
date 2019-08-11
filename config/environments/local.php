<?php
/** Development */
define('SAVEQUERIES', true);
define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);
// Disable display of errors and warnings
define('WP_DEBUG_DISPLAY', true);
define('FS_METHOD', 'direct');
define('FACEBOOK_APP_ID', '300250737432710');
define('FACEBOOK_API_VERSION', 'v3.1');
define('GOOGLE_APP_ID', '589726890032-lplp2v2oa8prujvo03jda02b7cramucc.apps.googleusercontent.com');
define('GOOGLE_CAPTCHA_SECRET', '6Lc7eXIUAAAAAKG_OzJR3AblE0ADojSuiOD10hq5');
define('MIXPANEL_TOKEN','4f106af7ed1ff172ef5bf5a29d7af3af');
define('GOOGLE_TAG_MANAGER_ID','GTM-M2SBXJF');

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', str_replace('http://','',env('WP_HOME')));
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define( 'WP_DEFAULT_THEME', 'foody');
//define( 'W3TC_CONFIG_DIR', '/var/tmp/w3tc/w3tc-config' );
define('EWWW_IMAGE_OPTIMIZER_TOOL_PATH','/var/tmp/ewww/');


define('JWT_AUTH_SECRET_KEY', env('JWT_SECRET')); // Replace 'your-top-secret-key' with an actual secret key.
define('JWT_AUTH_CORS_ENABLE', true);