;
(function (b, c) {
  var $ = b.jQuery || b.Cowboy || (b.Cowboy = {}), a;
  $.throttle = a = function (e, f, j, i) {
    var h, d = 0;
    if (typeof f !== "boolean") {
      i = j;
      j = f;
      f = c
    }
    function g() {
      var o = this, m = +new Date() - d, n = arguments;
      function l() {
        d = +new Date();
        j.apply(o, n)
      }
      function k() {
        h = c
      }
      if (i && !h) {
        l()
      }
      h && clearTimeout(h);
      if (i === c && m > e) {
        l()
      } else {
        if (f !== true) {
          h = setTimeout(i ? k : l, i === c ? e - m : e)
        }
      }
    }
    if ($.guid) {
      g.guid = j.guid = j.guid || $.guid++
    }
    return g
  };
  $.debounce = function (d, e, f) {
    return f === c ? a(d, e, false) : a(d, f, e !== false)
  }
})(this);
(function ($) {
  'use strict';
  function transitionEnd() {
    var el = document.createElement('quadmenu')

    var transEndEventNames = {
      WebkitTransition: 'webkitTransitionEnd',
      MozTransition: 'transitionend',
      OTransition: 'oTransitionEnd otransitionend',
      transition: 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }


  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('quadmenuTransitionEnd', function () {
      called = true
    })
    var callback = function () {
      if (!called)
        $($el).trigger($.support.transition.end)
    }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition)
      return

    $.event.special.quadmenuTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this))
          return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

  var QuadMenuCollapse = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, QuadMenuCollapse.DEFAULTS, options)
    this.$trigger = $('[data-quadmenu="collapse"][href="#' + element.id + '"],' +
      '[data-quadmenu="collapse"][data-target="#' + element.id + '"]')
    this.transitioning = null

    if (this.options.parent) {
      this.$parent = this.getParent()
    } else {
      this.addAriaAndQuadMenuCollapsedClass(this.$element, this.$trigger)
    }

    if (this.options.toggle)
      this.toggle()
  }

  QuadMenuCollapse.TRANSITION_DURATION = 350

  QuadMenuCollapse.DEFAULTS = {
    toggle: true
  }

  QuadMenuCollapse.prototype.dimension = function () {
    var hasWidth = this.$element.hasClass('width')
    return hasWidth ? 'width' : 'height'
  }

  QuadMenuCollapse.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('in'))
      return

    var activesData
    var actives = this.$parent && this.$parent.children('.panel').children('.in, .collapsing')

    if (actives && actives.length) {
      activesData = actives.data('quadmenu.collapse')
      if (activesData && activesData.transitioning)
        return
    }

    var startEvent = $.Event('show.quadmenu.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented())
      return

    if (actives && actives.length) {
      Plugin.call(actives, 'hide')
      activesData || actives.data('quadmenu.collapse', null)
    }

    var dimension = this.dimension()

    this.$element
      .removeClass('collapse')
      .addClass('collapsing')[dimension](0)
      .attr('aria-expanded', true)

    this.$trigger
      .removeClass('collapsed')
      .attr('aria-expanded', true)

    this.transitioning = 1

    var complete = function () {
      this.$element
        .removeClass('collapsing')
        .addClass('collapse in')[dimension]('')
      this.transitioning = 0
      this.$element
        .trigger('shown.quadmenu.collapse')
    }

    if (!$.support.transition)
      return complete.call(this)

    var scrollSize = $.camelCase(['scroll', dimension].join('-'))

    this.$element
      .one('quadmenuTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(QuadMenuCollapse.TRANSITION_DURATION)[dimension](this.$element[0][scrollSize])
  }

  QuadMenuCollapse.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('in'))
      return

    var startEvent = $.Event('hide.quadmenu.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented())
      return

    var dimension = this.dimension()

    this.$element[dimension](this.$element[dimension]())[0].offsetHeight

    this.$element
      .addClass('collapsing')
      .removeClass('collapse in')
      .attr('aria-expanded', false)

    this.$trigger
      .addClass('collapsed')
      .attr('aria-expanded', false)

    this.transitioning = 1

    var complete = function () {
      this.transitioning = 0
      this.$element
        .removeClass('collapsing')
        .addClass('collapse')
        .trigger('hidden.quadmenu.collapse')
    }

    if (!$.support.transition)
      return complete.call(this)

    this.$element
    [dimension](0)
      .one('quadmenuTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(QuadMenuCollapse.TRANSITION_DURATION)
  }

  QuadMenuCollapse.prototype.toggle = function () {
    this[this.$element.hasClass('in') ? 'hide' : 'show']()
  }

  QuadMenuCollapse.prototype.getParent = function () {
    return $(this.options.parent)
      .find('[data-quadmenu="collapse"][data-parent="' + this.options.parent + '"]')
      .each($.proxy(function (i, element) {
        var $element = $(element)
        this.addAriaAndQuadMenuCollapsedClass(getTargetFromTrigger($element), $element)
      }, this))
      .end()
  }

  QuadMenuCollapse.prototype.addAriaAndQuadMenuCollapsedClass = function ($element, $trigger) {
    var isOpen = $element.hasClass('in')

    $element.attr('aria-expanded', isOpen)
    $trigger
      .toggleClass('collapsed', !isOpen)
      .attr('aria-expanded', isOpen)
  }

  function getTargetFromTrigger($trigger) {
    var href
    var target = $trigger.attr('data-target')
      || (href = $trigger.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7

    return $(target)
  }


  // COLLAPSE PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data = $this.data('quadmenu.collapse')
      var options = $.extend({}, QuadMenuCollapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data && options.toggle && /show|hide/.test(option))
        options.toggle = false
      if (!data)
        $this.data('quadmenu.collapse', (data = new QuadMenuCollapse(this, options)))
      if (typeof option == 'string')
        data[option]()
    })
  }

  var old = $.fn.collapse

  $.fn.collapse = Plugin
  $.fn.collapse.Constructor = QuadMenuCollapse


  // COLLAPSE NO CONFLICT
  // ====================

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


  // COLLAPSE DATA-API
  // =================

  $(document).on('click.quadmenu.collapse.data-api', '[data-quadmenu="collapse"]', function (e) {
    var $this = $(this)

    if (!$this.attr('data-target'))
      e.preventDefault()

    var $target = getTargetFromTrigger($this)
    var data = $target.data('quadmenu.collapse')
    var option = data ? 'toggle' : $this.data()

    Plugin.call($target, option)
  })

}(jQuery));
(function ($, window, document, undefined) {
  'use strict';
  var defaults = {
    responsive: true,
    containerGutter: parseInt(quadmenu.gutter),
    touchEvents: true,
    mouseEvents: true,
    moveThreshold: 50,
    //intent_sensitivity: 15,
    intent_timeout: 300,
    intent_interval: 30,
  };
  function Plugin(element, options) {
    var plugin = this;
    this.element = element;
    this.$quadmenu = $(this.element);
    this.$ul = this.$quadmenu.find('ul.quadmenu-navbar-nav');
    this.settings = $.extend({}, defaults, options);
    this.touchenabled = ('ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0);
    this.mobiledevice = (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
    this.perfectScrollbar = (typeof $.fn.perfectScrollbar !== 'undefined' && !this.mobiledevice) ? true : false;
    if (this.touchenabled) {
      this.$quadmenu.addClass('quadmenu-touch');
    } else {
      this.$quadmenu.addClass('quadmenu-notouch');
    }

    if (window.navigator.pointerEnabled) {
      this.touchStart = 'pointerdown';
      this.touchEnd = 'pointerup';
      this.touchMove = 'pointermove';
    } else if (window.navigator.msPointerEnabled) {
      this.touchStart = 'MSPointerDown';
      this.touchEnd = 'MSPointerUp';
      this.touchMove = 'MSPointerMove';
    } else {
      this.touchStart = 'touchstart';
      this.touchEnd = 'touchend';
      this.touchMove = 'touchmove';
    }

    this.init();
  }

  Plugin.prototype = {
    init: function () {
      this.quadmenuResolveConflics();
      this.quadmenuInitClasses();
      this.quadmenuInitWidth();
      this.quadmenuInitContainerWidth();
      this.quadmenuInitLazyLoad();
      //this.quadmenuInitNavbarVertical();
      this.quadmenuInitNavbarSticky(); // horizontal
      this.quadmenuInitNavbarOffcanvas();
      this.quadmenuInitNavbarSlideBar(); // horizontal
      this.quadmenuInitItemTabs(); // horizontal
      this.quadmenuInitItemCarousel();
      this.quadmenuInitItemLogIn();
      this.quadmenuInitItemSocial();
      this.quadmenuInitItemCart();
      this.quadmenuInitItemWidgets();
      this.quadmenuInitDropdownFloat(); // horizontal
      this.quadmenuInitDropdownMaxHeight(); // horizontal
      this.quadmenuInitDropdownTouchEvents();
      this.quadmenuInitDropdownMouseEvents();
      this.quadmenuInitDropdownRetractors();
      this.quadmenuInitDropdownCloseAll(); // horizontal
      this.quadmenuInitScrollBar(); // horizontal
      this.quadmenuInit();
    },
    quadmenuInit: function () {

      this.$quadmenu.trigger('init.quadmenu');

    },
    quadmenuResolveConflics: function () {

      if (this.$quadmenu.data('unwrap')) {

        this.$quadmenu.unwrap('nav').find('.quadmenu-item, .quadmenu-dropdown-toggle, .quadmenu-dropdown-menu, .quadmenu-dropdown-submenu').add(this.$quadmenu).removeAttr('style').unbind().off();
      }

    },
    quadmenuInitClasses: function (plugin) {

      plugin = plugin || this;
      plugin.handleClasses();
      $(window).on('resize', $.debounce(300, function () {
        plugin.handleClasses();
      }));
    },
    handleClasses: function () {

      var template = this.$quadmenu.data('template'),
        mobile = this.settings.responsive && (window.innerWidth <= this.$quadmenu.data('breakpoint')) ? true : false;

      if (!mobile && template == 'collapse') {
        this.$quadmenu.addClass('quadmenu-is-horizontal');
      }

      if (!mobile && template == 'embed') {
        this.$quadmenu.addClass('quadmenu-is-horizontal');
      }

      if (!mobile && template == 'offcanvas') {
        this.$quadmenu.addClass('quadmenu-is-horizontal');
      }

      if (mobile) {
        this.$quadmenu.removeClass('quadmenu-is-horizontal');
      }

      this.$quadmenu.removeClass('no-js').addClass('js');

    },
    quadmenuInitDropdownTouchEvents: function (plugin) {

      plugin = plugin || this;
      if (!this.settings.touchEvents)
        return;
      this.$ul.on(this.touchStart + '.quadmenu.toggle', '.quadmenu-dropdown > .quadmenu-dropdown-toggle', function (e) {
        plugin.handleTouchEvents(e, this, plugin);
      });
      this.$ul.on('click', '.quadmenu-dropdown > .quadmenu-dropdown-toggle', function (e) {
        plugin.handleClicks(e, this);
      });
    },
    quadmenuInitDropdownMouseEvents: function (plugin) {

      plugin = plugin || this;
      plugin.handleDropdownMouseEvents();
      $(window).on('resize', $.debounce(600, function () {
        plugin.handleDropdownMouseEvents();
      }));
    },
    handleDropdownMouseEvents: function (plugin) {

      plugin = plugin || this;
      this.$ul.find('.quadmenu-item').off('mouseleave.hoverIntent mouseenter.hoverIntent mousemove.hoverIntent');
      this.$ul.find('.quadmenu-item').removeProp('hoverIntent_t');
      this.$ul.find('.quadmenu-item').removeProp('hoverIntent_s');
      if (!this.settings.mouseEvents)
        return;
      this.$ul.on('click', '.quadmenu-item > .quadmenu-dropdown-toggle', function (e) {
        plugin.handleLink(e, this);
      });
      this.$ul.on('click.quadmenu.toggle', '.quadmenu-item > .quadmenu-dropdown-toggle', function (e) {
        plugin.handleMouseClick(e, this, plugin);
      });
      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal') || typeof $.fn.hoverIntent === 'undefined')
        return;
      plugin.handleMouseHover(this.$ul.find('.quadmenu-item > .quadmenu-dropdown-toggle.hoverintent'), plugin);
    },
    handleMouseHover: function (target, plugin) {
      var $target = $(target),
        $li = $target.parent('.quadmenu-item');
      $target.off('click.quadmenu.toggle');
      $target.off(this.touchStart + '.quadmenu.toggle');
      if (!$li.length)
        return;
      $li.hoverIntent({
        over: function () {

          var $li = $(this),
            $target = $li.find('> .quadmenu-dropdown-menu');
          if ($target.data('quadmenu-killHover'))
            return;
          plugin.triggerSubmenu($li, plugin);
        },
        out: function () {

          var $li = $(this),
            $target = $li.find('> .quadmenu-dropdown-menu');
          if ($target.data('quadmenu-killHover'))
            return;
          if ($li.hasClass('quadmenu-item-type-tab'))
            return;
          plugin.closeSubmenu($li);
        },
        //sensitivity: plugin.settings.intent_sensitivity,
        timeout: plugin.settings.intent_timeout,
        interval: plugin.settings.intent_interval
      });
    },
    quadmenuInitDropdownRetractors: function (plugin) {

      plugin = plugin || this;
      this.$ul.on('click.retractor', '.quadmenu-item.quadmenu-dropdown.open > .quadmenu-dropdown-toggle > .quadmenu-item-content > .quadmenu-caret', function (e) {
        plugin.handleDropdownCloseEnd(e, this, plugin);
      });
      // we dont need to off this evet on window resize because touch events hare handled by the browser
      if (!this.settings.touchEvents)
        return;
      this.$ul.on(this.touchStart + '.retractor', '.quadmenu-item.quadmenu-dropdown.open > .quadmenu-dropdown-toggle > .quadmenu-item-content > .quadmenu-caret', function (e) {
        plugin.handleDropdownCloseStart(e, this, plugin);
      });
    },
    handleTouchEvents: function (e, target, plugin) {

      var $target = $(target);
      //disable hoverintent on touch devices
      //$li.off('mouseleave.hoverIntent');
      $target.on(plugin.touchEnd, function (e) {
        plugin.handleTouchTap(e, this, plugin);
      });
      $target.on(plugin.touchMove, function (e) {
        plugin.preventTapOnScroll(e, this, plugin);
      });
      if (e.originalEvent !== undefined) {

        if (e.originalEvent.touches) {
          $target.data('quadmenu-startX', e.originalEvent.touches[0].clientX);
          $target.data('quadmenu-startY', e.originalEvent.touches[0].clientY);
        }
        //microsoft
        else if (e.originalEvent.clientY) {
          var pos = $target.offset();
          $target.data('quadmenu-startX', e.originalEvent.clientX);
          $target.data('quadmenu-startY', e.originalEvent.clientY);
        }

      }

    },
    preventTapOnScroll: function (e, target, plugin) {

      var $target = $(target);
      if (e.originalEvent !== undefined) {

        //make sure the touch points aren't too close, if so, then remove the event handlers
        if (e.originalEvent.touches) {
          if (Math.abs(e.originalEvent.touches[0].clientX - $target.data('quadmenu-startX')) > plugin.settings.moveThreshold ||
            Math.abs(e.originalEvent.touches[0].clientY - $target.data('quadmenu-startY')) > plugin.settings.moveThreshold) {

            plugin.resetHandlers($target);
          }
        } else if (e.originalEvent.clientY) {
          var pos = $target.data(pos);
          if (Math.abs(e.originalEvent.clientX - $target.data('quadmenu-startX')) > plugin.settings.moveThreshold ||
            Math.abs(e.originalEvent.clientY - $target.data('quadmenu-startY')) > plugin.settings.moveThreshold) {

            plugin.resetHandlers($target);
          }
        }

      }

    },
    handleTouchTap: function (e, target, plugin) {
      e.preventDefault();

      var $target = $(target),
        $li = $target.parent();
      //prevent clicks
      $target.data('quadmenu-killClick', true);
      //prevent hover
      $target.data('quadmenu-killHover', true);
      setTimeout(function () {
        $target.data('quadmenu-killClick', false).data('quadmenu-killHover', false);
      }, 1000);
      //close other submenus
      if (this.$quadmenu.hasClass('quadmenu-is-horizontal')) {
        plugin.closeSubmenu($li.siblings('.open'));
      }

      if ($li.hasClass('quadmenu-dropdown')) {
        //if submenu is already open then close it
        if ($li.hasClass('open')) {
          //please don't close tabs
          if (!$li.hasClass('quadmenu-item-type-tab') || !this.$quadmenu.hasClass('quadmenu-is-horizontal')) {
            plugin.closeSubmenu($li);
          }
          //allow link to be followed
          plugin.handleLink(e, target, true);
        }
        //if submenu is closed then open the submenu and prevent link from being followed
        else {
          plugin.openSubmenu($li);
        }
      }
      //allow links whitout submenus to be followed
      else {
        plugin.handleLink(e, target, true);
      }

      plugin.resetHandlers($target);
    },
    handleLink: function (e, link, follow) {

      follow = follow || false;
      var $link = $(link),
        href = $link.attr('href');
      if (!$link.is('a'))
        return;
      if (!href) {
        e.preventDefault();
        return;
      }

      if (!follow || !e.isDefaultPrevented())
        return;
      //handle links after preventDefault
      if ($link.attr('target') === '_blank') {
        window.open(href, '_blank');
      } else {
        window.location = href;
      }

    },
    handleMouseClick: function (e, target, plugin) {

      var $target = $(target),
        $li = $target.parent('.quadmenu-item');
      if ($target.data('quadmenu-killClick') || !$li.length)
        return;
      //disable hoverintent on click, just in case
      //$li.off('mousemove.hoverIntent');
      //$li.off('mouseenter.hoverIntent');
      //$li.off('mouseleave.hoverIntent');
      if ($li.hasClass('open')) {

        if ($target.is('a')) {
          plugin.handleLink(e, target);
        }

      } else if ($li.hasClass('quadmenu-dropdown')) {
        e.preventDefault();
        //close other submenus
        if (this.$quadmenu.hasClass('quadmenu-is-horizontal')) {
          plugin.closeSubmenu($li.siblings('.open'));
        }
        plugin.openSubmenu($li);
      }

    },
    handleDropdownCloseStart: function (e, caret, plugin) {
      e.preventDefault();
      //e.stopPropagation(); // prevent envent on url
      $(caret).on(plugin.touchEnd, function (e) {
        plugin.handleDropdownCloseEnd(e, this, plugin);
      });
    },
    handleDropdownCloseEnd: function (e, caret, plugin) {
      e.preventDefault();
      e.stopPropagation(); // prevent envent on url
      var $li = $(caret).closest('.quadmenu-dropdown.open');
      plugin.closeSubmenu($li);
      $(caret).off(plugin.touchEnd);
      return false;
    },
    resetHandlers: function ($target) {

      $target.off(this.touchEnd);
      $target.off(this.touchMove);
      var $li = $target.parent();
      //1326 error en touch se desactiva hoverintent definitvamente
      $li.off('mousemove.hoverIntent');
      $li.off('mouseenter.hoverIntent');
      $li.off('mouseleave.hoverIntent');
      $li.removeProp('hoverIntent_t');
      $li.removeProp('hoverIntent_s');
    },
    triggerSubmenu: function ($li, plugin) {
      plugin.closeSubmenu($li.siblings('.open'));
      plugin.openSubmenu($li);
    },
    openSubmenu: function ($li, timeout) {

      if ($li.hasClass('open'))
        return;
      timeout = timeout || 100;
      $li.trigger('show.quadmenu.dropdown');
      $li.addClass('opening');
      setTimeout(function () {
        $li.addClass('open');
        $li.removeClass('opening');
        $li.trigger('shown.quadmenu.dropdown');
      }, timeout);
    },
    closeSubmenu: function ($li, timeout) {

      if (!$li.hasClass('open'))
        return;
      timeout = timeout || 200;
      $li.trigger('hide.quadmenu.dropdown');
      $li.addClass('closing');
      setTimeout(function () {
        $li.find('.quadmenu-item').removeClass('open');
        $li.removeClass('open').removeClass('closing');
        $li.trigger('hidden.quadmenu.dropdown');
      }, timeout);
    },
    handleClicks: function (e, target) {

      var $target = $(target);
      if ($target.data('quadmenu-killClick')) {
        e.preventDefault();
      }
    },
    quadmenuInitDropdownCloseAll: function (plugin) {

      plugin = plugin || this;
      /*
       * resize is calling in mobile touch taps and closing all menus
       * $(window).on('resize', $.debounce(300, function () {
       plugin.closeAllSubmenus();
       }));*/

      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      $(document).on(this.touchEnd + '.hidden.quadmenu.dropdown.all click.hidden.quadmenu.dropdown.all', function (e) {

        if ($(e.target).closest('#quadmenu').length)
          return;
        plugin.closeAllSubmenus();
      });
    },
    closeAllSubmenus: function () {

      var $li = this.$ul.find('.quadmenu-item.open');
      if (!$li.length)
        return;
      this.closeSubmenu($li, 100);
      //this.trigger('hidden.quadmenu.dropdown.all');

      return;
    },
    quadmenuInitDropdownMaxHeight: function (plugin) {

      plugin = plugin || this;
      this.$ul.off('shown.quadmenu.dropdown.height');
      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      this.$ul.on('shown.quadmenu.dropdown.height', '.dropdown-maxheight', function (e) {
        plugin.handleDropdownMaxHeight($(this));
      });
      this.$ul.on('shown.quadmenu.dropdown.tabheight', '.dropdown-maxheight.quadmenu-item-type-tab', function (e) {
        plugin.handleTabsHeight($(this));
      });
      $(window).on('resize', $.debounce(300, function () {
        plugin.$ul.find('.dropdown-maxheight > .quadmenu-dropdown-menu > ul').css({ 'height': '', 'overflow-y': '' }).removeData('quadmenu-dropdownHeight').removeData('quadmenu-maxHeight');
      }));
    },
    handleDropdownMaxHeight: function (li, minHeight) {

      if (typeof $.fn.scrollTop === 'undefined')
        return;
      minHeight = minHeight || 200;
      var $li = $(li),
        $dropdown = $li.find('> .quadmenu-dropdown-menu > ul');
      if (!$dropdown.length)
        return;

      var dropdownHeight = $dropdown.removeAttr('style').outerHeight(),
        offsetTop = this.getElementOffset($dropdown),
        scrollTop = $(window).scrollTop(),
        currentTop = Math.max(0, offsetTop - scrollTop),
        currentBottom = $(window).height() - currentTop - 15,
        maxHeight = Math.min(dropdownHeight, currentBottom),
        Height = parseInt(Math.max(maxHeight, minHeight));
      $dropdown.css({ 'height': Height + 'px', 'overflow-y': 'auto' }).data('quadmenu-maxHeight', Height);

      return Height;
    },
    //quadmenuInitItemActive: function () {
    //    this.$ul.find('.current-menu-item:not(.quadmenu-item-type-tab)').addClass('active').first().parents('.quadmenu-item:not(.quadmenu-item-type-tab)').addClass('active');
    //},
    quadmenuInitNavbarSticky: function () {

      var plugin = this;

      if (typeof $.fn.scrollTop === 'undefined')
        return;

      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;

      plugin.$sticky = this.$quadmenu.filter('[data-sticky="1"]').first();

      if (!plugin.$sticky.length)
        return;

      plugin.is_sticky = false;

      $(window).on('load', function () {

        var topLast = 0;

        plugin.sticky_height = plugin.$sticky.height();

        plugin.adminbar_height = $('#wpadminbar').height() || 0;

        plugin.sticky_offset = plugin.$sticky.offset().top;

        plugin.topYSticky = Math.max(plugin.$sticky.offset().top - plugin.adminbar_height, plugin.$sticky.data('sticky-offset'), plugin.sticky_height + plugin.adminbar_height);

        if (plugin.topYSticky > plugin.sticky_offset) {
          plugin.$sticky.addClass('quadmenu-sticky-animation');
        }

        $(window).on('scroll', function () {

          var topY = $(this).scrollTop();

          if (plugin.is_sticky && (topY < (plugin.topYSticky))) {
            plugin.handleUnSticky();
          }

          if (!plugin.is_sticky && (topY > (plugin.topYSticky))) {
            plugin.handleSticky();
          }

          topLast = topY;
        });

      });


    },
    handleSticky: function () {

      var plugin = this;
      plugin.is_sticky = true;
      var placeholder = $('<div />').addClass('quadmenu-sticky-wrapper').css({
        'height': plugin.sticky_height + 'px',
        'position': 'static'
      });
      plugin.$sticky.find('.quadmenu-navbar-collapse.collapse.in').collapse('hide');
      plugin.$sticky.toggleClass('quadmenu-sticky-top').wrap(placeholder);
      plugin.$sticky.trigger('sticking.quadmenu.navbar');
      //setTimeout(function () {
      plugin.$sticky.trigger('sticky.quadmenu.navbar');
      //}, 200);
    },
    handleUnSticking: function () {

      var plugin = this;
      plugin.is_sticky = false;
      plugin.$sticky.addClass('quadmenu-unsticking-top');
    },
    handleUnSticky: function () {

      var plugin = this;
      plugin.is_sticky = false;
      plugin.$sticky.trigger('unsticking.quadmenu.navbar');
      //plugin.$sticky.addClass('quadmenu-unsticking-top');
      setTimeout(function () {
        //plugin.$sticky.removeClass('quadmenu-unsticking-top');
      }, 200);
      plugin.$sticky.removeClass('quadmenu-sticky-top');
      plugin.$sticky.unwrap();
      plugin.$sticky.trigger('unsticky.quadmenu.navbar');
    },
    quadmenuInitDropdownFloat: function (plugin) {

      plugin = plugin || this;
      plugin.handleDropdownFloat();
      $(window).on('resize', $.debounce(600, function () {
        plugin.handleDropdownFloat();
      }));
    },
    handleDropdownFloat: function (plugin) {

      plugin = plugin || this;
      this.$ul.off('shown.quadmenu.dropdown.float');
      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      this.$ul.on('shown.quadmenu.dropdown.float', '.quadmenu-item.quadmenu-dropdown:not(.quadmenu-item-type-tab):not(.quadmenu-item-type-tabs)', function (e) {
        var $dropdown = $(this).find('> .quadmenu-dropdown-menu:not(.quadmenu-dropdown-stretch-content):not(.quadmenu-dropdown-stretch-dropdown)');
        if (!$dropdown.length)
          return;
        var elH = $dropdown.outerWidth(),
          W = $(window).innerWidth(),
          p = $dropdown.offset(),
          l = p.left,
          r = parseInt(W - (l + elH));

        if ($(this).hasClass('quadmenu-dropdown-left') && l < 0) {
          $dropdown.css({ 'margin-right': l - plugin.settings.containerGutter + 'px' });
        } else if (r < 0) {
          $dropdown.css({ 'margin-left': r - plugin.settings.containerGutter + 'px' });
        }

      });
    },
    quadmenuInitWidth: function (plugin) {

      plugin = plugin || this;
      if (!plugin.$quadmenu.data('width'))
        return;
      plugin.handleFullWidth(plugin.$quadmenu);
      $(window).on('resize', $.debounce(600, function () {
        plugin.handleFullWidth(plugin.$quadmenu);
      }));
    },
    handleFullWidth: function (navbar) {

      var $navbar = $(navbar);
      $navbar.css({
        position: '',
        'box-sizing': '',
        left: '',
        right: '',
        width: ''
      });
      var margin_left = parseInt($navbar.css('margin-left'), 10),
        offset = 0 - $navbar.offset().left - margin_left,
        width = $(window).width();
      $navbar.css({
        position: 'relative',
        'box-sizing': 'border-box',
        left: offset,
        right: offset,
        width: width
      });
    },
    quadmenuInitContainerWidth: function (plugin) {

      plugin = plugin || this;
      plugin.handleContainerWidth(plugin.$quadmenu);
      this.$quadmenu.on('sticking.quadmenu.navbar unsticking.quadmenu.navbar', function () {
        plugin.handleContainerWidth($(this));
      });
      $(window).on('resize', $.debounce(600, function () {
        plugin.handleContainerWidth(plugin.$quadmenu);
      }));
    },
    handleContainerWidth: function (navbar) {

      var $navbar = $(navbar),
        $container = $navbar.find('.quadmenu-container');
      if (!$navbar.data('selector'))
        return;
      $container.css({ 'width': $(this.$quadmenu.data('selector')).innerWidth() + 'px' });
    },
    quadmenuInitLazyLoad: function (plugin) {

      plugin = plugin || this;

      /*this.$quadmenu.on('show.quadmenu.collapse show.quadmenu.dropdown', function (e) {
       
       $(this).find('img[data-src]').each(function (e) {
       
       var $img = $(this),
       width = $img.innerWidth(),
       height = $img.attr('height') || width,
       ratio = width / height,
       loaded = $img.data('lazyload');
       
       if (!loaded && ratio != 1) {
       $img.css({'width': width + 'px', 'height': (width / ratio) + 'px'});
       }
       
       });
       
       });*/

      this.$quadmenu.on('init.quadmenu shown.quadmenu.collapse shown.quadmenu.dropdown', function (e) {

        $(this).find('img[data-src]:visible').each(function (e) {

          var $img = $(this),
            src = $img.data('src'),
            srcset = $img.data('srcset'),
            loaded = $img.data('lazyload');

          if (!loaded) {

            $img.addClass('quadmenu-lazyload');

            if (src) {
              $img.attr('src', src).removeAttr('data-src').data('lazyload', true);
            }

            if (srcset) {
              $img.attr('srcset', srcset).removeAttr('data-srcset').data('lazyload', true);
            }

          }

        });

      });
    },
    /*quadmenuInitNavbarVertical: function (plugin) {
     
     plugin = plugin || this;
     if (!this.$quadmenu.hasClass('quadmenu-is-vertical'))
     return;
     plugin.handleNavbarVertical();
     $(window).on('resize', $.debounce(600, function () {
     plugin.handleNavbarVertical();
     }));
     },
     handleNavbarVertical: function (plugin) {
     
     plugin = plugin || this;
     if (this.$quadmenu.find('.navbar-offcanvas').hasClass('in'))
     return;
     setTimeout(function () {
     plugin.closeAllSubmenus();
     plugin.$quadmenu.find('[data-quadmenu=offcanvas]').trigger('click');
     }, 500);
     },*/
    quadmenuInitNavbarOffcanvas: function (plugin) {

      plugin = plugin || this;

      this.$quadmenu.on('show.quadmenu.collapse shown.quadmenu.collapse hide.quadmenu.collapse hidden.quadmenu.collapse', '.navbar-offcanvas', function (e) {

        //var $offcanvas = $('.navbar-offcanvas', $(this));

        //if ($offcanvas.length) {

        var width = $(this).width(),
          translateX = plugin.$quadmenu.hasClass('quadmenu-offcanvas-left') ? width : width * -1;

        $(this).trigger(e.type + '.quadmenu.offcanvas', [translateX]);

        //}

      });

      this.$quadmenu.on('show.quadmenu.offcanvas', '.navbar-offcanvas', function (e, translateX) {

        var $transform = $('> .quadmenu-navbar-toggle', plugin.$quadmenu).add($('.quadmenu-navbar-header', plugin.$quadmenu));

        $transform.css({ 'transform': 'translateX(' + translateX + 'px)' });

        $(this).css({ 'transform': 'translateX(0)' });

        $('body').addClass('quadmenu-offcanvas-in');

        plugin.$quadmenu.addClass('quadmenu-is-vertical');
      });

      this.$quadmenu.on('hide.quadmenu.offcanvas', '.navbar-offcanvas', function (e) {

        var $transform = $('> .quadmenu-navbar-toggle', plugin.$quadmenu).add($('.quadmenu-navbar-header', plugin.$quadmenu));

        $transform.css({ 'transform': '' });

        $(this).removeAttr('style');

        $('body').removeClass('quadmenu-offcanvas-in');
      });

      this.$quadmenu.on('hidden.quadmenu.offcanvas', '.navbar-offcanvas', function (e) {

        plugin.$quadmenu.removeClass('quadmenu-is-vertical');
      });

      this.$quadmenu.on('shown.quadmenu.offcanvas hidden.quadmenu.offcanvas', '.navbar-offcanvas', function (e, translateX) {

        setTimeout(function () {

          var $transform = $(this).add($('> .quadmenu-navbar-toggle', plugin.$quadmenu)).add($('.quadmenu-navbar-header', plugin.$quadmenu));

          $transform.toggleClass('canvas-sliding');

        }, 1000);
      });

      $(document).on(this.touchStart + '.hide.quadmenu.offcanvas click.hide.quadmenu.offcanvas', function (e) {

        var $target = $(e.target),
          responsive = plugin.settings.responsive && (window.innerWidth <= plugin.$quadmenu.data('breakpoint')) ? true : false;
        if ($target.closest('#quadmenu').length)
          return;
        if (!responsive)
          return;
        $target.on(plugin.touchEnd, function (e) {
          $('.navbar-offcanvas').collapse('hide');
        });
        $target.on(plugin.touchMove, function (e) {
          plugin.preventTapOnScroll(e, this, plugin);
        });
        if (e.originalEvent !== undefined) {

          if (e.originalEvent.touches) {
            $target.data('quadmenu-startX', e.originalEvent.touches[0].clientX);
            $target.data('quadmenu-startY', e.originalEvent.touches[0].clientY);
          }
          //microsoft
          else if (e.originalEvent.clientY) {
            var pos = $target.offset();
            $target.data('quadmenu-startX', e.originalEvent.clientX);
            $target.data('quadmenu-startY', e.originalEvent.clientY);
          }
        }

      });
    },
    quadmenuInitScrollBar: function (plugin) {

      plugin = plugin || this;
      if (!this.perfectScrollbar)
        return;
      plugin.handleDropdownScrollbar();
      plugin.handleVerticalScrollbar();
      $(window).on('resize', $.debounce(300, function () {
        plugin.handleDropdownScrollbar();
        plugin.handleVerticalScrollbar();
      }));
    },
    handleDropdownScrollbar: function (plugin) {

      plugin = plugin || this;
      this.$ul.off('shown.quadmenu.dropdown.pscrollbar');
      this.$ul.find('.ps-container').perfectScrollbar('destroy').data('ps-id', false);
      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      this.$ul.on('shown.quadmenu.dropdown.pscrollbar', '.dropdown-maxheight', function (e) {
        plugin.$dropdown = $(this).find('> .quadmenu-dropdown-menu > ul');
        //fix for mousehover ----------------------
        plugin.$dropdown.on('scroll', $.debounce(500, true, function () {
          $(this).data('quadmenu-killHover', true);
        }));
        plugin.$dropdown.on('scroll', $.debounce(500, function () {
          $(this).removeData('quadmenu-killHover');
        }));
        if (plugin.$dropdown.data('ps-id')) {
          plugin.$dropdown.perfectScrollbar('update');
          return;
        }

        plugin.$dropdown.perfectScrollbar({
          useKeyboard: true,
          suppressScrollX: true,
          includePadding: true,
          scrollYMarginOffset: 1
        });
      });
    },
    handleVerticalScrollbar: function (plugin) {

      plugin = plugin || this;
      plugin.$offcanvas = this.$quadmenu.find('.navbar-offcanvas');
      plugin.$offcanvas.perfectScrollbar('destroy').data('ps-id', false);
      if (this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      plugin.$offcanvas.on('shown.quadmenu.dropdown.pscrollbar hidden.quadmenu.dropdown.pscrollbar shown.quadmenu.offcanvas.pscrollbar hidden.quadmenu.offcanvas.pscrollbar', function (e) {

        var $psMenu = $(this);
        $(this).on('mouseup mouseenter', '.ps-scrollbar-y-rail', function () {
          $psMenu.find('.quadmenu-item > .quadmenu-dropdown-toggle').data('quadmenu-killHover', true);
        });
        $(this).on('mouseleave', '.ps-scrollbar-y-rail', function () {
          $psMenu.find('.quadmenu-item > .quadmenu-dropdown-toggle').removeData('quadmenu-killHover');
        });
        $psMenu.perfectScrollbar('update');
      });
      if (plugin.$offcanvas.data('ps-id')) {
        plugin.$offcanvas.perfectScrollbar('update');
        return;
      }
      plugin.$offcanvas.perfectScrollbar({
        useKeyboard: true,
        suppressScrollX: true,
        includePadding: true,
        scrollYMarginOffset: 1
      });
    },
    quadmenuInitNavbarSlideBar: function (plugin) {

      plugin = plugin || this;

      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal') || !this.$quadmenu.hasClass('quadmenu-hover-slidebar'))
        return;

      setTimeout(function () {
        plugin.$ul.append('<li class="quadmenu-slidebar invisible"><span class="bar"></span></li>');
        plugin.handleSlideBar(plugin.$ul);
      }, 1000);
    },
    handleSlideBar: function (ul, plugin) {

      plugin = plugin || this;

      var $ul = $(ul),
        slide_nav = $ul.find('> li.quadmenu-slidebar'),
        $open = $ul.find('> li.open:visible'),
        $item = $ul.find('> li.current-menu-item:visible'),
        $ancestor = $ul.find('> li.current-menu-ancestor:visible'),
        $current = $ul.find('> li:visible:first-child');

      if ($ancestor.length) {
        $current = $ancestor;
      }
      if ($item.length) {
        $current = $item;
      }
      if ($open.length) {
        $current = $open;
      }

      function slide_nav_css($current) {

        if ($current.length) {

          var width = parseFloat($current.find('> a').outerWidth()),
            pos_left = parseFloat($current.position().left),
            pos_right = parseFloat($current.position().right);
          slide_nav.css({ 'width': width + 'px', 'left': pos_left + 'px', 'right': pos_right + 'px' }).removeClass('invisible');
        }

      }

      slide_nav_css($current);

      slide_nav.data('slidebar-style', slide_nav.attr('style'));

      //handle sticky transforms
      this.$ul.on('sticky.quadmenu.navbar.slidebar unsticky.quadmenu.navbar.slidebar', function () {
        slide_nav.addClass('invisible');
        var $ul = $(this).find('.quadmenu-navbar-nav'),
          $open = $ul.find('> ' + li + '.open'),
          $active = $open.length ? $open : $ul.find('> li.quadmenu-item.quadmenu-item-level-0.active'),
          $current = $active.length ? $active : $ul.find(li).not('.quadmenu-float-opposite').first();
        slide_nav_css($current);
        slide_nav.data('slidebar-style', slide_nav.attr('style'));
      });
      //handle mouse events
      $ul.find('> li.quadmenu-item.quadmenu-has-link').on('hover.slidebar', function (e) {
        slide_nav_css($(this));
      });

      $ul.find('> li.quadmenu-item.quadmenu-has-link').on('mouseleave.slidebar', function (e) {
        slide_nav.attr('style', slide_nav.data('slidebar-style')).show();
      });

      $ul.on('shown.quadmenu.dropdown.slidebar', '> li.quadmenu-item.quadmenu-has-link', function (e) {
        if ($(this).find('> a').hasClass('click')) {
          slide_nav_css($(this));
          slide_nav.data('slidebar-style', slide_nav.attr('style'));
        }
      });

      $ul.on('hide.quadmenu.dropdown.slidebar', '> li.quadmenu-item.quadmenu-has-link', function (e) {

        if ($(this).find('> a').hasClass('click')) {
          slide_nav_css($current);
          slide_nav.data('slidebar-style', slide_nav.attr('style'));
        }
      });
    },
    quadmenuInitItemTabs: function (plugin) {

      plugin = plugin || this;
      plugin.handleTabs();
      $(window).on('resize', $.debounce(600, function () {
        plugin.handleTabs();
      }));
    },
    handleTabs: function (plugin) {

      plugin = plugin || this;
      this.$ul.off('shown.quadmenu.dropdown.tabs', '.quadmenu-item-type-tabs');
      this.$ul.off('shown.quadmenu.dropdown.tabheight', '.quadmenu-item-type-tab');
      this.$ul.find('.quadmenu-item-type-tab > .quadmenu-dropdown-menu > ul').data('quadmenu-maxHeight', false).data('quadmenu-killHover', false);
      this.$ul.find('.quadmenu-item-type-tab > .quadmenu-dropdown-menu > ul').removeAttr('style');
      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      this.$ul.on('shown.quadmenu.dropdown.tabs', '.quadmenu-item-type-tabs', function (e) {

        if (!$(e.target).hasClass('quadmenu-item-type-tabs'))
          return;

        plugin.openSubmenu($(this).find('.quadmenu-item-type-tab:first'));


      });
    },
    handleTabsHeight: function (open) {

      var $open = $(open),
        $tabs = $open.closest('.quadmenu-dropdown-menu > ul');
      $tabs.css({ 'min-height': $open.find('> .quadmenu-dropdown-menu > ul').data('quadmenu-maxHeight') + 'px' });
    },
    getElementOffset: function (element) {

      var $element = $(element);
      if (!$element.length)
        return;
      if (!$element.is(':visible')) {
        $element.data('element-style', $element.attr('style'));
        $element.css({ 'visibility': 'hidden', 'display': 'block', 'transform': 'none', 'animation': 'none' });
        $element.removeAttr('style').attr('style', $element.data('element-style'));
      }

      return $element.offset().top;
    },
    quadmenuInitItemCarousel: function (plugin) {

      plugin = plugin || this;
      if (typeof $.fn.owlCarousel === 'undefined')
        return;
      this.$ul.on('mouseenter.hoverIntent', '.owl-carousel', function () {

        var $owl = $(this);
        $owl.trigger('stop.owl.autoplay');
      });
      this.$ul.on('mouseleave.hoverIntent', '.owl-carousel', function () {

        var $owl = $(this);
        if ($(this).data('autoplay') !== 'on')
          return;
        $owl.trigger('play.owl.autoplay');
      });
      this.$ul.on('hide.quadmenu.dropdown.carousel', '.quadmenu-item', function () {

        var $owl = $(this).find('.owl-carousel');

        if (!$owl.length)
          return;

        $owl.trigger('stop.owl.autoplay');
        $owl.trigger('stop.owl.video');

      });
      this.$ul.on('shown.quadmenu.dropdown.carousel', '.quadmenu-item', function (e) {
        // breakes the shown.quadmenu.dropdown event
        //e.stopPropagation();

        var $owl = $(this).find('.owl-carousel');

        if ($owl.length) {

          plugin.handleCarousel($owl, plugin);

        }

      });

      plugin.handleCarousel(this.$ul.find('.quadmenu-item-level-0 > .owl-carousel'), plugin);

    },
    handleCarousel: function (owl, plugin) {

      plugin = plugin || this;

      var $owl = $(owl);

      $owl.each(function () {

        var $owl = $(this),
          speed = parseInt($owl.data('speed')),
          autoplay = $owl.data('autoplay') === 'on' ? true : false,
          pagination = $owl.data('pagination') === 'on' ? true : false,
          dots = $owl.data('dots') === 'on' ? true : false,
          items = $owl.data('items') || 1,
          margin = parseInt(plugin.settings.containerGutter / 2),
          dotsEach = items === 1,
          autoplay_speed = parseInt($owl.data('autoplay_speed')) + speed;

        if ($owl.hasClass('owl-loaded')) {
          $owl.trigger('refresh.owl.carousel');
          return;
        }

        $owl.owlCarousel({
          itemClass: 'quadmenu-item-type-panel',
          responsive: {
            0: {
              items: 1
            },
            600: {
              items: Math.min(2, items)
            },
            900: {
              items: items
            },
          },
          //autoWidth: true,
          loop: true,
          navText: false,
          autoplayHoverPause: true,
          dotsEach: dotsEach,
          items: items,
          margin: margin,
          dots: dots,
          nav: pagination,
          smartSpeed: speed,
          autoplay: autoplay,
          autoplayTimeout: autoplay_speed,
        });
      });
    },
    quadmenuInitItemLogIn: function (plugin) {

      plugin = plugin || this;

      this.$ul.on('hide.quadmenu.dropdown.login', '.quadmenu-item-type-login', function (e) {

        var $li = $(this);

        plugin.handleMouseHover($li.find('> .quadmenu-dropdown-toggle.hoverintent'), plugin);
      });

      this.$ul.on('shown.quadmenu.dropdown.login', '.quadmenu-item-type-login', function (e) {

        var $li = $(this);

        $li.find('input').on('click', function () {
          $li.off('mousemove.hoverIntent');
          $li.off('mouseenter.hoverIntent');
          $li.off('mouseleave.hoverIntent');
          $li.removeProp('hoverIntent_t');
          $li.removeProp('hoverIntent_s');
        });

        $li.find('[data-toggle=form]').on('click', function () {
          e.preventDefault();
          $li.find($(this).data('target')).fadeIn().removeClass('hidden');
          $li.find($(this).data('current')).fadeOut().addClass('hidden');
          $li.find('> .quadmenu-dropdown-menu > ul').removeData('quadmenu-dropdownHeight');
          $li.find('> .quadmenu-dropdown-menu > ul').removeAttr('style');
          $li.trigger('shown.quadmenu.dropdown.height');
          $li.trigger('shown.quadmenu.dropdown.pscrollbar');
        });
        plugin.handleForm(e, $li);
      });
    },
    handleForm: function (e, li) {

      var $li = $(li);

      $li.find('form').on('submit', function (e) {

        //Prevent default action, so when user clicks button he doesn't navigate away from page
        if (e.preventDefault) {
          e.preventDefault();
        } else {
          e.returnValue = false;
        }

        var $form = $(this);

        var $dropdown = $form.closest('.quadmenu-dropdown-menu'),
          $message = $form.find('.quadmenu-result-message');
        // Collect data from inputs
        $.ajax({
          type: 'POST',
          url: quadmenu.ajaxurl,
          data: {
            action: $form.find('input[name=action]').val(),
            nonce: $form.find('input[name=quadmenu_nonce]').val(),
            user: $form.find('input[name=quadmenu_username]').val(),
            pass: $form.find('input[name=quadmenu_pass]').val(),
            mail: $form.find('input[name=quadmenu_email]').val(),
            name: $form.find('input[name=quadmenu_name]').val(),
            nick: $form.find('input[name=quadmenu_nick]').val(),
            //nonce: $form.find('input[name=' + $form.find('input[name=action]').val() + ']').val(),
          },
          beforeSend: function () {
            $dropdown.addClass('quadmenu-dropdown-mask');
          },
          complete: function () {
            setTimeout(function () {
              $dropdown.removeClass('quadmenu-dropdown-mask');
            }, 600);
          },
          success: function (response) {

            $message.empty().append(response.data);
            if (response.success === true) {
              setTimeout(function () {
                window.location.reload();
              }, 200);
            }
          },
        });
      });
    },
    quadmenuInitItemSocial: function (plugin) {

      plugin = plugin || this;
      plugin.handleSocial();
      $(window).on('resize', $.debounce(600, function () {
        plugin.handleSocial();
      }));
    },
    handleSocial: function (plugin) {

      plugin = plugin || this;
      this.$ul.off('shown.quadmenu.dropdown.social hidden.quadmenu.dropdown.social');
      if (!this.$quadmenu.hasClass('quadmenu-is-horizontal'))
        return;
      this.$ul.on('shown.quadmenu.dropdown.social', '.quadmenu-item-type-social', function () {
        plugin.$ul.find('> li.quadmenu-item.quadmenu-item-level-0:not(.quadmenu-item-type-social)').addClass('invisible');
        plugin.$ul.find('> li.quadmenu-slidebar').addClass('invisible');
      });
      this.$ul.on('hidden.quadmenu.dropdown.social', '.quadmenu-item-type-social', function () {
        plugin.$ul.find('> li.quadmenu-item.quadmenu-item-level-0:not(.quadmenu-item-type-social)').removeClass('invisible');
        plugin.$ul.find('> li.quadmenu-slidebar').removeClass('invisible');
      });
    },
    quadmenuInitItemCart: function (plugin) {

      plugin = plugin || this;

      var $cart = plugin.$quadmenu.find('li.quadmenu-item-type-cart');

      $cart.each(function () {

        var $li = $(this),
          url = $li.find('> a').data('cart-url'),
          animation = $li.find('> a').data('cart-animation');

        if (window.location.href === url) {
          $cart.removeClass('quadmenu-dropdown');
          $cart.find('> a').attr('href', 'javascript:void(0)');
        }

        $(document).bind('added_to_cart removed_from_cart edd_quantity_updated', function (e, cart) {

          $cart.find('.quadmenu-cart-qty', $li).addClass(animation);

          if (cart !== undefined && $(cart['span.quadmenu-cart-qty']).html() == 0) {
            $li.addClass('quadmenu-cart-empty');
          } else {
            $li.removeClass('quadmenu-cart-empty');
          }

          setTimeout(function () {
            $cart.find('.quadmenu-cart-qty', $li).removeClass(animation);
          }, 500);

        });

        //$(document).bind('added_to_cart removed_from_cart', function (e, cart) {

        //if (cart !== undefined) {
        //plugin.handleWooCart(plugin, $li, $(cart['div.widget_shopping_cart_content']), url);
        //}
        //});

        $(document).bind('edd_quantity_updated', function () {
          plugin.handleEddCart(plugin, $li, $cart, url);
        });

      });

    },
    /*handleWooCart: function (plugin, $li, $cart, url) {
     
     plugin = plugin || this;
     
     var total = $cart.find('.total > .amount').html() || $('.quadmenu-dropdown-toggle', $li).data('cart-price'),
     the_quantities = $cart.find('.quantity') || 0,
     qty = 0,
     numberPattern = /\d+/g;
     
     the_quantities.each(function (idx, el) {
     var qtytext = $(el).html().match(numberPattern);
     var qtyint = parseInt(qtytext[0]);
     qty = qty + qtyint;
     });
     
     plugin.updateCart($li, total, qty, url);
     
     },*/
    handleEddCart: function (plugin, $li, $cart, url) {

      plugin = plugin || this;

      var $edd_cart = $cart.find('.widget_edd_cart_widget');

      if (!$edd_cart.length)
        return;

      var total = $edd_cart.find('.edd_subtotal .subtotal').html(),
        qty = $edd_cart.find('.edd-cart-quantity').html();

      plugin.updateCart($li, total, qty, url);

    },
    updateCart: function (li, total, qty, url) {

      var $li = $(li),
        $total = $li.find('.quadmenu-cart-total'),
        $qty = $li.find('.quadmenu-cart-qty');

      $total.html(total);

      $qty.html(qty);

      if (qty > 0) {
        $li.removeClass('quadmenu-cart-empty');
      } else {
        $li.addClass('quadmenu-cart-empty');
      }

      if (url) {
        $li.find('> a').attr('href', url);
      }

      setTimeout(function () {
        $qty.removeClass('animate');
      }, 1500);
    },
    quadmenuInitItemWidgets: function (plugin) {

      $(document).on('show.quadmenu.dropdown', function (e) {
        $(this).find('.widget_media_audio > video, .widget_media_audio > audio').each(function () {
          this.player.trigger('resize');
        });
      });

      $(document).on('hidden.quadmenu.dropdown', function (e) {
        $(this).find('.widget_media_video video, .widget_media_video audio').each(function () {
          this.player.pause();
        });
      });
    },
  };
  $.fn.quadmenu = function (options) {

    var args = arguments;
    if (options === undefined || typeof options === 'object') {

      return this.each(function () {
        if (!$.data(this, 'plugin_quadmenu')) {
          $.data(this, 'plugin_quadmenu', new Plugin(this, options));
        }
      });
    } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
      // Cache the method call to make it possible to return a value
      var returns;
      this.each(function () {
        var instance = $.data(this, 'plugin_quadmenu');
        // Tests that there's already a plugin-instance and checks that the requested public method exists
        if (instance instanceof Plugin && typeof instance[options] === 'function') {

          // Call the method of our plugin instance, and pass it the supplied arguments.
          returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
        }

        // Allow instances to be destroyed via the 'destroy' method
        if (options === 'destroy') {
          $.data(this, 'plugin_quadmenu', null);
        }
      });
      // If the earlier cached method gives a value back return the value, otherwise return this to preserve chainability.
      return returns !== undefined ? returns : this;
    }
  };
})(jQuery, window, document);
//console.time('Time');
(function ($) {

  function quadmenu_init() {
    $('nav#quadmenu').quadmenu();
  }

  quadmenu_init();

  $(window).on('load', function () {
    quadmenu_init();
  });

})(jQuery);
//console.timeEnd('Time');