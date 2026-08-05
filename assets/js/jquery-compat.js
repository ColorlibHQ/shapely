/**
 * Minimal jQuery 3 -> 4 compatibility shim.
 *
 * jQuery 4.0 removes the `.bind()` / `.unbind()` / `.delegate()` / `.undelegate()`
 * event aliases. The bundled FlexSlider 2.x still calls all four, so it would
 * throw "is not a function" the moment WordPress ships jQuery 4 and drops
 * jQuery Migrate.
 *
 * Every definition below is guarded, so on jQuery 3.x (where these methods still
 * exist) this file does nothing at all. Only the aliases FlexSlider actually
 * uses are restored -- notably NOT `.load()`, which was both an AJAX method and
 * an event alias and cannot be shimmed unambiguously.
 *
 * @package Shapely
 */
( function ( $ ) {
	'use strict';

	if ( ! $ || ! $.fn ) {
		return;
	}

	if ( 'function' !== typeof $.fn.bind ) {
		$.fn.bind = function ( types, data, fn ) {
			return this.on( types, null, data, fn );
		};
	}

	if ( 'function' !== typeof $.fn.unbind ) {
		$.fn.unbind = function ( types, fn ) {
			return this.off( types, null, fn );
		};
	}

	if ( 'function' !== typeof $.fn.delegate ) {
		// Note the argument order flip: .delegate(selector, types, ...) maps to
		// .on(types, selector, ...).
		$.fn.delegate = function ( selector, types, data, fn ) {
			return this.on( types, selector, data, fn );
		};
	}

	if ( 'function' !== typeof $.fn.undelegate ) {
		$.fn.undelegate = function ( selector, types, fn ) {
			return 1 === arguments.length ?
				this.off( selector, '**' ) :
				this.off( types, selector || '**', fn );
		};
	}

	/*
	 * jQuery 4 also drops every per-event shorthand method. Live testing on
	 * WordPress 7.0.2 caught FlexSlider calling .blur()/.focus() and
	 * OwlCarousel calling .resize(); both libraries are patched, but any
	 * third-party widget dropped into a Shapely site may still use these.
	 */
	'blur focus focusin focusout resize scroll click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup contextmenu'
		.split( ' ' )
		.forEach( function ( name ) {
			if ( 'function' !== typeof $.fn[ name ] ) {
				$.fn[ name ] = function ( data, fn ) {
					return arguments.length > 0 ?
						this.on( name, null, data, fn ) :
						this.trigger( name );
				};
			}
		} );

	/*
	 * Type-checking utilities removed in jQuery 4. OwlCarousel used $.type();
	 * the others are included because they travel together in older plugins.
	 */
	if ( 'function' !== typeof $.type ) {
		var class2type = {};
		'Boolean Number String Function Array Date RegExp Object Error Symbol'
			.split( ' ' )
			.forEach( function ( name ) {
				class2type[ '[object ' + name + ']' ] = name.toLowerCase();
			} );

		$.type = function ( obj ) {
			if ( null == obj ) {
				return obj + '';
			}
			return 'object' === typeof obj || 'function' === typeof obj ?
				class2type[ Object.prototype.toString.call( obj ) ] || 'object' :
				typeof obj;
		};
	}

	if ( 'function' !== typeof $.isFunction ) {
		$.isFunction = function ( obj ) {
			return 'function' === typeof obj && 'number' !== typeof obj.nodeType;
		};
	}

	if ( 'function' !== typeof $.isArray ) {
		$.isArray = Array.isArray;
	}

	if ( 'function' !== typeof $.trim ) {
		$.trim = function ( text ) {
			return null == text ? '' : ( text + '' ).replace( /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '' );
		};
	}

	if ( 'function' !== typeof $.isNumeric ) {
		$.isNumeric = function ( obj ) {
			var type = $.type( obj );
			return ( 'number' === type || 'string' === type ) && ! isNaN( obj - parseFloat( obj ) );
		};
	}

	if ( 'function' !== typeof $.now ) {
		$.now = Date.now;
	}
}( jQuery ) );
