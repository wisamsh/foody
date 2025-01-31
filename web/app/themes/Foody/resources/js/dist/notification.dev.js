"use strict";

jQuery(document).ready(function ($) {
  // AJAX call when entering admin area
  jQuery.ajax({
    url: adminAjax.ajax_url,
    type: 'POST',
    data: {
      action: 'admin_enter'
    },
    success: function success(response) {
      console.log('Success:', response);
    },
    error: function error(xhr, status, _error) {
      console.log('Error:', _error);
    }
  });
});