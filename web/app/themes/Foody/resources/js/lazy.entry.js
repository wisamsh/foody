window.lazySizesConfig = window.lazySizesConfig || {};


// use data-original instead of data-src
lazySizesConfig.srcAttr = 'data-foody-src';
lazySizesConfig.preloadAfterLoad = true;

//page is optimized for fast onload event
// lazySizesConfig.loadMode = 1;

require('lazysizes');
// import a plugin
// import 'lazysizes/parent-fit/ls.parent-fit';