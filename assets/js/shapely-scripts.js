/* jshint es3:false, esversion:6 */
(function ($) {
  // jscs:ignore validateLineBreaks

  let clNav, clNavOuterHeight, windowW, menu, farRight, isOnScreen, difference, videos, recentEntries, searchInterval, shapelyCf, newURL;

  $(function () {
    if ('function' === typeof $.fn.imagesLoaded) {
      $('body').imagesLoaded(function () {
        $(window).trigger('resize').trigger('scroll');
      });
    }

    $('.shapely-dropdown').on('click', function (evt) {
      evt.preventDefault();
      $(this).parent().find('> ul').toggleClass('active');
      $(window).trigger('resize').trigger('scroll');
    });

    // Smooth scroll to inner links
    $('.inner-link').each(function () {
      let href = $(this).attr('href') || '';
      if ('#' !== href.charAt(0)) {
        $(this).removeClass('inner-link');
      }
    });

    // Smooth scroll
    (function () {
      if ('' === window.location.hash) {
        return;
      }

      // Try to extract the target ID from the related menu item, else use the hash as ID.
      // The hash is user-controlled, so an invalid selector must not abort document-ready.
      let scrollTarget;
      try {
        let scrollToID = $('#site-navigation #menu a[href="' + window.location.hash + '"]').data('scroll');
        scrollToID = scrollToID ? '#' + scrollToID : window.location.hash;
        scrollTarget = $(scrollToID);
      } catch (e) {
        return;
      }

      if (!scrollTarget || scrollTarget.length < 1) {
        return;
      }

      $('html,body').animate(
        {
          scrollTop: scrollTarget.offset().top,
        },
        2000
      );

      newURL = window.location.href.replace(window.location.hash, '');
      window.history.replaceState({}, document.title, newURL);
    })();

    $('#site-navigation #menu a[href^="#"]:not([href="#"])').on('click', function (evt) {
      let target;
      try {
        let scrollToID = '#' + $(this).data('scroll');

        if ($(scrollToID).length > 1) {
          scrollToID = $(this).attr('href');
        }

        target = $(scrollToID);
      } catch (e) {
        return;
      }

      if (!target || target.length < 1) {
        return;
      }

      evt.preventDefault();
      $('html,body').animate(
        {
          scrollTop: target.offset().top,
        },
        2000
      );
    });

    $('.inner-link').on('click', function () {
      $('html, body').animate(
        {
          scrollTop: 0,
        },
        500
      );
      return false;
    });

    // Append .background-image-holder <img>'s as CSS backgrounds
    $('.background-image-holder').each(function () {
      let imgSrc = $(this).children('img').attr('src');
      $(this).css('background', 'url("' + imgSrc + '")');
      $(this).children('img').hide();
      $(this).css('background-position', 'initial');
    });

    // Fade in background images
    setTimeout(function () {
      $('.background-image-holder').each(function () {
        $(this).addClass('fadeIn');
      });
    }, 200);

    if (window.ShapelyAdminObject && '1' === String(ShapelyAdminObject.sticky_header)) {
      // Fix nav to top while scrolling
      clNav = $('body .nav-container nav:first');
      clNavOuterHeight = $('body .nav-container nav:first').outerHeight();
      windowW = $(window).width();
      if (windowW > 991) {
        window.addEventListener('scroll', updateNav, false);
        updateNav();
      }

      $(window).on('resize', function () {
        windowW = $(window).width();
        if (windowW < 992) {
          clNav.removeClass('fixed scrolled outOfSight');
        } else {
          window.addEventListener('scroll', updateNav, false);
          updateNav();
        }
      });
    }

    // Menu dropdown positioning

    $('.menu > li > ul').each(function () {
      menu = $(this).offset();
      farRight = menu.left + $(this).outerWidth(true);
      if (farRight > $(window).width() && !$(this).hasClass('mega-menu')) {
        $(this).addClass('make-right');
      } else if (farRight > $(window).width() && $(this).hasClass('mega-menu')) {
        isOnScreen = $(window).width() - menu.left;
        difference = $(this).outerWidth(true) - isOnScreen;
        $(this).css('margin-left', -difference);
      }
    });

    // Mobile Menu

    $('.mobile-toggle').on('click', function () {
      $('.nav-bar').toggleClass('nav-open');
      $(this).toggleClass('active');
      // Keep the button's accessible state in sync with the visual state.
      $(this).attr('aria-expanded', $(this).hasClass('active') ? 'true' : 'false');
      $('.search-widget-handle').toggleClass('hidden-xs hidden-sm');
      $(window).trigger('resize').trigger('scroll');
    });

    $('.module.widget-handle').on('click', function () {
      $(this).toggleClass('toggle-search');
      $(window).trigger('resize').trigger('scroll');
    });

    $('.search-widget-handle .search-form input').on('click', function (e) {
      if (!e) {
        e = window.event;
      }
      e.stopPropagation();
    });

    // Image Sliders. FlexSlider is optional -- a blocked or failed script
    // must not take the rest of document-ready down with it.
    if ('function' === typeof $.fn.flexslider) {
      $('.slider-all-controls').flexslider({
        start: function (slider) {
          if (slider.find('.slides li:first-child').find('.fs-vid-background video').length) {
            slider.find('.slides li:first-child').find('.fs-vid-background video').get(0).play();
          }
        },
        after: function (slider) {
          if (slider.find('.fs-vid-background video').length) {
            if (slider.find('li:not(.flex-active-slide)').find('.fs-vid-background video').length) {
              slider.find('li:not(.flex-active-slide)').find('.fs-vid-background video').get(0).pause();
            }
            if (slider.find('.flex-active-slide').find('.fs-vid-background video').length) {
              slider.find('.flex-active-slide').find('.fs-vid-background video').get(0).play();
            }
          }
        },
      });
      $('.slider-paging-controls').flexslider({
        animation: 'slide',
        directionNav: false,
        after: function (slider) {
          if (!slider.playing) {
            slider.pause();
            slider.play();
            slider.off('mouseenter mouseleave');
            slider.off('mouseover mouseout');
            slider
              .on('mouseover', function () {
                if (!slider.manualPlay && !slider.manualPause) {
                  slider.pause();
                }
              })
              .on('mouseout', function () {
                if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
                  slider.play();
                }
              });
          }
        },
      });
      $('.slider-arrow-controls').flexslider({
        controlNav: false,
        after: function (slider) {
          if (!slider.playing) {
            slider.pause();
            slider.play();
            slider.off('mouseenter mouseleave');
            slider.off('mouseover mouseout');
            slider
              .on('mouseover', function () {
                if (!slider.manualPlay && !slider.manualPause) {
                  slider.pause();
                }
              })
              .on('mouseout', function () {
                if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
                  slider.play();
                }
              });
          }
        },
      });
      $('.slider-thumb-controls .slides li').each(function () {
        let imgSrc = $(this).find('img').attr('src');
        $(this).attr('data-thumb', imgSrc);
      });
      $('.slider-thumb-controls').flexslider({
        animation: 'slide',
        controlNav: 'thumbnails',
        directionNav: true,
        after: function (slider) {
          if (!slider.playing) {
            slider.pause();
            slider.play();
            slider.off('mouseenter mouseleave');
            slider.off('mouseover mouseout');
            slider
              .on('mouseover', function () {
                if (!slider.manualPlay && !slider.manualPause) {
                  slider.pause();
                }
              })
              .on('mouseout', function () {
                if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
                  slider.play();
                }
              });
          }
        },
      });
      $('.logo-carousel').flexslider({
        minItems: 1,
        maxItems: 4,
        move: 1,
        itemWidth: 200,
        itemMargin: 0,
        animation: 'slide',
        slideshow: true,
        slideshowSpeed: 3000,
        directionNav: false,
        controlNav: false,
        after: function (slider) {
          if (!slider.playing) {
            slider.pause();
            slider.play();
            slider.off('mouseenter mouseleave');
            slider.off('mouseover mouseout');
            slider
              .on('mouseover', function () {
                if (!slider.manualPlay && !slider.manualPause) {
                  slider.pause();
                }
              })
              .on('mouseout', function () {
                if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
                  slider.play();
                }
              });
          }
        },
      });
    }

    // Lightbox gallery titles
    $('.lightbox-grid li a').each(function () {
      let galleryTitle = $(this).closest('.lightbox-grid').attr('data-gallery-title');
      $(this).attr('data-lightbox', galleryTitle);
    });

    videos = $('.video-widget');
    if (videos.length) {
      $.each(videos, function () {
        let play = $(this).find('.play-button'),
          pause = $(this).find('.pause-button'),
          isYoutube = $(this).hasClass('youtube'),
          isVimeo = $(this).hasClass('vimeo'),
          videoId,
          mute,
          instance,
          self,
          autoplay,
          data,
          options,
          containerId,
          player;

        if (isYoutube && 'function' === typeof $.fn.YTPlayer) {
          videoId = $(this).attr('data-video-id');
          autoplay = parseInt($(this).attr('data-autoplay'), 10);
          mute = parseInt($(this).attr('data-mute'), 10);
          instance = $(this).YTPlayer({
            fitToBackground: true,
            videoId: videoId,
            mute: mute,
            playerVars: {
              autoplay: autoplay,
              autohide: 0,
              branding: 0,
              controls: 0,
              showinfo: 0,
              modestbranding: 0,
            },
            playerlets: {
              rel: 0,
            },
          });
          self = $(this);

          $(document).on('YTBGREADY', function () {
            let iframe = self.find('iframe'),
              height = iframe.height();
          });

          $(play).on('click', function (e) {
            let ytPlayer = $(this).parents('.video-widget').data('ytPlayer');
            e.preventDefault();
            if (ytPlayer && ytPlayer.player) {
              ytPlayer.player.playVideo();
            }
          });

          $(pause).on('click', function (e) {
            let ytPlayer = $(this).parents('.video-widget').data('ytPlayer');
            e.preventDefault();
            if (ytPlayer && ytPlayer.player) {
              ytPlayer.player.pauseVideo();
            }
          });
        } else if (isVimeo && 'undefined' !== typeof Vimeo && Vimeo.Player) {
          data = $(this).data();
          options = {
            id: data.videoId,
            autoplay: data.autoplay,
            loop: 1,
            title: false,
            portrait: false,
            byline: false,
            height: $(this).height(),
            width: $(this).width(),
          };
          containerId = $(this).find('.vimeo-holder').attr('id');
          player = new Vimeo.Player(containerId, options);

          if (data.mute) {
            player.setVolume(0);
          }

          $(play).on('click', function () {
            player.play();
          });
          $(pause).on('click', function () {
            player.pause();
          });
        } else {
          $(play).on('click', function (e) {
            let instance = $(this).parents('.video-widget').data('vide');
            e.preventDefault();
            if (instance && instance.getVideoObject()) {
              instance.getVideoObject().play();
            }
          });

          $(pause).on('click', function (e) {
            let instance = $(this).parents('.video-widget').data('vide');
            e.preventDefault();
            if (instance && instance.getVideoObject()) {
              instance.getVideoObject().pause();
            }
          });
        }
      });
    }

    recentEntries = $('.widget_recent_entries').find('li');
    $.each(recentEntries, function () {
      $(this).find('a').insertAfter($(this).find('.post-date'));
    });

    $('.comment-form').find('textarea').insertAfter($('.comment-form > #url'));

    if ('undefined' !== typeof $.fn.owlCarousel) {
      $('.owlCarousel').each(function (index) {
        let sliderSelector = '#owlCarousel-' + $(this).data('slider-id'); // This is the slider selector
        let sliderItems = $(this).data('slider-items');
        let sliderSpeed = $(this).data('slider-speed');
        let sliderAutoPlay = $(this).data('slider-auto-play');
        let sliderSingleItem = $(this).data('slider-single-item');

        //Conversion of 1 to true & 0 to false
        // auto play
        sliderAutoPlay = !(0 === sliderAutoPlay || 'false' === sliderAutoPlay);

        // Custom Navigation events outside of the owlCarousel mark-up
        $('.shapely-owl-next').on('click', function (event) {
          event.preventDefault();
          $(sliderSelector).trigger('next.owl.carousel');
        });
        $('.shapely-owl-prev').on('click', function (event) {
          event.preventDefault();
          $(sliderSelector).trigger('prev.owl.carousel');
        });

        // Instantiate the slider with all the options
        $(sliderSelector).owlCarousel({
          items: sliderItems,
          loop: false,
          margin: 2,
          autoplay: sliderAutoPlay,
          dots: false,
          autoplayTimeout: sliderSpeed * 10,
          responsive: {
            0: {
              items: 1,
            },
            768: {
              items: sliderItems,
            },
          },
        });
      });
    } // End

    // Keep the header search panel open while any control inside it has focus.
    // These handlers used to target #s / #searchsubmit, ids the search form has
    // never rendered, so the panel closed the moment the field was focused.
    // The timer is a one-shot setTimeout: the old setInterval was never cleared
    // unless the submit button happened to receive focus.
    $('#masthead .function').on('focusin', function () {
      clearTimeout(searchInterval);
      $(this).addClass('active');
    });

    $('#masthead .function').on('focusout', function () {
      let $panel = $(this);
      clearTimeout(searchInterval);
      searchInterval = setTimeout(function () {
        // Only close once focus has genuinely left the panel, not while it is
        // moving from the input to the submit button.
        if (!$panel.find(':focus').length) {
          $panel.removeClass('active');
        }
      }, 150);
    });

    // Check if is a contact form 7 with parallax background
    shapelyCf = $('.contact-section.image-bg .wpcf7');
    if (shapelyCf.length > 0) {
      shapelyCf.on('wpcf7submit', function () {
        setTimeout(function () {
          $(window).trigger('resize').trigger('scroll');
        }, 800);
      });
    }
  });

  $(window).on('load', function () {
    // "use strict";
    // Resetting testimonial parallax height
    let msnry, container, clFirstSectionHeight;
    if (0 !== $('.testimonial-section').length) {
      testimonialHeight();
      setTimeout(function () {
        testimonialHeight();
      }, 3000);
    }

    // Initialize Masonry
    if ($('.masonry').length && 'undefined' !== typeof Masonry) {
      container = document.querySelector('.masonry');
      msnry = new Masonry(container, {
        itemSelector: '.masonry-item',
      });

      msnry.on('layoutComplete', function () {
        clFirstSectionHeight = $('.main-container section:nth-of-type(1)').outerHeight(true);
        $('.masonry').addClass('fadeIn');
        $('.masonry-loader').addClass('fadeOut');
        if ($('.masonryFlyIn').length) {
          masonryFlyIn();
        }
      });

      msnry.layout();
    }

    // Navigation height
    clFirstSectionHeight = $('.main-container section:nth-of-type(1)').outerHeight(true);
  });

  /* Function To
   * keep menu fixed
   **/
  function updateNav() {
    let scroll = $(window).scrollTop();
    let windowW = $(window).width();

    if (windowW < 992) {
      return;
    }

    if (scroll > clNavOuterHeight) {
      clNav.addClass('outOfSight');
    }

    if ($(window).scrollTop() > clNavOuterHeight + 65) {
      //If href = #element id
      clNav.addClass('fixed scrolled');
    }

    if (0 === $(window).scrollTop()) {
      clNav.removeClass('fixed scrolled outOfSight');
    }
  }

  function masonryFlyIn() {
    let $items = $('.masonryFlyIn .masonry-item');
    let time = 0;

    $items.each(function () {
      let item = $(this);
      setTimeout(function () {
        item.addClass('fadeIn');
      }, time);
      time += 170;
    });
  }

})(jQuery);

/*
 * Resetting testimonial parallax height
 */
function testimonialHeight() {
  jQuery('.testimonial-section .parallax-window').css('height', jQuery('.testimonial-section .parallax-window .container').outerHeight() + 150);
  jQuery(window).trigger('resize').trigger('scroll');
}
