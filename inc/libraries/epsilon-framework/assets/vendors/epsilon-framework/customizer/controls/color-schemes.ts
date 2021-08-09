declare var _: any;
declare var wp: any;

/**
 * Espilon Color Schemes Module
 */
export class EpsilonColorSchemes {
  /**
   * Context
   */
  context: JQuery | any;
  /**
   * Actual control
   */
  control: any;

  /**
   * Class Constructor
   * @param {{container: JQuery; params: {value: number; id: string}}} control
   */
  public constructor( control: { container: JQuery, params: { value: number, id: string, default: string, stylesheet: string } } ) {
    this.control = control;
    this.context = this.control.container.find( '.epsilon-color-scheme' );

    if ( ! this.context.length ) {
      return;
    }

    this.init();
  }

  /**
   * Initiator
   */
  public init() {
    const self = this;
    /**
     * Set variables
     */
    let options = this.context.find( '.epsilon-color-scheme-option' ),
        input = this.context.parent().find( '[data-customize-setting-link]' ).first(),
        json = jQuery.parseJSON( options.first().find( 'input' ).val() ),
        api = wp.customize,
        colorSettings: Array<string> = [],
        css: { action: string, class: string, id: string, data: any } = {
          action: 'epsilon_generate_color_scheme_css',
          class: 'Epsilon_Color_Scheme',
          id: '',
          data: {}
        };

    jQuery.each( json, function( index, value ) {
      colorSettings.push( index );
    } );

    _.each( colorSettings, function( setting: string ) {
      css.data[ setting ] = api( setting )();

      if ( 'undefined' !== typeof api.control( setting ) ) {
        api.control( setting ).container.on( 'change', 'input.epsilon-color-picker', _.debounce( function( this: any ) {
          self.context.siblings( '.epsilon-color-scheme-selected' ).
              find( '.epsilon-color-scheme-palette' ).
              find( '*[data-field-id="' + setting + '"]' ).
              css( 'background', jQuery( this ).attr( 'value' ) );

          css.data[ setting ] = api( setting )();

          api.previewer.send( 'update-inline-color-schemes-css', css );
        }, 800 ) );
      }

    } );

    /**
     * On clicking a color scheme, update the color pickers
     */
    options.on( 'click', function( this: any ) {
      let val = jQuery( this ).attr( 'data-color-id' ),
          json: any = jQuery( this ).find( 'input' ).val();

      if ( json ) {
        json = jQuery.parseJSON( json );
      }

      /**
       * Find the customizer options
       */
      jQuery.each( json, function( index, value ) {
        /**
         * Set values
         */
        jQuery( '#customize-control-' + index + ' .epsilon-color-picker' ).minicolors( 'value', value );
        api( index ).set( value );

        self.context.siblings( '.epsilon-color-scheme-selected' ).find( '.epsilon-color-scheme-palette' ).find( '*[data-field-id="' + index + '"]' ).css( 'background', value );
      } );

      /**
       * Remove the selected class from siblings
       */
      jQuery( this ).siblings( '.epsilon-color-scheme-option' ).removeClass( 'selected' );
      /**
       * Make active the current selection
       */
      jQuery( this ).addClass( 'selected' );

      /**
       * Trigger change
       */
      input.val( val ).trigger( 'change' );
    } );

    /**
     * Advanced toggler
     */
    jQuery( '.epsilon-control-dropdown' ).on( 'click', function( this: any ) {
      jQuery( this ).toggleClass( 'active' );
      jQuery( this ).find( 'span' ).toggleClass( 'dashicons-arrow-down dashicons-arrow-up' );
      self.context.slideToggle();
    } );
  }
}
