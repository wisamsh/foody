<?php
/** Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
/** Disable all file modifications including updates and update notifications */
define('DISALLOW_FILE_MODS', true);
define('GOOGLE_TAG_MANAGER_ID', 'GTM-KQK843H');

define('FACEBOOK_APP_ID', '242005383411645');
define('FACEBOOK_API_VERSION', 'v3.1');
// TODO change to production details
define('GOOGLE_APP_ID', '589726890032-lplp2v2oa8prujvo03jda02b7cramucc.apps.googleusercontent.com');
define('GOOGLE_CAPTCHA_SECRET', '6Lc7eXIUAAAAAKG_OzJR3AblE0ADojSuiOD10hq5');
define('MIXPANEL_TOKEN', 'dfc57229dac7e9dbf81bc0d03c00c84b');

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', preg_replace('/http(s?):\/\//','',env('WP_HOME')));
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define( 'WP_DEFAULT_THEME', 'Foody');
define('EWWW_IMAGE_OPTIMIZER_TOOL_PATH','/home/ubuntu/foody-shared-efs/plugins/ewww/');

define('JWT_AUTH_SECRET_KEY', env('JWT_SECRET')); // Replace 'your-top-secret-key' with an actual secret key.
define('JWT_AUTH_CORS_ENABLE', true);