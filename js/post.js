/*global $, jQuery, dotclear, jsToolBar, fullScreenApi */
'use strict';

(function () {
  var fullScreenApi = {
    supportsFullScreen: false,
    isFullScreen: () => false,
    requestFullScreen: () => {},
    cancelFullScreen: () => {},
    fullScreenEventName: '',
    prefix: '',
  };
  const browserPrefixes = 'webkit moz o ms khtml'.split(' ');

  // check for native support
  if (typeof document.cancelFullScreen != 'undefined') {
    fullScreenApi.supportsFullScreen = true;
  } else {
    // check for fullscreen support by vendor prefix
    for (let i = 0, il = browserPrefixes.length; i < il; i++) {
      fullScreenApi.prefix = browserPrefixes[i];

      if (typeof document[fullScreenApi.prefix + 'CancelFullScreen'] != 'undefined') {
        fullScreenApi.supportsFullScreen = true;
        break;
      }
    }
  }

  // update methods to do something useful
  if (fullScreenApi.supportsFullScreen) {
    fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';

    fullScreenApi.isFullScreen = function () {
      switch (this.prefix) {
        case '':
          return document.fullScreen;
        case 'webkit':
          return document.webkitIsFullScreen;
        default:
          return document[this.prefix + 'FullScreen'];
      }
    };
    fullScreenApi.requestFullScreen = function (el) {
      return this.prefix === '' ? el.requestFullScreen() : el[this.prefix + 'RequestFullScreen']();
    };
    fullScreenApi.cancelFullScreen = function () {
      return this.prefix === '' ? document.cancelFullScreen() : document[this.prefix + 'CancelFullScreen']();
    };
  }

  // jQuery plugin
  if (typeof jQuery != 'undefined') {
    jQuery.fn.requestFullScreen = function () {
      return this.each(function () {
        if (fullScreenApi.supportsFullScreen) {
          fullScreenApi.requestFullScreen(this);
        }
      });
    };
  }

  // export api
  window.fullScreenApi = fullScreenApi;
})();

// utilities functions
(function ($) {
  // Return all element not belonging to context
  $.fn.allBut = function (context) {
    const target = this;
    let otherList = $();
    let processList = $(context || 'body').children();

    while (processList.size() > 0) {
      const cElem = processList.first();
      processList = processList.slice(1);

      if (cElem.filter(target).size() != target.size()) {
        if (cElem.has(target).size() > 0) {
          processList = processList.add(cElem.children());
        } else {
          if (cElem.css('display') != 'none') {
            // Get only not hidden element
            otherList = otherList.add(cElem);
          }
        }
      }
    }
    return otherList;
  };
})(jQuery);

const inZen = function (container, entry, page, main, wrapper) {
  // Switch into zen mode

  if (dotclear.zenMode == 1) return;

  // Get current status of some DOM element
  dotclear.zenMode_body_fs = $('body').css('font-size');
  dotclear.zenMode_body_tc = $('body').css('color');
  dotclear.zenMode_body_bc = $('body').css('background-color');
  dotclear.zenMode_page_bc = page.css('background-color');
  dotclear.zenMode_main_bc = main.css('background-color');
  dotclear.zenMode_main_bi = main.css('background-image');
  dotclear.zenMode_container_mt = container.css('margin-top');
  dotclear.zenMode_container_ml = container.css('margin-left');
  dotclear.zenMode_container_mr = container.css('margin-right');
  dotclear.zenMode_hide_mm = wrapper.hasClass('hide-mm');
  dotclear.zenMode_wrapper_bc = wrapper.css('background-color');
  dotclear.zenMode_wrapper_bi = wrapper.css('background-image');

  dotclear.zenMode_Color = 'rgb(101,101,101)';

  // Set textured background if set
  if (dotclear.zenMode_Background !== '') {
    $('body').css('background-image', `url(index.php?pf=zenEdit/img/background/${dotclear.zenMode_Background})`);
    if (dotclear.zenMode_Background.substr(0, 5) == 'dark/') {
      // Dark background
      dotclear.zenMode_Color = 'rgb(241,241,241)';
    }
  }

  // Hack some CSS attributes
  container.css('margin-top', '3em');
  if (dotclear.zenMode_hide_mm) {
    //    container.css('margin-left','14.5em');
    //    container.css('margin-right','14.5em');
  } else {
    if (dotclear.zenMode_SmallMargins == '1') {
      container.css('margin-left', entry.css('margin-right'));
      container.css('margin-right', '4em');
    }
  }
  $('body').css('font-size', '13px').css('color', dotclear.zenMode_Color).css('background-color', 'rgb(248,248,248)');
  wrapper.css('background-color', 'transparent').css('background-image', 'none');
  page.css('background-color', 'transparent');
  main.css('background-color', 'transparent').css('background-image', 'none');

  // Hide everything not mandatory
  dotclear.zenMode_other = container.allBut();
  dotclear.zenMode_other.hide();

  // Change toolbar button title and icon
  jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditHide;
  jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-off.png';
  //  Don't know how to refresh this button !?! So, jQuery will help me
  $('button.jstb_zenEdit')
    .attr('title', dotclear.msg.zenEditHide)
    .css('background-image', 'url(index.php?pf=zenEdit/img/zen-off.png)');

  dotclear.zenMode = 1;

  if (dotclear.zenMode_FullScreen == '1') {
    if (fullScreenApi.supportsFullScreen) {
      fullScreenApi.requestFullScreen(document.documentElement);
    }
  }
};

