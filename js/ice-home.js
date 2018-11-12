(function() {
console.log('oh');
  var $window = $(window);
  var $body = $('body');

  var IS_DEV = false;

  expandShops();

  var cookie = window.ZKZK.cookie;
  var _GET = window.ZKZK._GET;
  var pc = ResponsivePc();
  var sp = ResponsiveSp();
  var isFirstOpen = true;
  var winWidth;

  $window.on('resize', onResize);
  onResize();

  var responsiveManager = window.ZKZK.responsiveManager;
  responsiveManager.on('change', onResponsiveManagerChange);
  onResponsiveManagerChange(responsiveManager.getCurrentId());

  FastClick.attach(document.body);

  addIndirectHovers();
  return;


  function expandShops() {
    var tmpl = _.template($('#tmpl_index_shops').html());
    var data = parse($('.index_shops li'));
    console.log($('.index_shops li'));
    var html = tmpl(data);

    $('.index_shops').html(html);
    return;


    function parse($items) {
      var results = $items.toArray().map(function(item, index) {
        var $item = $(item);

        return {
          href: $item.find('a').attr('href'),
          bgSrc: $item.find('.bg').attr('src'),
          title: $item.find('.title').attr('alt'),
          titleSrc: $item.find('.title').attr('src'),
          y: index * 324
        };
      });

      return {
        items: results
      };
    }
  }


  function onResize() {
    winWidth = document.body.clientWidth;
  }


  function onResponsiveManagerChange(id) {
    if ( id === 'pc' ) {
      if ( !isFirstOpen ) {
        sp.close();
      }
      pc.open(isFirstOpen);
    }

    if ( id === 'sp' ) {
      if ( !isFirstOpen ) {
        pc.close();
      }
      sp.open(isFirstOpen);
    }

    isFirstOpen = false;
  }


  function ResponsivePc() {
    var whats = PcWhats();
    var sectionAnimations = SectionAnimations();
    var intro = PcIntro();
    var clipPathManager = ClipPathManager();
    var introPromise;

    return {
      open: open,
      close: close
    };


    function open(isFirstOpen) {
      $('.index_top .index_section_bg').css({
        height: window.innerHeight
      });
      $('.nav_buttons').removeClass('-shrink -shrinked');

      if ( isFirstOpen && ( !cookie.get('visited') || _GET.intro ) ) {
        introPromise = intro.open().then(function() {
          whats.activate();
          clipPathManager.activate();
          sectionAnimations.activate(true);
        });
        return;
      }

      intro.skip();
      whats.activate();
      sectionAnimations.activate();
      clipPathManager.activate();
    }


    function close() {
      var $deferred = $.Deferred();

      if ( introPromise ) {
        // 銈ゃ兂銉堛儹鍐嶇敓涓伀SP銇搞伄鍒囥倞鏇裤亪銇岃蛋銇ｃ仧銈夈€併偆銉炽儓銉啀鐢熷畬浜嗐伨銇у緟銇熴仜銈�
        return introPromise.then(function() {
          introPromise = null;
        }).then(close);
      }

      $('.index_top .index_section_bg').css({
        height: 'auto'
      });

      $('.index_top_logo').hide();

      whats.deactivate();
      sectionAnimations.deactivate();
      clipPathManager.deactivate();

      return $deferred.resolve();
    }
  }



  function PcWhats() {
    var $container = $('.index_about_points');
    var $titles = $container.find('.js-zoom');
    var $contents = $container.find('dd');
    var $openContent;

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      $titles.on('mouseenter', onTitleMouseEnter);
      $titles.on('mouseleave', onTitleMouseLeave);
    }


    function deactivate() {
      if ( $openContent ) {
        $openContent.hide();
      }
      $titles.off('mouseenter', onTitleMouseEnter);
      $titles.off('mouseleave', onTitleMouseLeave);
    }


    function onTitleMouseEnter(e) {
      e.preventDefault();

      $openContent = $(this).next();
      show($openContent);
    }


    function onTitleMouseLeave(e) {
      e.preventDefault();

      hide($openContent);
      $openContent = null;
    }


    function show($content) {
      var $mask = $content.find('.index_about_content_mask');
      var $text = $content.find('p');

      TweenMax.fromTo($content, 0.3, {
        scale: 0.7,
        opacity: 0,
        display: 'block'
      }, {
        scale: 1,
        opacity: 1,
        ease: Back.easeOut.config(1)
      });

      $mask.hide();

      TweenMax.fromTo($mask, 0, {
        left: -180
      }, {
        left: 360,
        delay: 0
      });
    }


    function hide($content) {
      TweenMax.to($content, 0.15, {
        opacity: 0,
        scale: 0.95,
        display: 'none'
      });
    }
  }



  function ClipPathManager() {
    var $masks = $('[data-d]');
    var templates = $masks.map(function() {
      return this.getAttribute('data-d');
    });
    var $paths = $masks.find('path');

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      $window.on('resize', onResize);
      onResize();

      // 3鍘熷墖銈汇偗銈枫儳銉炽仹銇疨C銇с伄銇裤優銈广偗銈掋亱銇戙倠
      $('[data-clip-path]').each(function() {
        $(this).attr({
          'clip-path': $(this).attr('data-clip-path')
        });
      });
    }


    function deactivate() {
      $window.off('resize', onResize);
      onResize();

      $('[data-clip-path]').each(function() {
        $(this).removeAttr('clip-path');
      });
    }


    function onResize() {
      var width = document.body.clientWidth;
      _.each(templates, function(template, index) {
        if ( !template ) {
          return;
        }

        $paths.eq(index).attr({
          d: template.replace('{{width}}', width).replace('{{halfWidth}}', width / 2)
        });
      });
    }
  }



  function PcIntro() {
    var $deferred;

    var $container = $('.index_intro');
    var $bg = $('.index_intro_bg');
    var $spinner = $container.find('.index_intro_spinner');

    var $logoSvg = $('.index_top_logo');
    var $logo = $logoSvg.find('.logo');
    var $logoMask = $logoSvg.find('.logoMask');
    var $croquant = $logoSvg.find('.croquant');
    var $zak1 = $logoSvg.find('.zak1');
    var $zak2 = $logoSvg.find('.zak2');

    var particle;

    return {
      open: open,
      skip: skip
    };


    function skip() {
      $container.hide();
      $logoSvg.show();
    }


    function open() {
      $deferred = $.Deferred();

      if ( cookie.get('visited') && !_GET.intro ) {
        $container.hide();
        $logoSvg.show();

        return $deferred.resolve();
      }

      cookie.set('visited');
      $container.addClass('start');
      particle = ParticleEmitter($logoSvg);

      var fromRad = Math.PI * 0.2;
      var toRad = Math.PI * 1.8;
      var data = getPathData(fromRad, toRad);

      $spinner.find('path').attr('d', data);

      spin();

      var isLoaded = false;
      var timeout = false;

      window.imagesLoaded($('.index_top')[0], function() {
        isLoaded = true;
      });

      setTimeout(function() {
        timeout = true;
      }, 10000);

      return $deferred.promise();


      function spin() {
        var o = {
          p: 0,
          fromRad: Math.PI * 0,
          toRad: Math.PI * 2
        };

        TweenMax.fromTo($spinner, 1, {
          rotationZ: 0,
          transformOrigin: '22px 22px'
        }, {
          rotationZ: 360,
          ease: Sine.easeInOut,
          onComplete: function() {
            console.log(timeout, isLoaded);
            if ( !timeout && ( $('html').hasClass('wf-loading') || !isLoaded ) ) {
              spin();
              return;
            }

            onPreloadComplete();
          }
        });
      }
    }


    function onPreloadComplete() {
      var o = {
        p: 0,
        fromRad: Math.PI * 0.2,
        toRad: Math.PI * 1.8
      };

      TweenMax.to(o, 0.5, {
        p: 1,
        fromRad: o.toRad,
        onUpdate: function() {

          var d = getPathData(o.fromRad, o.toRad);
          $spinner.find('path').attr({
            d: d,
            'stroke-width': 5 - (o.p * 4)
          });

          TweenMax.set($spinner, {
            rotationZ: o.p * 100
          });
        },
        ease: Cubic.easeIn
      });

      $logoSvg.show();
      $logo.hide();
      $zak1.hide();
      $zak2.hide();

      TweenMax.fromTo($croquant, 0.3, {
        opacity: 0,
        y: 30,
        transformOrigin: '50% 50%',
        display: 'block'
      }, {
        opacity: 1,
        y: 0,
        delay: 0.5
      });

      TweenMax.fromTo($logo, 0.1, {
        scale: 0.96,
        transformOrigin: '92px 92px'
      }, {
        scale: 1,
        delay: 1.35,
        ease: Linear.easeNone,
        onStart: function() {
          $zak1.show();
          $logo.show();
          particle.emit();
        }
      });
      TweenMax.fromTo($zak1, 0.1, {
        scale: 1.1,
        transformOrigin: '51px 15px'
      }, {
        scale: 1,
        delay: 1.3,
        ease: Linear.easeNone
      });

      TweenMax.fromTo($logo, 0.1, {
        scale: 0.96
      }, {
        scale: 1,
        delay: 1.9,
        ease: Linear.easeNone,
        onStart: function() {
          $logoMask.hide();
          $zak2.show();
          particle.emit();
        }
      });
      TweenMax.fromTo($zak2, 0.1, {
        scale: 1.1,
        transformOrigin: '51px 15px'
      }, {
        scale: 1,
        delay: 1.85,
        ease: Linear.easeNone
      });

      TweenMax.to($bg, 0.3, {
        opacity: 0,
        display: 'none',
        delay: 2.8,
        onStart: function() {
          window.scrollTo(0, 0);
        },
        onComplete: function() {
          $container.hide();
        }
      });

      TweenMax.fromTo($('.index_top_bg_01'), 5, {
        scale: 1.4,
        opacity: 1
      }, {
        scale: 1,
        delay: 2.8,
        ease: Sine.easeOut
      });
      TweenMax.delayedCall(4.3, function() {
        $deferred.resolve();
      });

      TweenMax.fromTo($('.index_top_mask'), 0.5, {
        width: 0,
        backgroundSize: winWidth + 'px 100%'
      }, {
        width: '100%',
        delay: 3.4,
        onComplete: function() {
          $(this).removeAttr('style');
        }
      });
    }


    function getPathData(fromRad, toRad) {
      var r = 17;
      var strokeWidth = 5;

      var startX = Math.sin(fromRad) * r + (r + strokeWidth);
      var startY = -Math.cos(fromRad) * r + (r + strokeWidth);
      var endX = Math.sin(toRad) * r + (r + strokeWidth);
      var endY = -Math.cos(toRad) * r + (r + strokeWidth);
      var largeArcFlag = ( Math.abs(fromRad - toRad) > Math.PI ) ? 1 : 0;

      return [
        ('M' + [startX, startY].join(',')),
        ('A' + [r, r, 0, largeArcFlag, 1, endX, endY].join(','))
      ].join('');
    }


    function ParticleEmitter($container) {
      var triangles = [];
      var numParticlesPerEmit = 10;
      for ( var i = 0; i < numParticlesPerEmit * 2; i++ ) {
        var angle = getRandomAngle();//20 + Math.random() * 70;
        var size = Math.random();
        var t = getTriangle(angle, size);
        $container.append(t);
        t.setAttribute('visibility', 'hidden');
        triangles.push({
          element: t,
          angle: angle,
          size: size
        });
      }

       return {
        emit: emit
      };


      function getRandomAngle() {
        var areas = [[20, 90], [135, 180], [190, 270], [310, 360]];
        var areaWidths = areas.map(function(area) {
          return area[1] - area[0];
        });
        var areaTotalWidth = areaWidths.reduce(function(result, current) {
          return result + current;
        }, 0);

        var r = Math.random();
        var pos = r * areaTotalWidth;

        for ( var i = 0; i < areaWidths.length; i++ ) {
          if ( pos < areaWidths[i] ) {
            break;
          }

          pos -= areaWidths[i];
        }

        return areas[i][0] + pos;
      }


      function emit() {
        for ( var i = 0; i < numParticlesPerEmit; i++ ) {
          var item = triangles.shift();
          var t = item.element;
          var angle = item.angle;

          var rad = (function() {
            angle += 90;
            if ( angle > 360 ) {
              angle -= 360;
            }

            return angle / 180 * Math.PI;
          }());

          var x = Math.sin(rad) * (1.15+(Math.random() * 0.2))* 95 + 105;
          var y = -Math.cos(rad) * (1.15+(Math.random() * 0.2)) * 95 + 95;

          var delay = Math.random() * 0.05;
          TweenMax.fromTo(t, 0.3, {
            transformOrigin: '50% 50%',
            visibility: 'visible'
          }, {
            x: x,
            y: y,
            rotationZ: ((angle > 90 && angle < 270) ? -360 : 360) * Math.random(),
            ease: Quint.easeOut,
            delay: delay
          });
          TweenMax.to(t, 0.3, {
            opacity: 0,
            scale: Math.random(),
            ease: Sine.easeOut,
            display: 'none',
            delay: delay + 0.2
          });
        }
      }


      function getTriangle(angle, size) {
        var path = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
        var maxSize = 13.5;
        var points = [
          0, 0,
          (Math.random() * maxSize), (Math.random() * maxSize),
          (Math.random() * maxSize), (Math.random() * maxSize)
        ].join(' ');
        $(path).attr({
          points: points,
          fill: '#fff'
        });

        var pos = 0.8 + Math.random() * 0.15;
        var rad = (function() {
          angle += 90;
          if ( angle > 360 ) {
            angle -= 360;
          }

          return angle / 180 * Math.PI;
        }());

        var dy = -Math.cos(rad) * pos * 95;
        var dx = Math.sin(rad) * pos * 95;

        TweenMax.set(path, {
          x: 105 + dx,
          y: 95 + dy
        });

        return path;
      }
    }
  } // /PcIntro



  function SectionAnimations() {
    var animations = [
      TopAnimation(),
      ConceptAnimation(),
      ShopsBannerAnimation(),
      PrinciplesAnimation(),
      Principle1Animation(),
      Principle2Animation(),
      Principle3Animation(),
      AboutAnimation(),
      PriceAnimation(),
      HowtoAnimation(),
      // ContactAnimation(),
      ShopsAnimation()
    ];

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate(openFromIntro) {
      _.forEach(animations, function(animation) {
        animation.activate(openFromIntro);
      });
    }


    function deactivate() {
      _.forEach(animations, function(animation) {
        animation.deactivate();
      });
    }
  }



  function TopAnimation() {
    var $container = $('.index_top');
    var $bg = $container.find('.index_section_bg');
    var $bgs = $container.find('.index_top_bgs > div');
    var currentBgIndex = -1;
    var isActive;
    var showNextTimer;

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate(openFromIntro) {
      isActive = true;

      $window.on({
        scroll: onScroll,
        resize: onResize
      });
      onScroll();

      if ( openFromIntro ) {
        currentBgIndex = 0;
        showNextTimer = setTimeout(showNextImage, 3000);
      } else {
        TweenMax.killTweensOf($bgs);
        showNextImage();
      }
    }


    function deactivate() {
      isActive = false;
      clearTimeout(showNextTimer);

      $window.off({
        scroll: onScroll
      });
      window.removeEventListener('resize', onResize);

      $bg.removeAttr('style');
      $bgs.css({
        top: 0
      });
    }


    function onScroll() {
      var scrollTop = window.pageYOffset;

      $bgs.css({
        top:  (scrollTop * 0.2)
      });
    }


    function showNextImage() {
      if ( !isActive ) {
        return;
      }

      currentBgIndex++;
      if ( currentBgIndex >= $bgs.length ) {
        currentBgIndex = 0;
      }

      var $current = $bgs.eq(currentBgIndex);

      TweenMax.fromTo($current, 0.7, {
        opacity: 0
      }, {
        opacity: 1
      });

      TweenMax.fromTo($current, 6, {
        scale: 1.2,
        zIndex: 1,
        display: 'block'
      }, {
        scale: 1,
        ease: Sine.easeOut
      });

      clearTimeout(showNextTimer);
      showNextTimer = setTimeout(function() {
        $bgs.not($current).hide();
        $current.css({
          zIndex: 0
        });
        showNextImage();
      }, 5000);
    }


    function onResize(e) {
      if ( !isActive ) {
        return;
      }

      $bg.css({
        height: window.innerHeight
      });
    }
  }



  function ConceptAnimation() {
    var $container = $('.index_concept');
    var $title = $container.find('h2');
    var $lead = $container.find('.index_concept_lead');
    var $texts = $container.find('p');

    var titleAnimation;

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isPlayed = false;
        activate();
        play();
        deactivate();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( isPlayed ) {
        return;
      }

      isActive = true;
      if ( !titleAnimation ) {
        titleAnimation = getTitleAnimation($title);
      } else {
        titleAnimation.progress(0);
      }
      $container.css({
        opacity: 0
      });

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;
      $container.css({
        opacity: 1
      });
      titleAnimation.progress(1);
      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }
      threshold = $container.offset().top - window.innerHeight * 0.85;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();
      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      TweenMax.to($container, 1, {
        opacity: 1
      });
      TweenMax.fromTo($lead, 1, {
        opacity: 0,
        scale: 1.2
      }, {
        opacity: 1,
        scale: 1,
        ease: Quint.easeOut,
        delay: 0.4
      });

      TweenMax.staggerFromTo($texts, 0.6, {
        opacity: 0,
        y: 60
      }, {
        opacity: 1,
        y: 0,
        delay: 1.2
      }, 0.1);

      titleAnimation.play(0);
    }
  }



  function ShopsBannerAnimation() {
    var $container = $('.index_shopsBanner');
    var $title = $container.find('h2');
    var $bgContainer = $container.find('.index_section_bg');
    var $bg = $container.find('svg');
    var $label = $container.find('.index_shopsBanner_label');

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        isPlayed = false;
        activate();
        play();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      isActive = true;
      $window.on({
        resize: onResize,
        scroll: onScroll
      });
      onResize();

      if ( isPlayed ) {
        return;
      }

      $bg.find('image').hide();
      $label.hide();

      $container.find('.shopsBanner_bg_img').on({
        mouseover: onMouseOver,
        mouseout: onMouseOut
      });
    }


    function deactivate() {
      isActive = false;

      $container.css({
        opacity: 1
      });
      $label.show();
      $bg.find('image').show();

      $container.find('.shopsBanner_bg_img').off({
        mouseover: onMouseOver,
        mouseout: onMouseOut
      });

      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $container.offset().top - window.innerHeight * 0.7;
      onScroll();
    }


    function onScroll(e) {
      TweenMax.set($bg.find('image'), {
        y: (window.pageYOffset - threshold) * 0.2
      });

      if ( isPlayed || window.pageYOffset < threshold ) {
        return;
      }

      play();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      $container.css({
        overflow: 'hidden'
      });
      $bg.css({
        width: winWidth,
        display: 'block'
      });
      $bg.find('image').show();

      TweenMax.fromTo($bg.find('#shopBannerImgMask rect'), 1, {
        width: 0
      }, {
        width: '100%'
      });
      TweenMax.fromTo($bg.find('image'), 1, {
        x: -120
      }, {
        x: 0,
        onComplete: function() {
          $bg.css({
            width: '100%'
          });
        }
      });

      TweenMax.fromTo($label, 0.3, {
        display: 'block',
        scale: 1.2,
        opacity: 0
      }, {
        scale: 1,
        opacity: 1,
        delay: 0.8
      });
    }


    function onMouseOver() {
      $container.addClass('hover');
    }


    function onMouseOut() {
      $container.removeClass('hover');
    }
  }



  function PrinciplesAnimation() {
    var $container = $('.index_principles');
    var $title = $container.find('h2');
    var $lead = $container.find('.index_concept_lead');
    var $texts = $container.find('p');

    var titleAnimation;

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        isPlayed = false;
        activate();
        play();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( isPlayed ) {
        return;
      }

      isActive = true;

      if ( !titleAnimation ) {
        titleAnimation = getTitleAnimation($title);
      } else {
        titleAnimation.progress(0);
      }

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;

      titleAnimation.progress(1);
      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $container.offset().top - window.innerHeight * 0.85;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();
      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      TweenMax.to($container, 1, {
        opacity: 1
      });
      titleAnimation.play(0);
    }
  }



  function Principle1Animation() {
    return PrincipleAnimation($('.index_principles_item').eq(0), 0);
  }



  function Principle2Animation() {
    return PrincipleAnimation($('.index_principles_item').eq(1), 1);
  }



  function Principle3Animation() {
    return PrincipleAnimation($('.index_principles_item').eq(2), 2);
  }



  function PrincipleAnimation($container, principleIndex) {
    var $text = $container.find('.index_principles_item_text');
    var $img = $container.find('.index_principles_item_img');
    var $svg = $img.find('.only_pc svg');

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isPlayed = false;
        activate();
        play();
        deactivate();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( isPlayed ) {
        $img.css({
          position: 'relative'
        });
        return;
      }

      isActive = true;
      $container.css({
        opacity: 0
      });

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;

      $container.css({
        opacity: 1
      });
      $text.removeAttr('style');
      $img.removeAttr('style');

      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $container.offset().top - window.innerHeight * 0.7;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();
      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      ( principleIndex % 2 ) ?
        playLeftToRight() :
        playRightToLeft();
    }


    function playLeftToRight() {
      TweenMax.to($container, 0, {
        opacity: 1
      });

      TweenMax.fromTo($img, 0.6, {
        position: 'absolute',
        left: 0,
        width: 0,
        height: 660,
        overflow: 'hidden'
      }, {
        width: '50%'
      });

      $svg.css({
        position: 'absolute',
        left: 0,
        width: winWidth / 2
      });
      TweenMax.fromTo($svg.find('image'), 1, {
        x: -50
      }, {
        x: 0,
        onComplete: function() {
          $svg.css({
            width: '100%'
          });
        }
      });

      TweenMax.fromTo($text, 0.3, {
        opacity: 0,
        y: '-40%'
      }, {
        opacity: 1,
        y: '-50%',
        delay: 0.2
      });
    }


    function playRightToLeft() {
      TweenMax.to($container, 0, {
        opacity: 1
      });

      TweenMax.fromTo($img, 0.6, {
        position: 'absolute',
        right: 0,
        width: 0,
        height: 660,
        overflow: 'hidden'
      }, {
        width: '50%'
      });

      $svg.css({
        position: 'absolute',
        right: 0,
        width: winWidth / 2
      });
      TweenMax.fromTo($svg.find('image'), 1, {
        x: 50
      }, {
        x: 0,
        onComplete: function() {
          $svg.css({
            width: '100%'
          });
        }
      });

      TweenMax.fromTo($text, 0.3, {
        opacity: 0,
        y: '-40%'
      }, {
        opacity: 1,
        y: '-50%',
        delay: 0.2
      });
    }
  }



  function AboutAnimation() {
    var $container = $('.index_about');
    var $title = $container.find('h2');
    var $texts = $container.find('.index_about_point_01, .index_about_point_02, .index_about_point_03, .index_about_point_04, .index_about_point_07');
    var $image02 = $container.find('.index_about_bg_02');

    var $lines = $container.find('.index_section_bg .only_pc svg path');
    var $crunchText = $container.find('.index_about_content_07');

    var titleAnimation;
    var isActive;

    var isPlayed;
    var threshold;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isPlayed = false;
        activate();
        play();
        deactivate();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };



    function activate() {
      if ( isPlayed ) {
        return;
      }

      isActive = true;
      if ( !titleAnimation ) {
        titleAnimation = getTitleAnimation($title);
      } else {
        titleAnimation.progress(0);
      }

      $image02.css({
        opacity: 0
      });
      $texts.css('opacity', 0);
      $lines.hide();
      $crunchText.css('opacity', 0);

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;
      titleAnimation.progress(1);
      $texts.removeAttr('style');
      $lines.show();

      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $container.offset().top - window.innerHeight * 0.7;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();

      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      TweenMax.delayedCall(0.3, function() {
        titleAnimation.play(0);
      });

      TweenMax.staggerFromTo($image02, 0.3, {
        opacity: 0,
        scale: 0.5
      }, {
        opacity: 1,
        scale: 1,
        delay: 0.6,
        ease: Back.easeOut
      }, 0.2);

      $texts.css('opacity', 0);

      $lines.show().each(function(index) {
        draw(this, index);
      });


      TweenMax.fromTo($texts.eq(4).add($crunchText), 0.3, {
        opacity: 0,
        y: 20
      }, {
        opacity: 1,
        y: 0,
        delay: 2.2
      });
    }


    function draw(path, index) {
      var length = path.getTotalLength();

      path.style.strokeDasharray = length + ' ' + length;
      path.style.strokeDashoffset = length;

      TweenMax.to(path, 0.3, {
        strokeDashoffset: 0,
        delay: 1 + index * 0.2
      });

      if ( !$texts[index] ) {
        return;
      }

      TweenMax.fromTo($texts[index], 0.3, {
        opacity: 0,
        y: 20
      }, {
        opacity: 1,
        y: 0,
        delay: 1.4 + index * 0.2
      });
    }
  }



  function PriceAnimation() {
    var $container = $('.index_price');
    var $title = $container.find('h2');
    var $lead = $container.find('.index_concept_lead');
    var $texts = $container.find('p');
    var $masks = $container.find('.index_price_item_mask');
    var $images = $container.find('dt:not(:eq(3))');

    var titleAnimation;

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isPlayed = false;
        activate();
        play();
        deactivate();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( isPlayed ) {
        return;
      }

      isActive = true;
      if ( !titleAnimation ) {
        titleAnimation = getTitleAnimation($title);
      } else {
        titleAnimation.progress(0);
      }
      $container.css({
        opacity: 0
      });

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;
      titleAnimation.progress(1);

      $container.css({
        opacity: 1
      });

      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $container.offset().top - window.innerHeight * 0.85;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();
      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      TweenMax.to($container, 0.3, {
        opacity: 1
      });

      TweenMax.staggerFromTo($masks, 0.6, {
        height: 650
      }, {
        height: 0,
        delay: 0.3
      }, 0.2);

      TweenMax.staggerFromTo($images, 0.5, {
        scale: 0.8
      }, {
        scale: 1,
        ease: Back.easeOut,
        delay: 0.3
      }, 0.2);

      titleAnimation.play(0);
    }
  }



  function HowtoAnimation() {
    var $container = $('.index_howto');
    var $title = $container.find('h2');
    var $fresh = $container.find('.index_howto_fresh');
    var $mask = $container.find('.index_howto_mask');
    var $bgImg = $container.find('.only_pc >img');
    var $bgText = $container.find('.only_pc p');
    var $texts = $container.find('.index_section_content');

    var titleAnimation;
    var freshAnimation;

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isPlayed = false;
        activate();
        play();
        deactivate();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( isPlayed ) {
        return;
      }

      isActive = true;
      if ( !titleAnimation ) {
        titleAnimation = getTitleAnimation($title);
        freshAnimation = getTitleAnimation($fresh);
      } else {
        titleAnimation.progress(0);
        freshAnimation.progress(0);
      }

      $texts.find('>*').css({
        opacity: 0
      });

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;

      titleAnimation.progress(1);
      freshAnimation.progress(1);
      $texts.find('>*').removeAttr('style');

      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $container.offset().top - window.innerHeight * 0.85;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();
      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      TweenMax.fromTo($mask, 0.6, {
        width: '50%',
        display: 'block'
      }, {
        width: '0%',
        display: 'none',
        onComplete: function() {
        }
      });
      $title.css({
        opacity: 0
      });
      TweenMax.delayedCall(0.3, function() {
        titleAnimation.play(0);
      });
      TweenMax.delayedCall(0.5, function() {
        freshAnimation.play(0);
      });

      TweenMax.fromTo($bgImg, 1, {
        x: -50,
        width: '110%',
        maxWidth: 'none'
      }, {
        x: 0
      });
      TweenMax.fromTo($bgText, 1, {
        x: -20
      }, {
        x: 0
      });

      TweenMax.staggerFromTo($texts.find('>*').not($title).not($fresh), 0.5, {
        opacity: 0,
        y: 30
      }, {
        opacity: 1,
        y: 0,
        delay: 1
      }, 0.1);
    }
  }



  function ContactAnimation() {
    var $container = $('.index_contact');
    var $title = $container.find('h2');
    var $lead = $container.find('.index_section_lead');
    var $texts = $container.find('p');
    var $masks = $container.find('.index_contact_item_mask');

    var titleAnimation;

    var isPlayed;
    var threshold;
    var isActive;

    if ( IS_DEV ) {
      $container.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isPlayed = false;
        activate();
        play();
        deactivate();
      });
    }

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      if ( isPlayed ) {
        return;
      }

      isActive = true;
      if ( !titleAnimation ) {
        titleAnimation = getTitleAnimation($title.add($lead));
      } else {
        titleAnimation.progress(0);
      }
      $container.css({
        opacity: 0
      });

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;
      titleAnimation.progress(1);

      $container.css({
        opacity: 1
      });

      removeEventListeners();
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }
      threshold = $container.offset().top - window.innerHeight * 0.7;
      onScroll();
    }


    function onScroll(e) {
      if ( window.pageYOffset < threshold ) {
        return;
      }

      play();

      removeEventListeners();
    }


    function removeEventListeners() {
      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function play() {
      isPlayed  = true;

      TweenMax.to($container, 0.3, {
        opacity: 1
      });
      titleAnimation.play(0);

      $masks.each(function(index) {
        TweenMax.fromTo(this, 0.7, {
          height: 390
        }, {
          height: 0,
          ease: Sine.easeIn,
          delay: ((index / 3) + (index % 3)) * 0.15
        });
      });
    }
  }



  function ShopsAnimation() {
    var $container = $('.index_shops');
    var $shops = $container.find('.index_shops_pc_item');
    var $shopsInner = $shops.find('.index_shops_pc_item_inner');
    var $shopSvgs = $shops.find('svg');
    var $shopTitles = $shops.find('img');
    var $shopImages = $shops.find('svg image');

    var isPlayed;
    var threshold;
    var isActive;

    $container.on('click', function(e) {
      return;
      e.preventDefault();
      e.stopPropagation();
      isPlayed = false;
      activate();
      play();
      deactivate();
    });

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      $window.on({
        scroll: onScroll
      });

      if ( isPlayed ) {
        return;
      }

      isActive = true;
      $shops.filter(':even').css({
        overflow: 'hidden',
        width: 0
      });
      $shopsInner.filter(':odd').css({
        overflow: 'hidden',
        width: 0
      });

      $window.on({
        resize: onResize,
        scroll: onScroll
      });

      onResize();
    }


    function deactivate() {
      isActive = false;

      $window.off({
        resize: onResize,
        scroll: onScroll
      });
    }


    function onResize() {
      if ( !isActive ) {
        return;
      }

      threshold = $shops.offset().top - window.innerHeight * 0.7;
      onScroll();
    }


    function onScroll(e) {
      $shopImages.each(function(index) {
        TweenMax.set(this, {
          y: (window.pageYOffset - (threshold + index * 329 + window.innerHeight / 2)) * 0.2
        });
      });

      if ( isPlayed || window.pageYOffset < threshold ) {
        return;
      }

      play();
    }


    function play() {
      isPlayed  = true;

      $shopSvgs.css({
        width: winWidth
      });
      TweenMax.delayedCall($shops.length * 0.5 + 1, function() {
        $shopSvgs.css({
          width: '100%'
        });
      });

      TweenMax.staggerFromTo($shops.filter(':even'), 1, {
        overflow: 'hidden',
        width: 0
      }, {
        width: '100%'
      }, 1);
      TweenMax.staggerFromTo($shopImages.filter(':even'), 1, {
        x: -100
      }, {
        x: 0
      }, 1);
      TweenMax.staggerFromTo($shopTitles.filter(':even'), 1, {
        x: -20,
        y: '-50%'
      }, {
        x: 0
      }, 1);

      $shopsInner.filter(':odd').css({
        position: 'absolute',
        right: 0,
        height: '100%'
      });
      $shopSvgs.filter(':odd').css({
        position: 'absolute',
        right: 0
      });

      TweenMax.staggerFromTo($shopsInner.filter(':odd'), 1, {
        overflow: 'hidden',
        width: 0,
        right: 0
      }, {
        width: '100%',
        delay: 0.5
      }, 1);
      TweenMax.staggerFromTo($shopImages.filter(':odd'), 1, {
        x: 100
      }, {
        x: 0,
        delay: 0.5
      }, 1);
      TweenMax.staggerFromTo($shopTitles.filter(':odd'), 1, {
        x: 20,
        y: '-50%'
      }, {
        x: 0,
        delay: 0.5
      }, 1);
    }
  }



  function getTitleAnimation($title) {
    var result = new TimelineMax({
      paused: true
    });

    result.add(TweenMax.fromTo($title, 0.3, {
      opacity: 0
    }, {
      opacity: 1
    }), 0);
    result.add(TweenMax.fromTo($title, 0.45, {
      scale: 0.8
    }, {
      scale: 1,
      ease: Back.easeOut
    }), 0);

    return result;
  }



  // 浠ヤ笅SP瀹熻
  function ResponsiveSp() {
    var whats = SpWhats();
    var topAnimation = TopAnimationSp();

    return {
      open: open,
      close: close
    };


    function open() {
      whats.activate();
      topAnimation.activate();
      initFooterShopsLink();
      $window.on('scroll', onScroll);
    }


    function close() {
      var $deferred = $.Deferred();

      whats.deactivate();
      topAnimation.deactivate();
      $window.off('scroll', onScroll);

      return $deferred.resolve();
    }


    function onScroll() {
      $window.off('scroll', onScroll);
      $('.nav_buttons').addClass('-shrink');
      setTimeout(function() {
        $('.nav_buttons').addClass('-shrinked');
      }, 250);
    }
  }


  function TopAnimationSp() {
    var $container = $('.index_top');
    var $bgs = $container.find('.index_top_bgs > div');
    var currentBgIndex = -1;
    var isActive;
    var showNextTimer;

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      isActive = true;
      TweenMax.killTweensOf($bgs);

      showNextImage();
    }


    function deactivate() {
      isActive = false;
      clearTimeout(showNextTimer);
    }


    function showNextImage() {
      if ( !isActive ) {
        return;
      }

      currentBgIndex++;
      if ( currentBgIndex >= $bgs.length ) {
        currentBgIndex = 0;
      }
      var $current = $bgs.eq(currentBgIndex);

      TweenMax.fromTo($current, 0.7, {
        opacity: 0
      }, {
        opacity: 1
      });

      TweenMax.fromTo($current, 5, {
        scale: 1.2,
        zIndex: 1,
        display: 'block'
      }, {
        scale: 1,
        ease: Sine.easeOut
      });

      clearTimeout(showNextTimer);
      showNextTimer = setTimeout(function() {
        $bgs.not($current).hide();
        $current.css({
          zIndex: 0
        });
        showNextImage();
      }, 4500);
    }
  }



  function SpWhats() {
    var $container = $('.index_about_points');
    var $titles = $container.find('.js-zoom');
    var $contents = $container.find('dd');
    var $openContent;

    return {
      activate: activate,
      deactivate: deactivate
    };


    function activate() {
      $titles.on('click', onTitleClick);
    }


    function deactivate() {
      $titles.off('click', onTitleClick);

      if ( $openContent ) {
        $openContent.hide();
      }
    }


    function onTitleClick(e) {
      e.preventDefault();

      $openContent = $(this).next();
      show($openContent);

      setTimeout(function() {
        $body.on('click', onWindowClick);
        $titles.off('click', onTitleClick);
      }, 100);
    }


    function onWindowClick(e) {
      hide($openContent);
      $openContent = null;

      $body.off('click', onWindowClick);
      $titles.on('click', onTitleClick);
    }


    function show($content) {
      TweenMax.fromTo($content, 0.35, {
        display: 'block',
        scale: 0.5,
        opacity: 0
      }, {
        scale: 1,
        opacity: 1,
        ease: Back.easeOut
      });
    }


    function hide($content) {
      TweenMax.to($content, 0.1, {
        display: 'none',
        scale: 0.8,
        opacity: 0
      });
    }
  }


  function initFooterShopsLink() {
    var pathForOdd = '';
    var pathForEven = '';

    $('.index_shops_links').each(function() {
    });
  }


  function addIndirectHovers() {
    $('[data-hover-for]').each(function() {
      addIndirectHover(this);
    });
  }


  function addIndirectHover(item) {
    var $item = $(item);
    var selector = $item.attr('data-hover-for');
    var $target = $(selector);

    $item.on({
      mouseenter: onMouseOver,
      mouseleave: onMouseOut
    });
    return;


    function onMouseOver() {
      $target.addClass('hover');
    }


    function onMouseOut() {
      $target.removeClass('hover');
    }
  }

}());