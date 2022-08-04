/*global $ */
'use strict';

const updateSampleBackground = () => {
  // Change background image of #zenedit_sample
  const background = $('#zenedit_background').val();
  $('#zenedit_sample').css('background-image', `url(index.php?pf=zenEdit/img/background/${background})`);
};

$(() => {
  $('#zenedit_background').on('change', updateSampleBackground).on('keyup', updateSampleBackground);
});
