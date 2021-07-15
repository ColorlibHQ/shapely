declare var wp: any;

import { EpsilonImage } from '../../controls/image';

wp.customize.controlConstructor[ 'epsilon-image' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;
    new EpsilonImage( control );
  }
} );
