/**
 * Espilon Layouts Module
 */
export class EpsilonLayouts {
  /**
   * Context
   */
  context: JQuery | any;
  /**
   * Control
   */
  control: any;
  /**
   * Buttons
   */
  html: { [key: string]: string } = {
    buttonLeft: '<a href="#" data-action="left"><span class="dashicons dashicons-arrow-left"></span> </a>',
    buttonRight: '<a href="#" data-action="right"><span class="dashicons dashicons-arrow-right"></span> </a>',
  };

  /**
   * Layout Buttons
   */
  layoutButtons: JQuery | any;
  /**
   * Resize Buttons
   */
  resizeButtons: JQuery | any;
  /**
   * Maximum number of columns
   */
  maxColumns: number;
  /**
   * Minimum span
   */
  minSpan: number;
  /**
   * Number of active columns
   */
  activeColumns: number | null = null;
  /**
   * Last column state
   */
  lastColumnsState: any;
  /**
   * Redundant constant for columns
   */
  colClasses: string = 'col12 col11 col10 col9 col8 col7 col6 col5 col4 col3 col2 col1';
  /**
   * Customizer link
   */
  dataLink: JQuery;

  /**
   * Class Constructor
   * @param {{container: JQuery; setting: void; params: {value: number; id: string}}} control
   */
  public constructor( control: { container: JQuery, setting: void, params: { value: number, id: string, minSpan: string } } ) {
    this.control = control;
    this.context = this.control.container;
    this.layoutButtons = this.context.find( '.epsilon-control-group > a' );
    this.resizeButtons = this.context.find( '.epsilon-layouts-setup > .epsilon-column > a' );
    this.maxColumns = this.layoutButtons.length;
    this.minSpan = parseFloat( this.control.params.minSpan );
    this.lastColumnsState = null;
    this.dataLink = this.context.find( '[data-customize-setting-link]' ).first();

    /**
     * Handle actions
     */
    this.handle_actions();
    /**
     * Whenever the column count or size changes, we save data to the hidden field
     */
    this.context.on( 'epsilon_column_count_changed epsilon_column_size_changed', this._save );
  }

  /**
   * Handle the click events in the control
   */
  public handle_actions() {
    /**
     * Hide / show columns
     */
    this._advanced_toggler();
    /**
     * Column resize event ( + / - buttons )
     */
    this._column_resize();
    /**
     * Addition removal of columns events
     */
    this._column_recount();
    this._layout_select();
    this._equalize_columns();
  }

  /**
   * Advanced toggler
   *
   * @private
   */
  private _advanced_toggler() {
    /**
     * On clicking the advanced options toggler,
     */
    this.context.on( 'click', '.epsilon-control-advanced', function( this: any, e: Event ) {
      e.preventDefault();
      jQuery( this ).toggleClass( 'active' );
      jQuery( '#' + jQuery( this ).attr( 'data-unique-id' ) ).slideToggle().addClass( 'active' );
    } );
  }

  /**
   * Column Resizer
   * @private
   */
  private _column_resize() {
    const self = this;
    let position,
        elementToSubtractFrom,
        elementToAddOn;

    this.context.on( 'click', '.epsilon-column > a', function( this: any, e: Event ) {
      elementToAddOn = jQuery( this ).parent();
      position = elementToAddOn.index();

      if ( 'right' === jQuery( this ).attr( 'data-action' ) ) {
        elementToSubtractFrom = self.context.find( '.epsilon-layouts-setup > .epsilon-column' ).eq( position + 1 );
      } else {
        elementToSubtractFrom = self.context.find( '.epsilon-layouts-setup > .epsilon-column' ).eq( position - 1 );
      }

      self._calc_column_resize( elementToSubtractFrom, elementToAddOn );
    } );
  }

  /**
   * Handle addition/removal of columns
   * @private
   */
  private _column_recount() {
    const self = this;
    let columns, operation, i, j;
    this.context.on( 'epsilon_column_count_change', function( this: any, e: { columns: { selected: any, beforeSelection: any } } ) {
      /**
       * Update instance variables
       */
      self.activeColumns = parseFloat( e.columns.selected );
      self.lastColumnsState = e.columns.beforeSelection;

      /**
       * In case we don't have anything to modify, we can terminate here
       */
      if ( self.activeColumns === self.lastColumnsState ) {
        return;
      }

      /**
       * Are we adding or subtrating?
       */
      operation = self.lastColumnsState < self.activeColumns ? 'adding' : 'subtracting';
      i = self.activeColumns - self.lastColumnsState;

      if ( 'subtracting' === operation ) {
        self.context.find( '.epsilon-layouts-setup > .epsilon-column' ).
            slice( - ( self.lastColumnsState - self.activeColumns ) ).
            remove();
      } else {
        for ( j = 0; j < i; j ++ ) {
          self.context.find( '.epsilon-layouts-setup' ).
              append(
                  '<div class="epsilon-column col4">' +
                  self.html.buttonLeft +
                  self.html.buttonRight +
                  '</div>' );
        }
      }

      /**
       * Trigger event to changed
       */
      self.context.trigger( {
        type: 'epsilon_column_count_changed',
        instance: self
      } );
    } );
  }

