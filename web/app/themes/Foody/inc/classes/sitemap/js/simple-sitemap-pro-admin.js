jQuery(document).ready(function($) {

    // Show correct tab on page load
    var currentSitemapTab = localStorage.getItem('currentSitemapTab');
    if( !currentSitemapTab ) { currentSitemapTab = 'simple-sitemap-pro'; }
    $('.' + currentSitemapTab + '-tab').show(); // show the tab content for the tab on page load

    // Sync the tab header with content
    // Note: the delay is necessary, otherwise if you just add then remove the classes a slight flicker is visible
    $('h2.nav-tab-wrapper a[href=' + currentSitemapTab + ']').addClass('nav-tab-active').delay(40).queue(function(next){
    //$('.' + currentSitemapTab).addClass('nav-tab-active').delay(40).queue(function(next){
      $('.nav-tab').not(this).removeClass('nav-tab-active'); // remove from other tab headings
      next();
    });

    // Show/hide tab content when tab headings clicked
    $('.nav-tab').on( 'click', function(event) {

        // note: this delay is necessary, otherwise if you just add then remove the classes a slight flicker is visible
        $(this).addClass('nav-tab-active').delay(60).queue(function(next){
            $('.nav-tab').not(this).removeClass('nav-tab-active'); // remove from other tab headings
            next();
        });

        $('.tab-content').hide(); // hide all tab content

        var tab_clicked = $(this).attr('href');

        $('.' + tab_clicked + '-tab').show(); // show the tab content for the tab header just clicked

        // store tab clicked
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem("currentSitemapTab", tab_clicked); // store
        }

        event.preventDefault(); // prevent tab link from reloading the page
    });

    // Reset plugin settings link
    $('#simple-sitemap-pro-reset > a').on( 'click', function() {
        var res = confirm('Are you sure? All plugin options (apart from the license key) will be reset to their default settings!');
        if( res === true ) {
            $('#simple-sitemap-pro-reset-form').submit();
        }
    });
});