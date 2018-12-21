(function ($) {// jscs:ignore validateLineBreaks

	'use strict';

	var api = wp.customize;

	api.shapelyLogoDimension = api.Control.extend({

		ready: function () {
			var control = this,
				dimensions;

			control.logo = api.control('custom_logo');
			control.values = control.setting();
			dimensions = control.container.find('.shapely-dimension');
			control.widthElement = $(dimensions[0]);
			control.heightElement = $(dimensions[1]);

			if (undefined !== control.logo.params.attachment) {
				control.hasLogo = true;
				control.logoWidth = control.logo.params.attachment.width;
				control.logoHeight = control.logo.params.attachment.height;
			} else {
				control.hasLogo = false;
				control.toggle(false);
			}

			control.logo.setting.bind('change', function () {
				control.updateLogo();
			});

			control.widthElement.on('change keyup', function () {
				if (control.hasLogo) {
					control.calculateRatio('width');
					control.shapelyInterval = setInterval(control.updateControl, 1000, control);
				}
			});

			control.heightElement.on('change keyup', function () {
				if (control.hasLogo) {
					control.calculateRatio('height');
					control.shapelyInterval = setInterval(control.updateControl, 1000, control);
				}
			});

		},

		updateLogo: function () {
			var control = this,
				values = control.setting();
			if (undefined !== control.logo.params.attachment) {

				control.logoWidth = control.logo.params.attachment.width;
				control.logoHeight = control.logo.params.attachment.height;

				if (!values) {
					values = {
						'width': control.logoWidth,
						'height': control.logoHeight,
					};
				} else {
					values.width = control.logoWidth;
					values.height = control.logoHeight;
				}

				control.widthElement.val(control.logoWidth);
				control.heightElement.val(control.logoHeight);

				control.setting({});
				control.setting(values);
				control.hasLogo = true;
				control.toggle(true);
			} else {
				control.hasLogo = false;
				control.toggle(false);
			}
		},

		calculateRatio: function (keep) {
			var control = this,
				aux;

			if ('width' === keep) {
				aux = control.logoHeight * control.widthElement.val() / control.logoWidth;
				control.heightElement.val(parseFloat(aux).toFixed(0));
			} else if ('height' === keep) {
				aux = control.logoWidth * control.heightElement.val() / control.logoHeight;
				control.widthElement.val(parseFloat(aux).toFixed(0));
			}

			clearInterval(control.shapelyInterval);
		},

		updateControl: function (control) {
			var values = control.setting();

			if (!values) {
				values = {
					'width': control.widthElement.val(),
					'height': control.heightElement.val(),
				};
			} else {
				values.width = control.widthElement.val();
				values.height = control.heightElement.val();
			}
			control.setting({});
			control.setting(values);
			clearInterval(control.shapelyInterval);
		}

	});

	// Extend epsilon button constructor
	$.extend(api.controlConstructor, {
		'shapely-logo-dimension': api.shapelyLogoDimension
	});

})(jQuery);
