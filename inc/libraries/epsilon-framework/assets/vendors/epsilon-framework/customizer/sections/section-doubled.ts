export class EpsilonSectionDoubled {
  /**
   * Context
   */
  context: JQuery | any;
  /**
   * Control
   */
  section: any;
  /**
   * Parent container
   */
  parent: JQuery;

  /**
   * Class Constructor
   * @param {{section: JQuery; params: { id: string}}} control
   */
  constructor( section: { container: JQuery, params: { id: string } } ) {
    /**
     * save instance of section
     */
    this.section = section;
    /**
     * Move out of the ugly list, this has overflow hidden and we can`t display it properly
     */
    this.createParent();

    /**
     * Append new sections to parent
     */
    this.section.container.appendTo( this.parent );

    /**
     * Handle Events
     */
    this.handleEvents();
  }

  private handleEvents(): void {
    const self = this;
    let section = this.section;

    /**
     * Close sections
     */
    section.container.on( 'click', '.epsilon-close-doubled-section', function( e: Event ): void {
      e.preventDefault();
      jQuery( 'body' ).removeClass( 'adding-doubled-section' );
      jQuery( 'body' ).find( '.doubled-section-opened' ).removeClass( 'doubled-section-opened' );
    } );

    /**
     * Open sections
     */
    section.headContainer.on( 'click', function( this: any, e: Event ): void {
      let opened: JQuery, strippedIdHead: string | any, strippedIdContainer: string | any;
      e.preventDefault();

      /**
       * We need to close everything on click
       * @type {*|{}}
       */
      opened = self.parent.find( '.doubled-section-opened' );
      if ( opened.length ) {
        opened.removeClass( 'doubled-section-opened' );
      }

      if ( jQuery( 'body' ).hasClass( 'adding-doubled-section' ) ) {
        strippedIdHead = jQuery( this ).attr( 'id' );

        if ( 'undefined' !== typeof strippedIdHead ) {
          strippedIdHead = strippedIdHead.replace( 'accordion-section-', '' );
        }

        strippedIdContainer = opened.attr( 'id' );
        if ( 'undefined' !== typeof strippedIdContainer ) {
          strippedIdContainer = strippedIdContainer.replace( 'sub-accordion-section-', '' );
        }

        if ( strippedIdContainer === strippedIdHead ) {
          jQuery( 'body' ).removeClass( 'adding-doubled-section' );
        }
      } else {
        jQuery( 'body' ).addClass( 'adding-doubled-section' );
      }

      jQuery.each( section.container, function( this: any ): void {
        if ( jQuery( this ).is( 'li' ) ) {
          return;
        }
        jQuery( this ).addClass( 'doubled-section-opened' );
      } );

    } );
  }

  /**
   * Create a parent container
   */
  private createParent(): void {
    let parent = jQuery( '.wp-full-overlay' ).find( '.doubled-section-parent' );
    if ( ! parent.length ) {
      jQuery( '.wp-full-overlay' ).append( '<div class="doubled-section-parent"></div>' );
      parent = jQuery( '.wp-full-overlay' ).find( '.doubled-section-parent' );
      this.parent = parent;
    }

    this.parent = parent;
  }
}
