/*global jQuery, dotclear, jsToolBar, fullScreenApi */
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

      if (typeof document[`${fullScreenApi.prefix}CancelFullScreen`] !== 'undefined') {
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
  if (typeof jQuery !== 'undefined') {
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
  jQuery.fn.allBut = function (context) {
    let otherList = jQuery();
    let processList = jQuery(context || 'body').children();

    while (processList.length > 0) {
      const cElem = processList.first();
      processList = processList.slice(1);

      if (cElem.filter(this).length !== this.length) {
        if (cElem.has(this).length > 0) {
          processList = processList.add(cElem.children());
        } else if (cElem.css('display') !== 'none') {
          // Get only not hidden element
          otherList = otherList.add(cElem);
        }
      }
    }
    return otherList;
  };
})();

// Toolbar button

dotclear.ready(() => {
  dotclear.mergeDeep(dotclear, dotclear.getData('zenedit'));

  jsToolBar.prototype.elements.zenEdit = {
    type: 'button',
    title: 'Zen',
    fn: {},
  };

  jsToolBar.prototype.elements.zenEdit.context = 'post';
  jsToolBar.prototype.elements.zenEdit.icon = dotclear.zenEdit.icon;
  jsToolBar.prototype.elements.zenEdit.icon_dark = dotclear.zenEdit.icon_dark;
  jsToolBar.prototype.elements.zenEdit.fn.wiki = () => dotclear.zenEdit.switch();
  jsToolBar.prototype.elements.zenEdit.fn.xhtml = () => dotclear.zenEdit.switch();
  jsToolBar.prototype.elements.zenEdit.fn.wysiwyg = () => dotclear.zenEdit.switch();
  jsToolBar.prototype.elements.zenEdit.fn.markdown = () => dotclear.zenEdit.switch();

  dotclear.zenEdit.switch = () => {
    const body = document.querySelector('body');
    const wrapper = document.getElementById('wrapper');
    const main = document.getElementById('main');
    const page = document.getElementById('content');
    const entry = document.getElementById('entry-wrapper');
    const container = document.getElementById('entry-content');
    const button = document.querySelector('button.jstb_zenEdit');

    if (dotclear.zenEdit.zenMode) {
      // Exit from zen mode

      // Reset textured background if set
      if (dotclear.zenEdit.background !== '') {
        body.style.backgroundImage = 'none';
      }

      // Restore some CSS attributes as before
      container.style.marginTop = dotclear.zenEdit.prop.container_mt;
      container.style.marginLeft = dotclear.zenEdit.prop.container_ml;
      container.style.marginRight = dotclear.zenEdit.prop.container_mr;
      body.style.color = dotclear.zenEdit.prop.body_tc;
      body.style.backgroundColor = dotclear.zenEdit.prop.body_bc;
      wrapper.style.backgroundColor = dotclear.zenEdit.prop.wrapper_bc;
      wrapper.style.backgroundImage = dotclear.zenEdit.prop.wrapper_bi;
      page.style.backgroundColor = dotclear.zenEdit.prop.page_bc;
      main.style.backgroundColor = dotclear.zenEdit.prop.main_bc;
      main.style.backgroundImage = dotclear.zenEdit.prop.main_bi;

      // Show everything having been hidden before
      dotclear.zenEdit.stack.show();

      // Restore toolbar button title
      jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEdit.show;
      button.setAttribute('title', dotclear.msg.zenEdit.show);

      dotclear.zenEdit.zenMode = false;

      if (dotclear.zenEdit.fullScreen && fullScreenApi.supportsFullScreen && fullScreenApi.isFullScreen) {
        fullScreenApi.cancelFullScreen(document.documentElement);
      }

      return;
    }

    // Switch into zen mode

    dotclear.zenEdit.prop = {
      // Get current status of some DOM element
      body_tc: body.style.color,
      body_bc: body.style.backgroundColor,
      page_bc: page.style.backgroundColor,
      main_bc: main.style.backgroundColor,
      main_bi: main.style.backgroundImage,
      container_mt: container.style.marginTop,
      container_ml: container.style.marginLeft,
      container_mr: container.style.marginRight,
      hide_mm: wrapper.classList.contains('hide-mm'),
      wrapper_bc: wrapper.style.backgroundColor,
      wrapper_bi: wrapper.style.backgroundImage,
    };

    dotclear.zenEdit.color = 'rgb(101,101,101)';

    // Set textured background if set
    if (dotclear.zenEdit.background !== '') {
      body.style.backgroundImage = `url(${dotclear.zenEdit.base_url}${dotclear.zenEdit.background})`;
      if (dotclear.zenEdit.background.substring(0, 5) === 'dark/') {
        // Dark background
        dotclear.zenEdit.color = 'rgb(241,241,241)';
      }
    }

    // Hack some CSS attributes
    container.style.marginTop = '3em';
    if (dotclear.zenEdit.smallMargins) {
      container.style.marginLeft = dotclear.zenEdit.prop.hide_mm ? entry.style.marginRight : '-13em';
      container.style.marginRight = '0';
    } else {
      container.style.marginLeft = dotclear.zenEdit.prop.hide_mm ? '14.5em' : '1em';
      container.style.marginRight = '14.5em';
    }
    body.style.color = dotclear.zenEdit.color;
    body.style.backgroundColor = 'rgb(248,248,248)';
    wrapper.style.backgroundColor = 'transparent';
    wrapper.style.backgroundImage = 'none';
    page.style.backgroundColor = 'transparent';
    main.style.backgroundColor = 'transparent';
    main.style.backgroundImage = 'none';

    // Hide everything not mandatory
    dotclear.zenEdit.stack = jQuery(container).allBut();
    dotclear.zenEdit.stack.hide();

    // Change toolbar button title and icon
    jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEdit.hide;
    button.setAttribute('title', dotclear.msg.zenEdit.hide);

    dotclear.zenEdit.zenMode = true;

    if (dotclear.zenEdit.fullScreen && fullScreenApi.supportsFullScreen) {
      fullScreenApi.requestFullScreen(document.documentElement);
    }
  };

  jsToolBar.prototype.elements.zenEdit.title = dotclear.zenEdit.zenMode ? dotclear.msg.zenEdit.hide : dotclear.msg.zenEdit.show;
});
