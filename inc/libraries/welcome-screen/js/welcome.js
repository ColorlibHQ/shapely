var welcomeScreenFunctions = {
  /**
   * Import demo content
   */
  importDemoContent: function() {
    var self = this;
    jQuery( '.epsilon-ajax-button' ).click( function( e ) {
      var action = jQuery( this ).attr( 'data-action' ) ? jQuery( this ).attr( 'data-action' ) : jQuery( this ).attr( 'id' ),
          container = jQuery( this ).parents( '.action-required-box' ),
          checkboxes = container.find( ':checkbox' ),
          importThis = {
            'plugins': [],
            'options': []
          };

      e.preventDefault();

      jQuery.each( checkboxes, function( k, item ) {

        if ( jQuery( item ).prop( 'checked' ) ) {
          importThis[ jQuery( item ).attr( 'name' ) ].push( jQuery( item ).val() );
        }

      } );

      self._importPlugins( importThis[ 'plugins' ] );

      if ( importThis[ 'plugins' ].length ) {
        jQuery( document ).on( 'epsilon-all-plugins-imported', function() {
          self._importContent( importThis, container );
        } );
      } else {
        self._importContent( importThis, container );
      }

    } );
  },
  /**
   * Import the actual content
   *
   * @param $import
   * @param action
   * @param container
   * @private
   *
   * @todo send "argument" with demo slug
   */
  _importContent: function( $import, container ) {

    var needImported = 'import-all';

    if ( $import.options.length < 1 ) {
      location.reload();
      return;
    }

    if ( $import.options.length < 2 ) {
      needImported = $import.options[0];
    }

    jQuery.ajax( {
      type: 'POST',
      data: { action: 'shapely_companion_import_content', 'import' : needImported },
      dataType: 'json',
      url: ajaxurl,
      success: function( json ) {
        if ( container.length ) {
          container.html( '<h3>Demo content was imported successfully! </h3>' );

          window.setTimeout( function() {
            container.slideUp( 300, function() {
              container.remove();
              location.reload();
            } );
          }, 3000 );
        }
      },
      /**
       * Throw errors
       *
       * @param jqXHR
       * @param textStatus
       * @param errorThrown
       */
      error: function( jqXHR, textStatus, errorThrown ) {
        console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
      }

    } );
  },
  /**
   * Start the installation/activation of the plugin
   *
   * @param $plugins
   * @private
   */
  _importPlugins: function( $plugins ) {
    var count = 0,
        max = $plugins.length;
    jQuery( 'a[data-slug="' + $plugins[ count ] + '"]' ).click();

    jQuery( document ).on( 'epsilon-plugin-activated', function() {
      count ++;
      if ( count === max ) {
        jQuery( document ).trigger( 'epsilon-all-plugins-imported' );
      }

      if ( 'undefined' !== typeof($plugins[ count ]) ) {
        jQuery( 'a[data-slug="' + $plugins[ count ] + '"]' ).click();
      }
    } );
  },

  /**
   * Dismiss action through AJAX
   */
  dismissAction: function() {
    var args;

    jQuery( '.required-action-button' ).click( function() {
      args = {
        action: [ 'Epsilon_Welcome_Screen', 'handle_required_action' ],
        nonce: welcomeScreen.ajax_nonce,
        args: {
          'do': jQuery( this ).attr( 'data-action' ),
          'id': jQuery( this ).attr( 'id' )
        }
      };

      jQuery.ajax( {
        type: 'POST',
        data: { action: 'welcome_screen_ajax_callback', args: args },
        dataType: 'json',
        url: ajaxurl,
        success: function() {
          location.reload();
        },
        /**
         * Throw errors
         *
         * @param jqXHR
         * @param textStatus
         * @param errorThrown
         */
        error: function( jqXHR, textStatus, errorThrown ) {
          console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
        }

      } );
    } );
  },

  /**
   * Init Range sliders in backend
   *
   * @param context
   */
  rangeSliders: function( context ) {
    var sliders = context.find( '.slider-container' );

    jQuery.each( sliders, function() {
      var slider, input, inputId, id, instance, self;
      self = jQuery( this );
      slider = jQuery( this ).find( '.ss-slider' );
      input = jQuery( this ).find( 'input' );
      inputId = input.attr( 'id' );
      id = slider.attr( 'id' );
      instance = jQuery( '#' + id );

      instance.slider( {
        value: self.find( 'input' ).attr( 'value' ),
        range: 'min',
        min: parseFloat( instance.attr( 'data-attr-min' ) ),
        max: parseFloat( instance.attr( 'data-attr-max' ) ),
        step: parseFloat( instance.attr( 'data-attr-step' ) ),
        /**
         * Removed Change event because server was flooded with requests from
         * javascript, sending changesets on each increment.
         *
         * @param event
         * @param ui
         */
        slide: function( event, ui ) {
          self.find( 'input' ).attr( 'value', ui.value );
        },
        /**
         * Bind the change event to the "actual" stop
         * @param event
         * @param ui
         */
        stop: function( event, ui ) {
          jQuery( '#' + inputId ).trigger( 'change' );
        }
      } );

      jQuery( input ).on( 'focus', function() {
        jQuery( this ).blur();
      } );

      instance.attr( 'value', ( instance.slider( 'value' ) ) );
      instance.on( 'change', function() {
        jQuery( '#' + id ).slider( {
          value: jQuery( this ).val()
        } );
      } );
    } );
  },

  /**
   * Activate the plugin when the plugin has been installed
   */
  activatePlugin: function() {
    var activateButtonSlug = jQuery( 'a[data-slug]' );
    jQuery( activateButtonSlug ).on( 'click', function( e ) {
      var self = jQuery( this ),
          dataToSend = { plugin: self.attr( 'data-slug' ) };
      if ( self.hasClass( 'install-now' ) || self.hasClass( 'deactivate-now' ) ) {
        return;
      }
      e.preventDefault();

      jQuery.ajax( {
        beforeSend: function() {
          self.replaceWith( '<a class="button updating-message">' + welcomeScreen.activating_string + '...</a>' );
        },
        async: true,
        type: 'GET',
        dataType: 'html',
        url: self.attr( 'href' ),
        success: function( response ) {
          var actions = jQuery( '#plugin-filter' ).find( '.action-required-box' );

          if ( ! actions.length ) {
            location.reload();
          }

          jQuery( '.updating-message' ).removeClass( 'updating-message' ).parents( '.action-required-box' ).slideUp( 200 ).remove();
          actions = jQuery( '#plugin-filter' ).find( '.action-required-box' );

          jQuery( '.import-content-container' ).find( 'input[data-slug="' + self.attr( 'data-slug' ) + '"]' ).parent().remove();

          if ( ! actions.length ) {
            jQuery( '#plugin-filter' ).append( '<span class"hooray">' + welcomeScreen.no_actions + '</span>' );
          }

          jQuery( document ).trigger( 'epsilon-plugin-activated', dataToSend );
        }
      } );
    } );

    jQuery( document ).on( 'wp-plugin-install-success', function( response, data ) {
      var activateButton = jQuery( 'a[data-slug="' + data.slug + '"]' ),
          dataToSend = { plugin: data.slug };
      if ( activateButton.length && ( jQuery( 'body' ).hasClass( welcomeScreen.body_class ) || jQuery( 'body' ).hasClass( 'wp-customizer' ) ) ) {

        jQuery.ajax( {
          beforeSend: function() {
            activateButton.replaceWith( '<a class="button updating-message">' + welcomeScreen.activating_string + '...</a>' );
          },
          async: true,
          type: 'GET',
          dataType: 'html',
          url: data.activateUrl,
          success: function( response ) {
            var actions = jQuery( '#plugin-filter' ).find( '.action-required-box' );

            if ( ! actions.length ) {
              location.reload();
            }

            jQuery( '.updating-message' ).removeClass( 'updating-message' ).parents( '.action-required-box' ).slideUp( 200 ).remove();
            jQuery( document ).trigger( 'epsilon-plugin-activated', dataToSend );
          }
        } );
      }
    } );
  }
};

jQuery( document ).ready( function() {

  welcomeScreenFunctions.dismissAction();
  welcomeScreenFunctions.importDemoContent();
  welcomeScreenFunctions.activatePlugin();

  jQuery( '.epsilon-hidden-content-toggler' ).click( function( e ){
    var id = jQuery( this ).attr( 'href' );
    e.preventDefault();
    jQuery( id ).slideToggle();
  });

} );
