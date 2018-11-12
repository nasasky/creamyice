window.ZKZK = (function() {
  var fonts = {
    ja: {
      google: {
        families: ['Oswald']
      },
      custom: {
        families: ['Noto Sans Japanese'],
        urls: ['http://fonts.googleapis.com/earlyaccess/notosansjapanese.css']
      }
    },
    en: {
      google: {
        families: ['Lato', 'Oswald']
      }
    }
  };
  var lang = $('html').attr('lang');
  if(lang=='zh'){
    console.log(111);
  }else{
      WebFont.load(fonts[lang]);
  }

  var responsiveManager = ResponsiveManager();
  var pcMenu = PcMenu();
  var pcBackButton = PcBackButton();
  var spMenu = SpMenu();

  $('.menu_window_items a').on('click', onMenuLinkClick);
  
  responsiveManager.on('change', onResponsiveManagerChange);
  onResponsiveManagerChange(responsiveManager.getCurrentId());

  return {
    responsiveManager: responsiveManager,
    cookie: initCookie(),
    _GET: _GET()
  };


  function onMenuLinkClick(e) {
    var id = this.href.split('#')[1];
    var destPage = this.href.split('/').pop().replace(/(index\.html)?(#.+)?/, '');
    var currentPage = location.href.split('/').pop().replace(/(index\.html)?(#.+)?/, '');

    var isLocalLink = (
      id &&
      ( this.target !== '_blank' ) &&
      ( destPage === currentPage )
    );

    if ( !isLocalLink ) {

      e.stopPropagation();
      return;
    }

    e.preventDefault();
    TweenMax.delayedCall(0.3, function() {
      var o = {
        y: window.pageYOffset
      };

      TweenMax.to(o, 1.5, {
        y: $('#' + id).offset().top,
        ease: Sine.easeInOut,
        onUpdate: function() {
          window.scrollTo(0, o.y);
        }
      });
    });
  }


  function onResponsiveManagerChange(id) {
    if ( id === 'sp' ) {
      pcMenu.deactivate();
      pcBackButton.deactivate();
      spMenu.activate();
    } else if ( id === 'pc' ) {
      spMenu.deactivate();
      pcMenu.activate();
      pcBackButton.activate();
    }
  }


  function PcMenu() {
    var $container = $('.menu');
    var $content = $container.find('.menu_window');
    var $links = $content.find('.menu_window_items li');

    var $button = $('.nav_buttons_menu');
    var $shopButton = $('.nav_buttons_shop');
    var $close = $content.find('.menu_window_close');

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      $button.on('click', onButtonClick);
      $close.on('click', onCloseClick);
      $links.on('click', onCloseClick);
    }


    function deactivate() {
      $button.off('click', onButtonClick);
      $close.off('click', onCloseClick);
      $links.off('click', onCloseClick);
    }

    function onButtonClick(e) {
      e.preventDefault();
      open();
    }


    function onCloseClick(e) {
      close();
    }


    function open() {
      $container.show();
      $content.show();

      TweenMax.fromTo($content, 0.3, {
        width: 0
      }, {
        width: 320
      });

      $links.css({
        opacity: 0
      });
      TweenMax.staggerFromTo($links, 0.3, {
        opacity: 0,
        x: 20
      }, {
        opacity: 1,
        x: 0,
        delay: 0.3
      }, 0.05);
    }


    function close() {
      TweenMax.to($button.add($shopButton), 0.3, {
        right: 0
      });
      TweenMax.to($content, 0.3, {
        width: 0,
        onComplete: function() {
          $container.hide();
        }
      });
    }
  }

  function PcBackButton() {
    var $button = $('.common_back a');

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( !$button.length ) {
        return;
      }

      $(window).on('resize', onResize);
      onResize();
    }


    function deactivate() {
      $(window).off('resize', onResize);
      setTimeout(function() {
        $button.removeAttr('style');
      });
    }


    function onResize() {
      var svgHtml = '<svg xmlns="http://www.w3.org/2000/svg" width="273" height="{{height}}" preserveAspectRatio="none"><path d="M0,{{height}}V0h273v55z" fill="#000"/></svg>';

      var height = 55 + (180 / document.body.clientWidth * 273);
      var dataUrl = 'data:image/svg+xml,' +
            encodeURIComponent(svgHtml.replace(/{{height}}/g, height));
      
      $button.css({
        height: height,
        background: 'url(' + dataUrl + ')',
        backgroundRepeat: 'no-repeat'
      });
    }
  }


  function SpMenu() {
    var $container = $('.menu');
    var $overlay = $container.find('.menu_overlay');
    var $content = $container.find('.menu_window');
    var $links = $content.find('.menu_window_items li');
    
    var $button = $('.nav_buttons_menu');
    var $close = $content.find('.menu_window_close');

    var isOldBrowser = navigator.userAgent.match(/Android 2/);

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      $button.on('click', onButtonClick);
      $close.on('click', onCloseClick);
      $links.on('click', onCloseClick);
    }


    function deactivate() {
      $button.off('click', onButtonClick);
      $close.off('click', onCloseClick);
      $links.off('click', onCloseClick);
    }

    function onButtonClick(e) {
      e.preventDefault();
      open();
    }


    function onCloseClick(e) {
      close();
    }


    function open() {
      $container.show();
      $content.show();

      if ( isOldBrowser ) {
        return;
      }
      
      TweenMax.fromTo($overlay, 0.3, {
        opacity: 0
      }, {
        opacity: 1
      });

      TweenMax.fromTo($content, 0.3, {
        right: -246
      }, {
        right: 0
      });

      TweenMax.staggerFromTo($links, 0.3, {
        opacity: 0,
        x: 20
      }, {
        opacity: 1,
        x: 0,
        delay: 0.3
      }, 0.05);
    }


    function close() {
      if ( isOldBrowser ) {
        $container.hide();
        return;
      }

      TweenMax.fromTo($overlay, 0.2, {
        opacity: 1
      }, {
        opacity: 0
      });

      TweenMax.to($content, 0.2, {
        right: -246,
        onComplete: function() {
          $container.hide();
        }
      });
    }
  }



  function ResponsiveManager() {
    var eventDispatcher = EventDispatcher();
    var currentFunc;
    var currentId;

    var breakPoints = [{
      id: 'sp',
      test: function(winWidth) {
        return ( winWidth < 800 );
      }
    }, {
      id: 'pc',
      test: function(winWidth) {
        return ( winWidth >= 800 ) ;
      }
    }];

    $(window).on('resize', onResize);
    onResize();
    
    return {
      on: eventDispatcher.on,
      off: eventDispatcher.off,
      onResize: onResize,
      getCurrentId: function() {
        return currentId;
      } 
    };


    function onResize(e) {
      var func;
      var id;

      _.forEach(breakPoints, function(breakPoint) {
        if ( breakPoint.test(window.innerWidth) ) {
          id = breakPoint.id;
          return false;
        }
      });

      if ( id === currentId ) {
        return;
      }
      eventDispatcher.trigger('change', id);
      currentId = id;
    }
  }
  
  
  function EventDispatcher() {
    var _listenersMap = {};

    return {
      on: on,
      off: off,
      trigger: trigger
    };
    

    function on(type, listener) {
      if ( typeof type === 'object' ) {
        for ( var key in type ) {
          on(key, type[key]);
        }
        return;
      }

      if ( !_listenersMap[type] ) {
        _listenersMap[type] = [];
      }
      var listeners = _listenersMap[type];
      if ( $.inArray(listener, listeners) !== -1 ) {
        return;
      }
      listeners.push(listener);
    }
    

    function off(type, listener) {
      if ( typeof type === 'object' ) {
        for ( var key in type ) {
          off(key, type[key]);
        }
        return;
      }

      if ( !_listenersMap[type] ) {
        return;
      }

      var listeners = _listenersMap[type];
      if ( !listener ) {
        _listenersMap[type] = [];
        return;
      }
      var index = $.inArray(listener, listeners);
      if ( index === -1 ) {
        return;
      }

      listeners.splice(index, 1);
    }
    
    
    function trigger(type, data) {
      var listeners = _listenersMap[type];

      if ( !listeners || listeners.length === 0 ) {
        return;
      }

      if ( arguments[2] ) {
        var args = Array.prototype.slice.call(arguments, 1);
      }

      listeners = listeners.concat();
      for ( var i = 0; i < listeners.length; i++ ) {
        var listener = listeners[i];
        if ( !listener ) {
          continue;
        }

        ( args ) ?
          listener.apply(null, args) :
          listener(data);
      }
    }
  }  
  
  

  function initCookie() {
    var COOKIE_PATH = '';

    return {
      get: get,
      set: set,
      erase: erase
    };
    
    
    function get(key) {
      var pairs = document.cookie.split(/;\s*/);
      for ( var i = 0; i < pairs.length; i++ ) {
        var a = pairs[i].split('=');
        if ( a[0] === key ) {
          return unescape(a[1]);
        }
      }
      
      return null;
    }


    function set(key, value, days) {
      var expireString = '';
      if ( days !== undefined ) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expireString = '; expires=' + date.toGMTString();
      }
      
      var cookieString = key + '=' + escape(value) + expireString/* + '; path=' + COOKIE_PATH*/   ;
      document.cookie = cookieString;
    }
    
    
    function erase(key) {
      set(key, ' ', -1);
    }
  }



  function _GET() {
    var result = {};

    if ( !location.search ) {
      return result;
    }

    var str = location.search.substring(1);

    var pairs = str.split('&');
    for( var i = 0; i < pairs.length; i++ ) {
      var pair = pairs[i];
      var a = pair.split('=');

      result[a[0]] = ( a[1] === '0' ) ?
        false :
        decodeURIComponent(a[1]);
    }
    
    return result;
  }
}());