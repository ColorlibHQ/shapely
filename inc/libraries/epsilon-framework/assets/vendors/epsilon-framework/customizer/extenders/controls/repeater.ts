declare var wp: any;

import { EpsilonFieldRepeater } from '../../controls/repeater';

wp.customize.controlConstructor[ 'epsilon-repeater' ] = wp.customize.Control.extend( {
  ready: function() {
    var control: any = this;
    new EpsilonFieldRepeater( control );
  }
} );