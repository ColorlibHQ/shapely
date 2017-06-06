/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function ($) {
	$(document).ready(function () {
		if ( 'undefined' === typeof wp || !wp.customize || !wp.customize.selectiveRefresh ) {
			return;
		}

		wp.customize.selectiveRefresh.bind('widget-updated', function (placement) {
			$('.logo-carousel').flexslider({
				minItems      : 1,
				maxItems      : 4,
				move          : 1,
				itemWidth     : 200,
				itemMargin    : 0,
				animation     : "slide",
				slideshow     : true,
				slideshowSpeed: 3000,
				directionNav  : false,
				controlNav    : false
			});

			$('.parallax-window').parallax();

			if ( $('.masonry').length && typeof Masonry != 'undefined' ) {
				var container = document.querySelector('.masonry');
				var msnry = new Masonry(container, {
					itemSelector: '.masonry-item'
				});
				var maxHeight = -1;
				msnry.on('layoutComplete', function ($) {
					var element = jQuery(msnry.element),
							cols = element.find('.masonry-item img');
					jQuery.each(cols, function () {
						if ( parseInt(jQuery(this).attr('height')) > maxHeight ) {
							maxHeight = parseInt(jQuery(this).attr('height'));
						}
					});

				});

				msnry.layout();
				var element = jQuery(msnry.element);

				jQuery(element).css('height', maxHeight + 'px');
			}

			if ( jQuery('.testimonial-section').length != 0 ) {
				testimonialHeight();
				setTimeout(function () {
					testimonialHeight();
				}, 3000);
			}

			$('.slider-arrow-controls').flexslider({
				controlNav: false
			});
			/*
			 * Resetting testimonial parallax height
			 */
			function testimonialHeight() {
				jQuery('.testimonial-section .parallax-window').css('height', jQuery('.testimonial-section .parallax-window .container').outerHeight() + 150);
				jQuery(window).trigger('resize').trigger('scroll');
			}
		});
	});


	if ( typeof(wp) !== 'undefined' ) {
		if ( typeof(wp.customize) !== 'undefined' ) {
			wp.customize.bind('preview-ready', function () {
				wp.customize.preview.bind('update-inline-css', function (object) {
					var data = {
						'action': object.action,
						'args'  : object.data,
						'id'    : object.id
					};

					jQuery.ajax({
						dataType: 'json',
						type    : 'POST',
						url     : WPUrls.ajaxurl,
						data    : data,
						complete: function (json) {
							var sufix = object.action + object.id;
							var style = $('#shapely-style-' + sufix);

							if ( !style.length ) {
								style = $('head').append('<style type="text/css" id="shapely-style-' + sufix + '" />').find('#shapely-style-' + sufix);
							}

							style.html(json.responseText);
						}
					});
				});
			});
		}
	}

})(jQuery);