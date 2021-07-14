declare var wp: any;

import { EpsilonSectionRepeater } from '../../controls/section-repeater';

wp.customize.controlConstructor[ 'epsilon-section-repeater' ] = wp.customize.Control.extend( {
  ready: function() {
    let control: any = this,
        id: string | any = jQuery( this.container ).attr( 'id' ),
        node: Node | null = document.getElementById( id ),
        observer = new MutationObserver( function( mutations ) {
          let element = mutations[ mutations.length - 1 ];

          if ( jQuery( element.target ).is( ':visible' ) && ! jQuery( element.target ).hasClass( 'epsilon-section-repeater-initiated' ) ) {
            jQuery( element.target ).addClass( 'epsilon-section-repeater-initiated' );
            new EpsilonSectionRepeater( control );
          }
        } );

    if ( this.container.is( ':visible' ) ) {
      this.container.addClass( 'epsilon-section-repeater-initiated' );
      new EpsilonSectionRepeater( control );
    }

    if ( null !== node ) {
      observer.observe( node, { attributes: true, childList: false, attributeFilter: [ 'style' ] } );
    }
  }
} );