declare var wp: any;
declare var _: any;
import { EpsilonFieldRepeater } from '../repeater';
import { EpsilonRepeaterAddons } from './repeater-addons';
import { EpsilonTextEditor } from '../text-editor';
import { EpsilonRepeaterRow } from './repeater-row';
import { EpsilonRepeaterSectionRow } from './repeater-section-row';
import { EpsilonSectionRepeater } from '../section-repeater';

export class EpsilonRepeaterUtils {
  /**
   * PRotected variable
   */
  protected control: EpsilonFieldRepeater | EpsilonSectionRepeater;

  /**
   * Constructor
   * @param {EpsilonFieldRepeater | EpsilonSectionRepeater} control
   */
  public constructor( control: EpsilonFieldRepeater | EpsilonSectionRepeater ) {
    this.control = control;
  }

  /**
   * Deletes a row
   */
  public delete( row: EpsilonRepeaterRow, index: number ) {
    const self = this;
    let settings = this.getValue(),
        currentRow: EpsilonRepeaterRow,
        i: number,
        prop: number | any;

    if ( settings[ index ] ) {
      // Find the row
      row = this.control.rows[ index ];
      if ( row ) {

        // Remove the row settings
        delete settings[ index ];

        // Remove the row from the rows collection
        delete this.control.rows[ index ];
      }
    }

    settings = this.cleanArray( settings );
    this.control.rows = self.cleanArray( this.control.rows );

    jQuery.each( this.control.rows, function( index: number, element ) {
      self.setIndex( element, index );
    } );

    // Update the new setting values
    this.setValue( settings );

    // Remap the row numbers
    i = 1;
    for ( prop in this.control.rows ) {
      if ( this.control.rows.hasOwnProperty( prop ) && this.control.rows[ prop ] ) {
        self.updateLabel( this.control.rows[ prop ] );
        i ++;
      }
    }
  }

  /**
   * Adds a row
   */
  public add( data: { [key: number]: object } ): EpsilonRepeaterRow | boolean {
    const self = this;
    let template: any = _.memoize( this.control.template ),
        newSetting: any = {},
        templateData: {
          [key: number]: any,
          index: number
        },
        value: {
          [key: number]: any,
        } = this.getValue(),
        i: number | string,
        rowContainer: JQuery,
        row: any;

    /**
     * In case we don`t have a template, we terminate here
     */
    if ( ! template ) {
      return false;
    }

    /**
     * Extend template data with what we passed in PHP
     */
    templateData = jQuery.extend( true, {}, this.control.control.params.fields );

    /**
     * In case we added the row with "known" data, we need to overwrite the array
     */
    if ( data ) {
      for ( i in data ) {
        if ( data.hasOwnProperty( i ) && templateData.hasOwnProperty( i ) ) {
          templateData[ i ][ 'default' ] = data[ i ];
        }
      }
    }

    /**
     * Add an index
     *
     * @type {number}
     */
    templateData[ 'index' ] = this.control.currentIndex;

    /**
     * Render the HTML template with underscores
     */
    template = template( templateData );
    rowContainer = jQuery( template ).appendTo( this.control.repeaterContainer );
    row = new EpsilonRepeaterRow( this.control, rowContainer );

    /**
     * Bind events to the new row
     */
    this.handleRowEvents( row );

    /**
     * Register the new row in the control
     *
     * @type {*}
     */
    this.control.rows[ this.control.currentIndex ] = row;

    /**
     * Add a new "index" to the setting ( easier to render in the frontend )
     */
    for ( i in templateData ) {
      if ( templateData.hasOwnProperty( i ) ) {
        newSetting[ i ] = templateData[ i ][ 'default' ];
      }
    }

    if ( 'undefined' === typeof newSetting[ 'index' ] ) {
      newSetting[ 'index' ] = this.control.currentIndex;
    }

    /**
     * Add a value to the setting
     * @type {{}}
     */
    value[ this.control.currentIndex ] = newSetting;

    /**
     * Set it
     */
    if ( ! data ) {
      this.setValue( value );
    }

    this.control.handleRowIncrementor();
    return row;
  }

