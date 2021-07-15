/**
 * Espilon Color Picker Module
 */
export class EpsilonColorPicker {
  /**
   * Context
   */
  context: JQuery | any;
  /**
   * Control
   */
  control: any;
  /**
   * Settings array
   */
  settings: {};
  /**
   * Color Picker instance
   */
  instance: JQuery | any;

  /**
   * Class Constructor
   * @param {{container: JQuery; setting: void; params: {value: number; id: string}}} control
   */
  public constructor( control: { container: JQuery, setting: void, params: { value: number, id: string } } ) {
    const self = this;
    let clear: JQuery;
    this.control = control;
    this.context = jQuery( control.container ).find( '.epsilon-color-picker' );
    this.settings = {
      changeDelay: 500,
      theme: 'default',
      change: this.changePallete,
    };

    if ( 'function' !== typeof jQuery.fn.minicolors ) {
      return;
    }

    if ( '' !== this.context.attr( 'placeholder' ) ) {
      this.context.defaultValue = this.context.attr( 'placeholder' );
    }

    if ( 'rgba' === this.context.attr( 'data-attr-mode' ) ) {
      this.context.format = 'rgb';
      this.context.opacity = true;
    }

    this.context.minicolors( this.settings );

    clear = this.context.parents( '.customize-control-epsilon-color-picker' ).find( 'a' );
    if ( ! clear.length ) {
      clear = this.context.parents( '.repeater-field-epsilon-color-picker' ).find( 'a' );
    }

    clear.on( 'click', function( e ) {
      e.preventDefault();
      self.instance = jQuery( this ).parents( '.customize-control-epsilon-color-picker' ).find( 'input.epsilon-color-picker' );
      if ( ! self.instance.length ) {
        self.instance = jQuery( this ).parents( '.repeater-field-epsilon-color-picker' ).find( 'input.epsilon-color-picker' );
      }

      self.instance.minicolors( 'value', jQuery( this ).attr( 'data-default' ) );
      self.instance.trigger( 'change' );
    } );
  }

  /**
   * Real time changes to the "pallete"
   *
   * @param value
   * @param opacity
   */
  public changePallete( value: any, opacity: any ) {
    jQuery( '.epsilon-color-scheme-selected' ).find( '*[data-field-id="' + jQuery( this ).attr( 'data-customize-setting-link' ) + '"]' ).css( 'background-color', value );
  }
}
