declare var wp: any;

import { EpsilonIconPicker } from '../../controls/icon-picker';

wp.customize.controlConstructor[ 'epsilon-icon-picker' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;

    new EpsilonIconPicker( control, false );

    control.container.on( 'change', 'input.epsilon-icon-picker',
        function( event: Event ) {
          control.setting.set( jQuery( event.target ).val() );
        }
    );
  }
} );