  /**
   * Sort fields
   */
  public sort( data: any ): void {
    const self = this;
    let rows = this.control.repeaterContainer.find( '.repeater-row' ),
        settings = this.getValue(),
        newOrder: Array<object> = [],
        newRows: Array<EpsilonRepeaterRow | EpsilonRepeaterSectionRow> = [],
        newSettings: Array<object> = [],
        textEditorSettings: any;

    rows.each( function( i: number, el: any ) {
      newOrder.push( jQuery( el ).data( 'row' ) );
    } );

    jQuery.each( newOrder, function( newPosition: any, oldPosition: any ) {
      newRows[ newPosition ] = self.control.rows[ oldPosition ];

      self.setIndex( newRows[ newPosition ], newPosition );
      newSettings[ newPosition ] = settings[ oldPosition ];
    } );

    let textareas: JQuery = rows.find( 'textarea' );
    for ( let i = 0; i < textareas.length; i ++ ) {
      setTimeout( function() {
        let settings: any = {
          container: jQuery( textareas[ i ] ).parent(),
          params: {
            id: jQuery( textareas[ i ] ).attr( 'id' )
          }
        };

        new EpsilonTextEditor( settings, true, true );
      }, 100 * i );

    }

    this.control.rows = newRows;
    this.setValue( newSettings );

    if ( self.control.control.params[ 'selective_refresh' ] ) {
      wp.customize.previewer.refresh();
    }
  }

  /**
   * Saves value
   */
  public setValue( value: { [key: number]: any } ) {
    this.control.control.setting.set( [] );
    this.control.control.setting.set( value );
  }

  /**
   * Gets the setting value
   */
  public getValue() {
    return this.control.control.setting.get();
  }

  /**
   * Update a single field inside a row.
   * Triggered when a field has changed
   */
  public updateField( instance: any, fieldId: string, element: JQuery ) {
    let row: EpsilonRepeaterRow | EpsilonRepeaterSectionRow = this.control.rows[ instance.index ],
        value: { [key: number]: any } = this.control.utils.getValue();

    this.updateRepeater( instance, fieldId, element );
  }

  /**
   * Updates a field repeater
   *
   * @param instance
   * @param {string} fieldId
   * @param {JQuery} element
   * @public
   */
  public updateRepeater( instance: any, fieldId: string, element: JQuery ) {
    let row: EpsilonRepeaterRow = this.control.rows[ instance.index ],
        value: { [key: number]: any } = this.control.utils.getValue();

    if ( ! row ) {
      return;
    }

    if ( ! this.control.control.params.fields[ fieldId ] ) {
      return;
    }

    if ( _.isUndefined( value[ row.index ][ fieldId ] ) ) {
      return;
    }

    switch ( this.control.control.params.fields[ fieldId ].type ) {
      case 'checkbox':
      case 'epsilon-toggle':
        value[ row.index ][ fieldId ] = jQuery( element ).prop( 'checked' );
        break;
      default:
        value[ row.index ][ fieldId ] = jQuery( element ).val();
        break;
    }

    this.control.utils.setValue( value );
  }

