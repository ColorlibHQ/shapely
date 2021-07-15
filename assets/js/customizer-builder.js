(function( $ ) {// jscs:ignore validateLineBreaks

  'use strict';

  var api = wp.customize;

  api(function() {
    var currentURL = api.settings.url.preview,
        urlBase,
        urlParts,
        pageSidebarID,
        pageSidebarSection;
    if ( currentURL !== ShapelyBuilder.siteURL ) {
      urlParts = currentURL.split( '/' );
      urlParts.pop();
      urlBase = urlParts[ urlParts.length - 1 ];
      if ( undefined !== ShapelyBuilder.pages[ urlBase ] ) {
        pageSidebarID = 'sidebar-widgets-shapely-' + urlBase;
        /*
         * Defer focus until:
         * 1. The section exist.
         * 2. The instance is embedded in the document (and so is focusable).
         * 3. The preview has finished loading so that the active states have been set.
         */
        pageSidebarSection = api.section( pageSidebarID, function( instance ) {
          instance.deferred.embedded.done( function() {
            api.previewer.deferred.active.done( function() {
              instance.trigger('focus');
            } );
          } );
        } );
      }
    }
  } );

})( jQuery );
