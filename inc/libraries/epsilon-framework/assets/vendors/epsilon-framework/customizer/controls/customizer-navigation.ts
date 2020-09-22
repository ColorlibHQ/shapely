import EventHandler = JQuery.EventHandler;

declare var wp: any;

/**
 * Espilon Customizer Navigation Module
 */
export class EpsilonCustomizerNavigation {
  /**
   * Context
   */
  context: JQuery | any;
  /**
   * Actual control
   */
  control: any;
  /**
   * Multiple flag
   */
  multiple: boolean;

  /**
   * Class Constructor
   * @param {{container: JQuery; params: {value: number; id: string}}} control
   */
  public constructor( control: { container: JQuery, params: { value: number, id: string } } ) {
    this.context = jQuery( control.container );
    this.multiple = this.context.find( '.epsilon-customizer-navigation-container' ).length > 1;

    if ( this.multiple ) {
      this.initMultiple();
    } else {
      this.init();
    }
  }

  /**
   * Init multiple fields
   */
  public initMultiple() {
    let containers = this.context.find( '.epsilon-customizer-navigation-container:not(.initiated)' ).first().addClass( 'initiated' );
    containers.map( function( index: number, element: any ) {
      jQuery( element ).find( 'a' ).on( 'click', function( this: any, e: JQueryEventConstructor ) {
        e.preventDefault();
        if ( 'undefined' !== typeof(wp.customize.section( jQuery( this ).attr( 'data-customizer-section' ) )) ) {
          if ( jQuery( e.target ).attr( 'data-doubled' ) ) {
            wp.customize.section( jQuery( this ).attr( 'data-customizer-section' ) ).headContainer.trigger( 'click' );
          } else {
            wp.customize.section( jQuery( this ).attr( 'data-customizer-section' ) ).focus();
          }
        }
      } );
    } );
  }

  /**
   * Control initiator
   */
  public init() {
    let navigation = this.context.find( '.epsilon-customizer-navigation-container' );
    if ( navigation.length ) {
      navigation.on( 'click', navigation.find( 'a' ), function( this: any, e: Event ) {
        e.preventDefault();
        if ( 'undefined' !== typeof(wp.customize.section( jQuery( e.target ).attr( 'data-customizer-section' ) )) ) {
          if ( jQuery( e.target ).attr( 'data-doubled' ) ) {
            wp.customize.section( jQuery( e.target ).attr( 'data-customizer-section' ) ).headContainer.trigger( 'click' );
          } else {
            wp.customize.section( jQuery( e.target ).attr( 'data-customizer-section' ) ).focus();
          }
        }
      } );
    }
  }
}