  /**
   * Load underscores template
   */
  public repeaterTemplate() {
    let compiled,
        options = {
          evaluate: /<#([\s\S]+?)#>/g,
          interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
          escape: /\{\{([^\}]+?)\}\}(?!\})/g,
          variable: 'data'
        };

    return function( data: any ) {
      compiled = _.template( jQuery( '.customize-control-epsilon-repeater-content-field' ).html(), null, options );
      return compiled( data );
    };
  }

  /**
   * Set the row's index
   */
  public setIndex( row: EpsilonRepeaterRow, index: number ): void {
    row.index = index;
    row.container.attr( 'data-row', index );
    row.container.data( 'row', index );
    this.control.utils.updateLabel( row );
  }

  /**
   * Removes a row from the container
   */
  public removeRow( row: EpsilonRepeaterRow ): void {
    row.container.slideUp( 300, function() {
      jQuery( this ).detach();
    } );
    row.container.trigger( 'row:remove', [ row.index ] );
  }

  /**
   * Update row label
   */
  public updateLabel( row: EpsilonRepeaterRow ): void {
    let rowLabelField: JQuery,
        rowLabel: string | any,
        rowLabelSelector;

    if ( 'field' === row.label.type ) {
      rowLabelField = row.container.find( '.repeater-field [data-field="' + row.label.field + '"]' );
      if ( _.isFunction( rowLabelField.val ) ) {
        rowLabel = rowLabelField.val();
        if ( '' !== rowLabel ) {
          if ( ! _.isUndefined( this.control.control.params.fields[ row.label.field ] ) ) {
            if ( ! _.isUndefined( this.control.control.params.fields[ row.label.field ].type ) ) {
              if ( 'select' === this.control.control.params.fields[ row.label.field ].type ) {
                if ( ! _.isUndefined( this.control.control.params.fields[ row.label.field ].choices ) &&
                    ! _.isUndefined( this.control.control.params.fields[ row.label.field ].choices[ rowLabel ] ) ) {
                  rowLabel = this.control.control.params.fields[ row.label.field ].choices[ rowLabel ];
                }
              } else if ( 'radio' === this.control.control.params.fields[ row.label.field ].type || 'radio-image' === this.control.control.params.fields[ row.label.field ].type ) {
                rowLabelSelector = this.control.control.selector + ' [data-row="' + row.index + '"] .repeater-field [data-field="' + row.label.field + '"]:checked';
                rowLabel = jQuery( rowLabelSelector ).val();
              }
            }
          }

          row.header.find( '.repeater-row-label' ).text( rowLabel );
          return;
        }
      }
    }

    row.header.find( '.repeater-row-label' ).text( row.label.value + ' ' + ( row.index + 1 ) );
  }

  /**
   * Toggles the row vizibility
   */
  public toggleMinimize( row: EpsilonRepeaterRow | EpsilonRepeaterSectionRow, section: boolean = false ): void {
    const self = this;
    if ( row.hasOwnProperty( 'type' ) ) {
      section = true;
    }

    row.container.find( '.repeater-row-content' ).slideToggle( 300, function() {
      row.container.toggleClass( 'minimized' );
      row.header.find( '.dashicons' ).toggleClass( 'dashicons-arrow-up' ).toggleClass( 'dashicons-arrow-down' );

      /**
       * In case we are in a section, we need to close others
       */
      if ( section ) {
        jQuery( 'body' ).removeClass( 'adding-section' );
        jQuery( 'body' ).removeClass( 'adding-doubled-section' );
        jQuery( '#sections-left-' + self.control.control.params.id ).find( '.available-sections' ).removeClass( 'opened' );

        jQuery.each( row.container.siblings(), function( index: number, element: any ) {
          if ( ! jQuery( element ).hasClass( 'minimized' ) ) {
            jQuery( element ).addClass( 'minimized' );
            jQuery( element ).find( '.repeater-row-content' ).slideToggle( 300, function() {
              jQuery( element ).
                  find( 'repeater-row-header' ).
                  addClass( 'minimized' ).
                  find( '.dashicons' ).
                  toggleClass( 'dashicons-arrow-up' ).
                  toggleClass( 'dashicons-arrow-down' );
            } );
          }
        } );
      }

    } );
  }

  /**
   * Cleans an arrow of undefined values
   */
  public cleanArray( obj: Array<EpsilonRepeaterSectionRow | EpsilonRepeaterRow> ): Array<EpsilonRepeaterSectionRow | EpsilonRepeaterRow> {
    let arr: Array<EpsilonRepeaterSectionRow | EpsilonRepeaterRow> = [];
    for ( let i = 0; i < obj.length; i ++ ) {
      if ( obj[ i ] ) {
        arr.push( obj[ i ] );
      }
    }

    return arr;
  }

  /**
   * Handle row events
   */
  public handleRowEvents( row: EpsilonRepeaterRow ): void {
    const self = this;
    /**
     * 1. Remove row event
     */
    row.container.on( 'row:remove', function( this: any, e: any, index: number ) {
      self.delete( row, index );

      if ( 'epsilon-section-repeater' === self.control.control.params.type ) {
        jQuery( 'body' ).removeClass( 'adding-doubled-section' );
      }

      if ( self.control.control.params[ 'selective_refresh' ] ) {
        wp.customize.previewer.refresh();
      }
    } );

    /**
     * 2. Initiate sortable script
     */
    row.header.on( 'mousedown', function() {
      row.container.trigger( 'row:start-dragging' );
    } );
  }
}