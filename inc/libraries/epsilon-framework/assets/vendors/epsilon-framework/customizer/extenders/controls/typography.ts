declare var wp: any;

import { EpsilonTypography } from '../../controls/typography';

/**
 * Epsilon Typography Control Constructor
 */
wp.customize.controlConstructor[ 'epsilon-typography' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;

    new EpsilonTypography( control );

    /**
     * Save the typography
     */
    control.container.on( 'change', '.customize-control-content > .epsilon-typography-input',
        function( e: Event ) {
          control.setting.set( jQuery( e.target ).val() );
        }
    );
  }
} );