  /**
   * When selecting a layout, recalc/remove/readd divs in the container
   *
   * @private
   */
  private _layout_select() {
    const self = this;
    let columns;

    this.layoutButtons.on( 'click', function( this: any, e: Event ) {
      e.preventDefault();
      let selected: any = jQuery( this ).attr( 'data-button-value' );
      /**
       * Handle addition/deletion through jQuery events
       */
      self.context.trigger( {
        type: 'epsilon_column_count_change',
        columns: {
          selected: parseFloat( selected ),
          beforeSelection: self.context.find( '.epsilon-layouts-setup > .epsilon-column' ).length
        }
      } );

      /**
       * Visual changes
       */
      jQuery( this ).addClass( 'active' ).siblings( 'a' ).removeClass( 'active' );
    } );
  }

  /**
   * Equalize coolumns, this is happening after a new layout is selected
   * @private
   */
  private _equalize_columns() {
    const self = this;

    this.context.on( 'epsilon_column_count_changed', function( this: any, e: Event ) {
      switch ( self.activeColumns ) {
        case 2:
          self.context.find( '.epsilon-column' ).removeClass( self.colClasses );
          self.context.find( '.epsilon-column' ).first().addClass( 'col8' ).attr( 'data-columns', ( 8 ) );
          self.context.find( '.epsilon-column' ).last().addClass( 'col4' ).attr( 'data-columns', ( 4 ) );
          break;
        default:
          if ( null === self.activeColumns ) {
            return;
          }

          self.context.find( '.epsilon-column' ).
              removeClass( self.colClasses ).
              addClass( 'col' + ( 12 / self.activeColumns ) ).
              attr( 'data-columns', ( 12 / self.activeColumns ) );
          break;
      }
    } );
  }

  /**
   * Change spans accordingly
   *
   * @param subtract
   * @param add
   */
  private _calc_column_resize( subtract: JQuery, add: JQuery ) {
    let columns: any = subtract.attr( 'data-columns' ),
        addColumns: any;

    if ( 'undefined' === typeof columns ) {
      return;
    }

    columns = parseFloat( columns );

    if ( parseFloat( columns ) === this.minSpan ) {
      return;
    }
    addColumns = add.attr( 'data-columns' );
    addColumns = parseFloat( addColumns );

    subtract.attr( 'data-columns', columns - 1 ).removeClass( this.colClasses ).addClass( 'col' + (columns - 1) );
    add.attr( 'data-columns', addColumns + 1 ).removeClass( this.colClasses ).addClass( 'col' + (addColumns + 1) );

    /**
     * Trigger event to change
     */
    this.context.trigger( {
      type: 'epsilon_column_size_changed',
      instance: this
    } );
  }

  /**
   * Save state in a json
   * @private
   */
  private _save( e: { instance: EpsilonLayouts } ) {
    /**
     * Save interface
     */
    interface Column {
      index: number,
      span: number,
    }

    interface Columns {
      [key: number]: Column
    }

    interface columnsInterface {
      columnsCount: number | any;
      columns: Columns;
    }

    let json: columnsInterface = {
      columnsCount: e.instance.activeColumns,
      columns: {}
    };

    jQuery.each( e.instance.context.find( '.epsilon-column' ), function( index: number ) {
      let columns: any = jQuery( this ).attr( 'data-columns' );

      if ( 'undefined' === typeof columns ) {
        return;
      }

      columns = parseFloat( columns );

      let Obj: Column = {
        index: index + 1,
        span: columns
      };

      json.columns[ index + 1 ] = Obj;
    } );

    if ( null === json.columnsCount ) {
      json.columnsCount = e.instance.context.find( '.epsilon-column' ).length;
    }

    e.instance.dataLink.val( JSON.stringify( json ) ).trigger( 'change' );
  }
}
