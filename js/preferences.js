/*global $ */
'use strict';

const updateSampleBackground = function() {
  // Change background image of #zenedit_sample
  const background = $('#zenedit_background').val();
  $('#zenedit_sample').css('background-image', `url(index.php?pf=zenEdit/img/background/${background})`);
};

$(function() {
  $('#zenedit_background').change(updateSampleBackground).keyup(updateSampleBackground);
});
