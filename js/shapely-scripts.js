;(function($){
    
var cl_nav,
    cl_navOuterHeight;

jQuery(document).ready(function($) {
    //"use strict";

    // Smooth scroll to inner links

    jQuery('.inner-link').each(function(){
        var href = jQuery(this).attr('href');
        if(href.charAt(0) !== "#"){
            jQuery(this).removeClass('inner-link');
        }
    });

    jQuery('.inner-link').click(function() {
          jQuery('html, body').animate({
            scrollTop: 0
          }, 1000);
          return false;
    });

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

    
    // Fix nav to top while scrolling

    cl_nav = $('body .nav-container nav:first');
    cl_navOuterHeight = $('body .nav-container nav:first').outerHeight();
    window.addEventListener("scroll", updateNav, false);
    updateNav();
    
    
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
        $('.search-widget-handle').toggleClass('hidden-xs hidden-sm');
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

/* Function To 
 * keep menu fixed
 **/
function updateNav(){
    if( $(window).scrollTop() > cl_navOuterHeight ){//if href = #element id
        cl_nav.addClass('fixed scrolled');
    }
    else{
        cl_nav.removeClass('fixed scrolled');
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
