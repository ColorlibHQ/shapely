declare var wp: any;

import { EpsilonColorPicker } from '../../controls/color-picker';

wp.customize.controlConstructor[ 'epsilon-color-picker' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;

    new EpsilonColorPicker( control );

    control.container.on( 'change', 'input.epsilon-color-picker',
        function( e: Event ) {
          control.setting.set( jQuery( e.target ).val() );
        }
    );
  }
} );