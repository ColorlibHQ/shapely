declare var wp: any;
import { EpsilonTextEditor } from '../../controls/text-editor';

wp.customize.controlConstructor[ 'epsilon-text-editor' ] = wp.customize.Control.extend( {
  ready: function() {
    var control = this;

    new EpsilonTextEditor( control );

    control.container.on( 'change keyup', 'textarea', function( event: Event ) {
      control.setting.set( jQuery( event.target ).val() );
    } );
  }
} );