/* jshint es3:false, esversion:6 */
(function ($) {// jscs:ignore validateLineBreaks

  let clNav, clNavOuterHeight, windowW, menu, farRight, isOnScreen, difference, videos, recentEntries, searchInterval,
    shapelyCf, element, newURL, scrollToID;

  jQuery(document).ready(function ($) {

    //"use strict";

    jQuery('body').imagesLoaded(function () {
      jQuery(window).trigger('resize').trigger('scroll');
    });

    jQuery('.shapely-dropdown').click(function (evt) {
      evt.preventDefault();
      jQuery(this).parent().find('> ul').toggleClass('active');
      jQuery(window).trigger('resize').trigger('scroll');
    });

    // Smooth scroll to inner links
    jQuery('.inner-link').each(function () {
      let href = jQuery(this).attr('href');
      if ('#' !== href.charAt(0)) {
        jQuery(this).removeClass('inner-link');
      }
    });

    // Smooth scroll
    if ('' !== window.location.hash) {
      element = $('#site-navigation #menu a[href=' + window.location.hash + ']');
      if (element) {
        scrollToID = '#' + element.data('scroll');

        if (jQuery(scrollToID).length > 1) {
          scrollToID = window.location.hash;
        }

        if (jQuery(scrollToID).length < 1) {
          return;
        }

        $('html,body').animate({
          scrollTop: $(scrollToID).offset().top
        }, 2000);

        newURL = window.location.href.replace(window.location.hash, '');
        window.history.replaceState({}, document.title, newURL);
      } else {
        return;
      }
    }

    $('#site-navigation #menu a[href^=#]:not([href=#])').click(function (evt) {
      let scrollToID = '#' + $(this).data('scroll');

      if (jQuery(scrollToID).length > 1) {
        scrollToID = $(this).attr('href');
      }

      if (jQuery(scrollToID).length < 1) {
        return;
      }

      evt.preventDefault();
      $('html,body').animate({
        scrollTop: $(scrollToID).offset().top
      }, 2000);
    });

    jQuery('.inner-link').click(function () {
      jQuery('html, body').animate({
        scrollTop: 0
      }, 500);
      return false;
    });

    // Append .background-image-holder <img>'s as CSS backgrounds

    jQuery('.background-image-holder').each(function () {
      let imgSrc = jQuery(this).children('img').attr('src');
      jQuery(this).css('background', 'url("' + imgSrc + '")');
      jQuery(this).children('img').hide();
      jQuery(this).css('background-position', 'initial');
    });

    // Fade in background images

    setTimeout(function () {
      jQuery('.background-image-holder').each(function () {
        jQuery(this).addClass('fadeIn');
      });
    }, 200);

    if ('1' === ShapelyAdminObject.sticky_header) {

      // Fix nav to top while scrolling
      clNav = $('body .nav-container nav:first');
      clNavOuterHeight = $('body .nav-container nav:first').outerHeight();
      windowW = jQuery(window).width();
      if (windowW > 991) {
        window.addEventListener('scroll', updateNav, false);
        updateNav();
      }

      $(window).resize(function () {
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
        $(this).css('margin-left', -(difference));
      }
    });

    // Mobile Menu

    $('.mobile-toggle').click(function () {
      $('.nav-bar').toggleClass('nav-open');
      $(this).toggleClass('active');
      $('.search-widget-handle').toggleClass('hidden-xs hidden-sm');
      jQuery(window).trigger('resize').trigger('scroll');
    });

    $('.module.widget-handle').click(function () {
      $(this).toggleClass('toggle-search');
      jQuery(window).trigger('resize').trigger('scroll');
    });

    $('.search-widget-handle .search-form input').click(function (e) {
      if (!e) {
        e = window.event;
      }
      e.stopPropagation();
    });

    // Image Sliders
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
      }
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
          slider.mouseover(function () {
            if (!slider.manualPlay && !slider.manualPause) {
              slider.pause();
            }
          }).mouseout(function () {
            if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
              slider.play();
            }
          });
        }
      }
    });
    $('.slider-arrow-controls').flexslider({
      controlNav: false,
      after: function (slider) {
        if (!slider.playing) {
          slider.pause();
          slider.play();
          slider.off('mouseenter mouseleave');
          slider.off('mouseover mouseout');
          slider.mouseover(function () {
            if (!slider.manualPlay && !slider.manualPause) {
              slider.pause();
            }
          }).mouseout(function () {
            if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
              slider.play();
            }
          });
        }
      }
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
          slider.mouseover(function () {
            if (!slider.manualPlay && !slider.manualPause) {
              slider.pause();
            }
          }).mouseout(function () {
            if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
              slider.play();
            }
          });
        }
      }
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
          slider.mouseover(function () {
            if (!slider.manualPlay && !slider.manualPause) {
              slider.pause();
            }
          }).mouseout(function () {
            if (!slider.manualPause && !slider.manualPlay && !slider.stopped) {
              slider.play();
            }
          });
        }
      }
    });

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
          videoId, mute, instance, self, autoplay, data, options, containerId, player;

        if (isYoutube) {
          videoId = $(this).attr('data-video-id');
          autoplay = parseInt($(this).attr('data-autoplay'), 10);
          mute = parseInt($(this).attr('data-mute'), 10);
          instance = $(this).YTPlayer({
            fitToBackground: true,
            videoId: videoId,
            mute: mute,
            playerlets: {
              modestbranding: 0,
              autoplay: autoplay,
              controls: 0,
              showinfo: 0,
              branding: 0,
              rel: 0,
              autohide: 0
            }
          });
          self = $(this);

          $(document).on('YTBGREADY', function () {
            let iframe = self.find('iframe'),
              height = iframe.height();
          });

          $(play).on('click', function (e) {
            let parent = $(this).parents('.video-widget'),
              instance = $(parent).data('ytPlayer').player;
            e.preventDefault();
            instance.playVideo();
          });

          $(pause).on('click', function (e) {
            let parent = $(this).parents('.video-widget'),
              instance = $(parent).data('ytPlayer').player;
            e.preventDefault();
            instance.pauseVideo();
          });

        } else if (isVimeo) {

          data = jQuery(this).data();
          options = {
            id: data.videoId,
            autoplay: data.autoplay,
            loop: 1,
            title: false,
            portrait: false,
            byline: false,
            height: jQuery(this).height(),
            width: jQuery(this).width()
          };
          containerId = jQuery(this).find('.vimeo-holder').attr('id');
          player = new Vimeo.Player(containerId, options);

          if (data.mute) {
            player.setVolume(0);
          }

          jQuery(play).click(function () {
            player.play();
          });
          jQuery(pause).click(function () {
            player.pause();
          });

        } else {

          $(play).on('click', function (e) {
            let parent = $(this).parents('.video-widget'),
              instance = $(parent).data('vide'),
              video = instance.getVideoObject();
            e.preventDefault();
            video.play();
          });

          $(pause).on('click', function (e) {
            let parent = $(this).parents('.video-widget'),
              instance = $(parent).data('vide'),
              video = instance.getVideoObject();
            e.preventDefault();
            video.pause();
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
              items: 1
            },
            768: {
              items: sliderItems
            }
          }
        });
      });
    } // End

    jQuery('#masthead .function #s').focus(function () {
      jQuery(this).parents('.function').addClass('active');
    });

    jQuery('#masthead .function #s').focusout(function () {
      searchInterval = setInterval(function () {
        jQuery('#masthead .function').removeClass('active');
      }, 500);
    });

    jQuery('#masthead .function #searchsubmit').focus(function () {
      clearInterval(searchInterval);
      jQuery(this).parents('.function').addClass('active');
    });

    jQuery('#masthead .function #searchsubmit').focusout(function () {
      jQuery(this).parents('.function').removeClass('active');
    });

    // Check if is a contact form 7 with parallax background
    shapelyCf = jQuery('.contact-section.image-bg .wpcf7');
    if (shapelyCf.length > 0) {
      shapelyCf.on('wpcf7submit', function () {
        setTimeout(function () {
          jQuery(window).trigger('resize').trigger('scroll');
        }, 800);
      });
    }

  });

  jQuery(window).load(function ($) {

    // "use strict";
    // Resetting testimonial parallax height
    let msnry, container, clFirstSectionHeight;
    if (0 !== jQuery('.testimonial-section').length) {
      testimonialHeight();
      setTimeout(function () {
        testimonialHeight();
      }, 3000);
    }

    // Initialize Masonry

    if (jQuery('.masonry').length && 'undefined' !== typeof Masonry) {
      container = document.querySelector('.masonry');
      msnry = new Masonry(container, {
        itemSelector: '.masonry-item'
      });

      msnry.on('layoutComplete', function ($) {

        clFirstSectionHeight = jQuery('.main-container section:nth-of-type(1)').outerHeight(true);
        jQuery('.masonry').addClass('fadeIn');
        jQuery('.masonry-loader').addClass('fadeOut');
        if (jQuery('.masonryFlyIn').length) {
          masonryFlyIn();
        }
      });

      msnry.layout();
    }

    // Navigation height
    clFirstSectionHeight = jQuery('.main-container section:nth-of-type(1)').outerHeight(true);

  });

  /* Function To
   * keep menu fixed
   **/
  function updateNav() {
    let scroll = $(window).scrollTop();
    let windowW = jQuery(window).width();

    if (windowW < 992) {
      return;
    }

    if (scroll > clNavOuterHeight) {
      clNav.addClass('outOfSight');
    }

    if ($(window).scrollTop() > (clNavOuterHeight + 65)) {//If href = #element id
      clNav.addClass('fixed scrolled');
    }

    if (0 === $(window).scrollTop()) {
      clNav.removeClass('fixed scrolled outOfSight');
    }
  }

  function masonryFlyIn() {
    let $items = jQuery('.masonryFlyIn .masonry-item');
    let time = 0;

    $items.each(function () {
      let item = jQuery(this);
      setTimeout(function () {
        item.addClass('fadeIn');
      }, time);
      time += 170;
    });
  }

  jQuery('body').imagesLoaded(function () {
    jQuery(window).trigger('resize').trigger('scroll');
  });

})(jQuery);

/*
 * Resetting testimonial parallax height
 */
function testimonialHeight() {
  jQuery('.testimonial-section .parallax-window').css('height', jQuery('.testimonial-section .parallax-window .container').outerHeight() + 150);
  jQuery(window).trigger('resize').trigger('scroll');
}
