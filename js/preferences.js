/*global $ */
'use strict';

$(() => {
  const { base_url } = dotclear.getData('zenedit_prefs');
  const updateSampleBackground = () => {
    // Change background image of #zenedit_sample
    const background = $('#zenedit_background').val();
    $('#zenedit_sample').css('background-image', `url(${base_url}${background})`);
  };

  $('#zenedit_background').on('change', updateSampleBackground).on('keyup', updateSampleBackground);
});
