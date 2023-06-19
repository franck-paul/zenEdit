/*global $, jQuery, dotclear, jsToolBar, fullScreenApi */
'use strict';

(() => {
  const fullScreenApi = {
    supportsFullScreen: false,
    isFullScreen: () => false,
    requestFullScreen: () => {},
    cancelFullScreen: () => {},
    fullScreenEventName: '',
    prefix: '',
  };
  const browserPrefixes = 'webkit moz o ms khtml'.split(' ');

  // check for native support
  if (typeof document.cancelFullScreen === 'undefined') {
    // check for fullscreen support by vendor prefix
    for (let i = 0, il = browserPrefixes.length; i < il; i++) {
      fullScreenApi.prefix = browserPrefixes[i];

      if (typeof document[`${fullScreenApi.prefix}CancelFullScreen`] != 'undefined') {
        fullScreenApi.supportsFullScreen = true;
        break;
      }
    }
  } else {
    fullScreenApi.supportsFullScreen = true;
  }

  // update methods to do something useful
  if (fullScreenApi.supportsFullScreen) {
    fullScreenApi.fullScreenEventName = `${fullScreenApi.prefix}fullscreenchange`;

    fullScreenApi.isFullScreen = function () {
      switch (this.prefix) {
        case '':
          return document.fullScreen;
        case 'webkit':
          return document.webkitIsFullScreen;
        default:
          return document[`${this.prefix}FullScreen`];
      }
    };
    fullScreenApi.requestFullScreen = function (el) {
      return this.prefix === '' ? el.requestFullScreen() : el[`${this.prefix}RequestFullScreen`]();
    };
    fullScreenApi.cancelFullScreen = function () {
      return this.prefix === '' ? document.cancelFullScreen() : document[`${this.prefix}CancelFullScreen`]();
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
(() => {
  // Return all element not belonging to context
  $.fn.allBut = function (context) {
    let otherList = $();
    let processList = $(context || 'body').children();

    while (processList.length > 0) {
      const cElem = processList.first();
      processList = processList.slice(1);

      if (cElem.filter(this).length != this.length) {
        if (cElem.has(this).length > 0) {
          processList = processList.add(cElem.children());
        } else if (cElem.css('display') != 'none') {
          // Get only not hidden element
          otherList = otherList.add(cElem);
        }
      }
    }
    return otherList;
  };
})();

// Toolbar button for series

$(() => {
  dotclear.mergeDeep(dotclear, dotclear.getData('zenedit'));

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
  jsToolBar.prototype.elements.zenEdit.icon = dotclear.zenEdit.icon; // 'index.php?pf=zenEdit/icon.svg';
  jsToolBar.prototype.elements.zenEdit.fn.wiki = () => dotclear.zenEdit.switch();
  jsToolBar.prototype.elements.zenEdit.fn.xhtml = () => dotclear.zenEdit.switch();
  jsToolBar.prototype.elements.zenEdit.fn.wysiwyg = () => dotclear.zenEdit.switch();
  jsToolBar.prototype.elements.zenEdit.fn.markdown = () => dotclear.zenEdit.switch();

  dotclear.zenEdit.switch = () => {
    const wrapper = $('#wrapper');
    const main = $('#main');
    const page = $('#content');
    const entry = $('#entry-wrapper');
    const container = $('div#entry-content');

    if (dotclear.zenEdit.mode == 0) {
      // Switch into zen mode

      dotclear.zenEdit.prop = {
        // Get current status of some DOM element
        body_tc: $('body').css('color'),
        body_bc: $('body').css('background-color'),
        page_bc: page.css('background-color'),
        main_bc: main.css('background-color'),
        main_bi: main.css('background-image'),
        container_mt: container.css('margin-top'),
        container_ml: container.css('margin-left'),
        container_mr: container.css('margin-right'),
        hide_mm: wrapper.hasClass('hide-mm'),
        wrapper_bc: wrapper.css('background-color'),
        wrapper_bi: wrapper.css('background-image'),
      };

      dotclear.zenEdit.color = 'rgb(101,101,101)';

      // Set textured background if set
      if (dotclear.zenEdit.background !== '') {
        $('body').css('background-image', `url(${dotclear.zenEdit.base_url}${dotclear.zenEdit.background})`);
        if (dotclear.zenEdit.background.substr(0, 5) == 'dark/') {
          // Dark background
          dotclear.zenEdit.color = 'rgb(241,241,241)';
        }
      }

      // Hack some CSS attributes
      container.css('margin-top', '3em');
      if (dotclear.zenEdit.smallMargins == '1') {
        container.css('margin-left', dotclear.zenEdit.prop.hide_mm ? entry.css('margin-right') : '-13em');
        container.css('margin-right', '0');
      } else {
        container.css('margin-left', dotclear.zenEdit.prop.hide_mm ? '14.5em' : '1em');
        container.css('margin-right', '14.5em');
      }
      $('body').css('color', dotclear.zenEdit.color).css('background-color', 'rgb(248,248,248)');
      wrapper.css('background-color', 'transparent').css('background-image', 'none');
      page.css('background-color', 'transparent');
      main.css('background-color', 'transparent').css('background-image', 'none');

      // Hide everything not mandatory
      dotclear.zenEdit.stack = container.allBut();
      dotclear.zenEdit.stack.hide();

      // Change toolbar button title and icon
      jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEdit.hide;
      //  Don't know how to refresh this button !?! So, jQuery will help me
      $('button.jstb_zenEdit').attr('title', dotclear.msg.zenEdit.hide);

      dotclear.zenEdit.mode = 1;

      if (dotclear.zenEdit.fullScreen == '1' && fullScreenApi.supportsFullScreen) {
        fullScreenApi.requestFullScreen(document.documentElement);
      }
      return;
    }

    // Exit from zen mode

    // Reset textured background if set
    if (dotclear.zenEdit.background !== '') {
      $('body').css('background-image', 'none');
    }

    // Restore some CSS attributes as before
    container.css('margin-top', dotclear.zenEdit.prop.container_mt);
    container.css('margin-left', dotclear.zenEdit.prop.container_ml);
    container.css('margin-right', dotclear.zenEdit.prop.container_mr);
    $('body').css('color', dotclear.zenEdit.prop.body_tc).css('background-color', dotclear.zenEdit.prop.body_bc);
    wrapper.css('background-color', dotclear.zenEdit.prop.wrapper_bc).css('background-image', dotclear.zenEdit.prop.wrapper_bi);
    page.css('background-color', dotclear.zenEdit.prop.page_bc);
    main.css('background-color', dotclear.zenEdit.prop.main_bc).css('background-image', dotclear.zenEdit.prop.main_bi);

    // Show everything having been hidden before
    dotclear.zenEdit.stack.show();

    // Restore toolbar button title
    jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEdit.show;
    //  Don't know how to refresh this button !?! So, jQuery will help me
    $('button.jstb_zenEdit').attr('title', dotclear.msg.zenEdit.show);

    dotclear.zenEdit.mode = 0;

    if (dotclear.zenEdit.fullScreen == '1' && fullScreenApi.supportsFullScreen && fullScreenApi.isFullScreen) {
      fullScreenApi.cancelFullScreen(document.documentElement);
    }
  };

  jsToolBar.prototype.elements.zenEdit.title =
    dotclear.zenEdit.mode == 0 ? dotclear.msg.zenEdit.show : dotclear.msg.zenEdit.hide;
});
