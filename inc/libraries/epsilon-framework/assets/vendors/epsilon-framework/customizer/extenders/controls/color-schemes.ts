declare var wp: any;

import { EpsilonColorSchemes } from '../../controls/color-schemes';

/**
 * Epsilon Color Schemes Control Constructor
 */
wp.customize.controlConstructor[ 'epsilon-color-scheme' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this, section, instance;

    wp.customize.bind( 'ready', function() {
      new EpsilonColorSchemes( control );
    } );

    /**
     * Save the typography
     */
    control.container.on( 'change', '.epsilon-color-scheme-input',
        function( e: Event ) {
          control.setting.set( jQuery( e.target ).val() );
        }
    );
  }
} );


