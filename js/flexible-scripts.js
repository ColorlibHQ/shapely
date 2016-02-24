;(function($){
    
var cl_firstSectionHeight,
    cl_nav,
    cl_navOuterHeight,
    cl_navScrolled = false,
    cl_navFixed = false,
    cl_outOfSight = false,
    cl_scrollTop = 0;

jQuery(document).ready(function($) {
    //"use strict";

    // Smooth scroll to inner links

    jQuery('.inner-link').each(function(){
        var href = jQuery(this).attr('href');
        if(href.charAt(0) !== "#"){
            jQuery(this).removeClass('inner-link');
        }
    });

    if(jQuery('.inner-link').length){
        jQuery('.inner-link').smoothScroll({
            offset: -55,
            speed: 800
        });
    }

    // Update scroll variable for scrolling functions

    addEventListener('scroll', function() {
        cl_scrollTop = window.pageYOffset;
    }, false);

    // Append .background-image-holder <img>'s as CSS backgrounds

    jQuery('.background-image-holder').each(function() {
        var imgSrc = jQuery(this).children('img').attr('src');
        jQuery(this).css('background', 'url("' + imgSrc + '")');
        jQuery(this).children('img').hide();
        jQuery(this).css('background-position', 'initial');
    });

    // Fade in background images

    setTimeout(function() {
        jQuery('.background-image-holder').each(function() {
            jQuery(this).addClass('fadeIn');
        });
    }, 200);

    // Navigation

    if (!jQuery('nav').hasClass('fixed') && !jQuery('nav').hasClass('absolute')) {

        // Make nav container height of nav

        jQuery('.nav-container').css('min-height', jQuery('nav').outerHeight(true));

        jQuery(window).resize(function() {
            jQuery('.nav-container').css('min-height', jQuery('nav').outerHeight(true));
        });

        // Compensate the height of parallax element for inline nav

        if ($(window).width() > 768) {
            $('.parallax:nth-of-type(1) .background-image-holder').css('top', -($('nav').outerHeight(true)));
        }

        // Adjust fullscreen elements

        if ($(window).width() > 768) {
            $('section.fullscreen:nth-of-type(1)').css('height', ($(window).height() - $('nav').outerHeight(true)) + 2);
        }

    } else {
        $('body').addClass('nav-is-overlay');
    }

    if ($('nav').hasClass('bg-dark')) {
        $('.nav-container').addClass('bg-dark');
    }


    // Fix nav to top while scrolling

    cl_nav = $('body .nav-container nav:first');
    cl_navOuterHeight = $('body .nav-container nav:first').outerHeight();
    window.addEventListener("scroll", updateNav, false);

    // Menu dropdown positioning

    $('.menu > li > ul').each(function() {
        var menu = $(this).offset();
        var farRight = menu.left + $(this).outerWidth(true);
        if (farRight > $(window).width() && !$(this).hasClass('mega-menu')) {
            $(this).addClass('make-right');
        } else if (farRight > $(window).width() && $(this).hasClass('mega-menu')) {
            var isOnScreen = $(window).width() - menu.left;
            var difference = $(this).outerWidth(true) - isOnScreen;
            $(this).css('margin-left', -(difference));
        }
    });

    // Mobile Menu

    $('.mobile-toggle').click(function() {
        $('.nav-bar').toggleClass('nav-open');
        $(this).toggleClass('active');
    });

    $('.menu li').click(function(e) {
        if (!e) e = window.event;
        e.stopPropagation();
        if ($(this).find('ul').length) {
            $(this).toggleClass('toggle-sub');
        } else {
            $(this).parents('.toggle-sub').removeClass('toggle-sub');
        }
    });

    $('.menu li a').click(function() {
        if ($(this).hasClass('inner-link')){
            $(this).closest('.nav-bar').removeClass('nav-open');
        }
    });

    $('.module.widget-handle').click(function() {
        $(this).toggleClass('toggle-search');
    });
    
    $('.search-widget-handle .search-form input').click(function(e){
        if (!e) e = window.event;
        e.stopPropagation();
    });

    // Image Sliders
    $('.slider-all-controls').flexslider({
        start: function(slider){
            if(slider.find('.slides li:first-child').find('.fs-vid-background video').length){
               slider.find('.slides li:first-child').find('.fs-vid-background video').get(0).play();
            }
        },
        after: function(slider){
            if(slider.find('.fs-vid-background video').length){
                if(slider.find('li:not(.flex-active-slide)').find('.fs-vid-background video').length){
                    slider.find('li:not(.flex-active-slide)').find('.fs-vid-background video').get(0).pause();
                }
                if(slider.find('.flex-active-slide').find('.fs-vid-background video').length){
                    slider.find('.flex-active-slide').find('.fs-vid-background video').get(0).play();
                }
            }
        }
    });
    $('.slider-paging-controls').flexslider({
        animation: "slide",
        directionNav: false
    });
    $('.slider-arrow-controls').flexslider({
        controlNav: false
    });
    $('.slider-thumb-controls .slides li').each(function() {
        var imgSrc = $(this).find('img').attr('src');
        $(this).attr('data-thumb', imgSrc);
    });
    $('.slider-thumb-controls').flexslider({
        animation: "slide",
        controlNav: "thumbnails",
        directionNav: true
    });
    $('.logo-carousel').flexslider({
        minItems: 1,
        maxItems: 4,
        move: 1,
        itemWidth: 200,
        itemMargin: 0,
        animation: "slide",
        slideshow: true,
        slideshowSpeed: 3000,
        directionNav: false,
        controlNav: false
    });

    // Lightbox gallery titles
    $('.lightbox-grid li a').each(function(){
        var galleryTitle = $(this).closest('.lightbox-grid').attr('data-gallery-title');
        $(this).attr('data-lightbox', galleryTitle);
    });

});

jQuery(window).load(function($) {
   // "use strict";

   // Resetting testimonial parallax height
   if( jQuery('.testimonial-section').length != 0 ){
     testimonialHeight();
     setTimeout(function(){ testimonialHeight(); }, 3000);
   }

    // Initialize Masonry

    if (jQuery('.masonry').length && typeof Masonry != 'undefined') {
        var container = document.querySelector('.masonry');
        var msnry = new Masonry(container, {
            itemSelector: '.masonry-item'
        });

        msnry.on('layoutComplete', function($) {

            cl_firstSectionHeight = jQuery('.main-container section:nth-of-type(1)').outerHeight(true);

            // Fix floating project filters to bottom of projects container

            if (jQuery('.filters.floating').length) {
                setupFloatingProjectFilters();
                updateFloatingFilters();
                window.addEventListener("scroll", updateFloatingFilters, false);
            }

            jQuery('.masonry').addClass('fadeIn');
            jQuery('.masonry-loader').addClass('fadeOut');
            if (jQuery('.masonryFlyIn').length) {
                masonryFlyIn();
            }
        });

        msnry.layout();
    }
    // Navigation height
    cl_firstSectionHeight = jQuery('.main-container section:nth-of-type(1)').outerHeight(true);


});

function updateNav() {

    var scrollY = cl_scrollTop;

    if (scrollY <= 0) {
        if (cl_navFixed) {
            cl_navFixed = false;
            cl_nav.removeClass('fixed');
        }
        if (cl_outOfSight) {
            cl_outOfSight = false;
            cl_nav.removeClass('outOfSight');
        }
        if (cl_navScrolled) {
            cl_navScrolled = false;
            cl_nav.removeClass('scrolled');
        }
        return;
    }
    
    if (scrollY > 100) {
        if (!cl_navScrolled) {
            cl_nav.addClass('scrolled');
            cl_navScrolled = true;
            return;
        }
    } else {
        if (scrollY > cl_navOuterHeight) {
            if (!cl_navFixed) {
                cl_nav.addClass('fixed');
                cl_navFixed = true;
            }

            if (scrollY > cl_navOuterHeight * 2) {
                if (!cl_outOfSight) {
                    cl_nav.addClass('outOfSight');
                    cl_outOfSight = true;
                }
            } else {
                if (cl_outOfSight) {
                    cl_outOfSight = false;
                    cl_nav.removeClass('outOfSight');
                }
            }
        } else {
            if (cl_navFixed) {
                cl_navFixed = false;
                cl_nav.removeClass('fixed');
            }
            if (cl_outOfSight) {
                cl_outOfSight = false;
                cl_nav.removeClass('outOfSight');
            }
        }

        if (cl_navScrolled) {
            cl_navScrolled = false;
            cl_nav.removeClass('scrolled');
        }

    }
}

function masonryFlyIn() {
    var $items = jQuery('.masonryFlyIn .masonry-item');
    var time = 0;

    $items.each(function() {
        var item = jQuery(this);
        setTimeout(function() {
            item.addClass('fadeIn');
        }, time);
        time += 170;
    });
}
})(jQuery);

/*
 * Resetting testimonial parallax height
 */
function testimonialHeight(){
  jQuery('.testimonial-section .parallax-window').css('height', jQuery('.testimonial-section .parallax-window .container').outerHeight()+150 );
  jQuery(window).trigger('resize').trigger('scroll');
}
