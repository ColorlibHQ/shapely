/**
 * Espilon Button Group
 */
export class EpsilonButtonGroup {
  /**
   * Control instance
   */
  protected control: any;
  /**
   * Context
   */
  protected context: JQuery;

  /**
   * Class constructor
   * @param {{container: JQuery; setting: void; params: {value: number; id: string}}} control
   */
  public constructor( control: { container: JQuery, setting: void, params: { value: number, id: string } } ) {
    this.control = control;
    this.context = control.container;

    this.handleEvents();
  }

  /**
   * Handle click events
   */
  public handleEvents() {
    const self = this;
    this.context.on( 'click', '.epsilon-control-group > a', function( this: any, e: JQueryEventConstructor ) {
      e.preventDefault();
      let value: any = jQuery( this ).attr( 'data-value' );
      jQuery( this ).siblings().removeClass( 'active' );
      jQuery( this ).addClass( 'active' );

      if ( ! self.control.hasOwnProperty( 'repeater' ) ) {
        self.control.setting.set( jQuery( this ).attr( 'data-value' ) );
      } else {
        jQuery(this).parent().find( 'input' ).val( value ).trigger( 'change' );
      }
    } );
  }
}
