import Slider = JQueryUI.Slider;
/**
 * Espilon Range Slider Module
 */
export class EpsilonRangeSlider {
  /**
   * Object Context
   */
  context: JQuery;
  /**
   * Control value
   */
  value: number;
  /**
   * Minimum
   * @type {number}
   */
  min: number = 1;
  /**
   * Maximum
   * @type {number}
   */
  max: number = 20;
  /**
   * Step incrementor
   * @type {number}
   */
  step: number = 1;

  /**
   * Class constructor
   * @param {{container: JQuery; params: {value: number; id: string; sliderControls: {min: number; max: number; step: number}}}} control
   */
  public constructor( control: { container: JQuery, params: { value: number, id: string, sliderControls: { min: number, max: number, step: number } } } ) {
    this.context = jQuery( control.container ).hasClass( 'slider-container' ) ? jQuery( control.container ) : jQuery( control.container ).find( '.slider-container' );
    this.min = control.params.sliderControls.min;
    this.max = control.params.sliderControls.max;
    this.step = control.params.sliderControls.step;
    this.value = control.params.value;
    if ( ! this.context ) {
      return;
    }

    this.init();
  }

  /**
   * Initiator
   */
  public init() {
    const self = this;
    let slider = this.context.find( '.ss-slider' ),
        input = this.context.find( '.rl-slider' ),
        inputId = input.attr( 'id' ),
        id = slider.attr( 'id' );

    jQuery( '#' + id ).slider( {
      value: this.value,
      range: 'min',
      min: this.min,
      max: this.max,
      step: this.step,
      /**
       * Removed Change event because server was flooded with requests from
       * javascript, sending changesets on each increment.
       *
       * @param event
       * @param ui
       */
      slide: function( event: Event, ui: { value: number } ) {
        jQuery( '#' + inputId ).attr( 'value', ui.value );
      },
      /**
       * Bind the change event to the "actual" stop
       * @param event
       * @param ui
       */
      stop: function( event: Event, ui: { value: number } ) {
        jQuery( '#' + inputId ).trigger( 'change' );
      }
    } );

    jQuery( input ).on( 'focus', function() {
      jQuery( this ).blur();
    } );

    jQuery( '#' + inputId ).attr( 'value', ( jQuery( '#' + id ).slider( 'value' ) ) );
  }
}
