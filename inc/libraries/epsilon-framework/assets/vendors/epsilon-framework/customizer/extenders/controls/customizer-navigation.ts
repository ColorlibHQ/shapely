declare var wp: any;

import { EpsilonCustomizerNavigation } from '../../controls/customizer-navigation';

wp.customize.controlConstructor[ 'epsilon-customizer-navigation' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;
    new EpsilonCustomizerNavigation( control );
  }
} );