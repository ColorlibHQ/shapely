// jscs:ignore validateLineBreaks
( function() {
	var isWebkit = navigator.userAgent.toLowerCase().indexOf( 'webkit' ) > -1,
	    isOpera  = navigator.userAgent.toLowerCase().indexOf( 'opera' )  > -1,
	    isIe     = navigator.userAgent.toLowerCase().indexOf( 'msie' )   > -1;

	if ( ( isWebkit || isOpera || isIe ) && document.getElementById && window.addEventListener ) {
		window.addEventListener( 'hashchange', function() {
			var id = location.hash.substring( 1 ),
				element;

			// [A-z] is not [A-Za-z]: it also spans [ \ ] ^ _ ` between Z and a.
			if ( ! ( /^[A-Za-z0-9_-]+$/.test( id ) ) ) {
				return;
			}

			element = document.getElementById( id );

			if ( element ) {
				if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
					element.tabIndex = -1;
				}

				// This is a DOM node, not a jQuery object: .trigger() does not exist
				// on it, so this threw a TypeError and the skip link never actually
				// moved keyboard focus.
				element.focus();
			}
		}, false );
	}
})();
