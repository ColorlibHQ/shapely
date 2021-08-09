declare var wp: any;

import { EpsilonLayouts } from '../../controls/layouts';

wp.customize.controlConstructor[ 'epsilon-layouts' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;
    new EpsilonLayouts( control );

    /**
     * Save the layout
     */
    control.container.on( 'change', 'input', function( e: Event ) {
      control.setting.set( jQuery( e.target ).val() );
    } );
  }
} );

