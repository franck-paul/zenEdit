/*global dotclear */
'use strict';

dotclear.ready(() => {
  const { base_url } = dotclear.getData('zenedit_prefs');
  const background = document.getElementById('zenedit_background');

  if (background) {
    background.addEventListener('change', (event) => {
      // Change background image sample
      document.getElementById('zenedit_sample').style.backgroundImage = `url(${base_url}${event.target.value})`;
    });
  }
});