const outZen = function (container, entry, page, main, wrapper) {
  // Exit from zen mode

  if (dotclear.zenMode == 0) return;

  // Reset textured background if set
  if (dotclear.zenMode_Background !== '') {
    $('body').css('background-image', 'none');
  }

  // Restore some CSS attributes as before
  container.css('margin-top', dotclear.zenMode_container_mt);
  container.css('margin-left', dotclear.zenMode_container_ml);
  container.css('margin-right', dotclear.zenMode_container_mr);
  $('body')
    .css('font-size', dotclear.zenMode_body_fs)
    .css('color', dotclear.zenMode_body_tc)
    .css('background-color', dotclear.zenMode_body_bc);
  wrapper.css('background-color', dotclear.zenMode_wrapper_bc).css('background-image', dotclear.zenMode_wrapper_bi);
  page.css('background-color', dotclear.zenMode_page_bc);
  main.css('background-color', dotclear.zenMode_main_bc).css('background-image', dotclear.zenMode_main_bi);

  // Show everything having been hidden before
  dotclear.zenMode_other.show();

  // Restore toolbar button title
  jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditShow;
  jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-on.png';
  //  Don't know how to refresh this button !?! So, jQuery will help me
  $('button.jstb_zenEdit')
    .attr('title', dotclear.msg.zenEditShow)
    .css('background-image', 'url(index.php?pf=zenEdit/img/zen-on.png)');

  dotclear.zenMode = 0;

  if (dotclear.zenMode_FullScreen == '1') {
    if (fullScreenApi.supportsFullScreen && fullScreenApi.isFullScreen) {
      fullScreenApi.cancelFullScreen(document.documentElement);
    }
  }
};

const switchZen = function () {
  const wrapper = $('#wrapper');
  const main = $('#main');
  const page = $('#content');
  const entry = $('#entry-wrapper');
  const container = $('div#entry-content');
  if (dotclear.zenMode == 0) {
    inZen(container, entry, page, main, wrapper);
  } else {
    outZen(container, entry, page, main, wrapper);
  }
};

// Toolbar button for series

jsToolBar.prototype.elements.zenEditSpace = {
  type: 'space',
  format: {
    wysiwyg: true,
    wiki: true,
    xhtml: true,
    markdown: true,
  },
};

jsToolBar.prototype.elements.zenEdit = {
  type: 'button',
  title: 'Zen',
  fn: {},
};
jsToolBar.prototype.elements.zenEdit.context = 'post';
jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-on.png';
jsToolBar.prototype.elements.zenEdit.fn.wiki = () => switchZen();
jsToolBar.prototype.elements.zenEdit.fn.xhtml = () => switchZen();
jsToolBar.prototype.elements.zenEdit.fn.wysiwyg = () => switchZen();
jsToolBar.prototype.elements.zenEdit.fn.markdown = () => switchZen();

$(document).ready(function () {
  dotclear.mergeDeep(dotclear, dotclear.getData('zenedit'));

  if (dotclear.zenMode == 0) {
    jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditShow;
    jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-on.png';
  } else {
    jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditHide;
    jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-off.png';
  }
});
