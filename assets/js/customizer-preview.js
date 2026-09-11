(function( $ ) {// jscs:ignore validateLineBreaks

  if ( 'undefined' !== typeof( wp ) ) {
    if ( 'undefined' !== typeof( wp.customize ) ) {
      wp.customize.bind( 'preview-ready', function() {
        wp.customize.preview.bind( 'update-inline-css', function( object ) {
          var data = {
            'action': object.action,
            'args': object.data,
            'id': object.id
          };

          jQuery.ajax( {
            dataType: 'json',
            type: 'POST',
            url: WPUrls.ajaxurl,
            data: data,
            complete: function( json ) {
              var sufix = object.action + object.id;
              var style = $( '#shapely-style-' + sufix );

              if ( ! style.length ) {
                style = $( 'head' ).append( '<style type="text/css" id="shapely-style-' + sufix + '" />' ).find( '#shapely-style-' + sufix );
              }

              // .text() rather than .html(): the payload is CSS, so it should never
              // be parsed as markup on its way into the <style> element.
              style.text( json.responseText );
            }
          } );
        } );
      } );
    }
  }

})( jQuery );
