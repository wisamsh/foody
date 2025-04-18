"use strict";

jQuery(".close_banner").on('click', function (e) {
  IDToClose = jQuery(this).attr('data-close');
  jQuery("#" + IDToClose).toggle();
});