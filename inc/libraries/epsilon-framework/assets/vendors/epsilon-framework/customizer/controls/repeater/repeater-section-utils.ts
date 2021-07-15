declare var wp: any;
declare var _: any;
import { EpsilonRepeaterUtils } from './repeater-utils';
import { EpsilonRepeaterSectionRow } from './repeater-section-row';
import { EpsilonFieldRepeater } from '../repeater';
import { EpsilonSectionRepeater } from '../section-repeater';

export class EpsilonRepeaterSectionUtils extends EpsilonRepeaterUtils {
  /**
   * constructor
   * @param {EpsilonFieldRepeater | EpsilonSectionRepeater} control
   */
  public constructor( control: EpsilonFieldRepeater | EpsilonSectionRepeater ) {
    super( control );
  }

  /**
   * Add button
   * @param instance
   */
  public addButton() {
    const self = this;
    let isAddBtn,
        sections = jQuery( '#sections-left-' + self.control.control.params.id ).find( '.available-sections' ),
        body = jQuery( 'body' );

    /**
     * Get a reference for the parent section, if we close it. we must close the Section sidebar as well
     */
    wp.customize[ 'section' ]( this.control.control.params.section, function( instance: any ) {
      instance.container.find( '.accordion-section-title, .customize-section-back' ).on( 'click keydown', function( event: Event ) {
        if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
          return;
        }

        /**
         * In case we left the "sections" screen, let's close all the repeatable sections
         */
        _.each( self.control.rows, function( sect: EpsilonRepeaterSectionRow ) {
          if ( ! sect.container.hasClass( 'minimized' ) ) {
            self.toggleMinimize( sect, true );
          }
        } );

        body.removeClass( 'adding-section' );
        body.find( '.doubled-section-opened' ).removeClass( 'doubled-section-opened' );
        sections.removeClass( 'opened' );
      } );
    } );

    this.control.context.find( '.epsilon-add-new-section' ).on( 'click keydown', function( e: Event ) {
      if ( jQuery( 'body' ).hasClass( 'adding-doubled-section' ) ) {
        return;
      }

      isAddBtn = jQuery( e.target ).is( '.epsilon-add-new-section' );

      body.toggleClass( 'adding-section' );
      sections.toggleClass( 'opened' );
      if ( body.hasClass( 'adding-section' ) && ! isAddBtn ) {
        self.control.control.close();
      }
    } );
  }

  /**
   * Overwrite base method
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
      compiled = _.template( jQuery( '.customize-control-epsilon-repeater-content-section' ).html(), null, options );
      return compiled( data );
    };
  }

  /**
   * Overwrite base method
   */
  public add( data: any ): EpsilonRepeaterSectionRow | boolean {
    const self = this;
    let template: any = _.memoize( this.control.template ),
        newSetting: any = {},
        templateData: any,
        value: {
          [key: number]: any,
        } = self.getValue(),
        i: number | string,
        rowContainer: JQuery,
        row: any,
        fields: any;

    /**
     * In case we don`t have a template, we terminate here
     */
    if ( ! template ) {
      return false;
    }

    /**
     * Extend template data with what we passed in PHP
     */
    if ( 'undefined' === typeof ( this.control.control.params.sections[ data.type ] ) ) {
      return false;
    }

    /**
     * Form the new fields with the static ones
     */
    fields = jQuery.extend( true, {}, this.control.control.params.sections[ data.type ].fields, this.control.control.params.sections[ data.type ].customization.styling,
        this.control.control.params.sections[ data.type ].customization.layout );

    /**
     * Extend template data with what we passed in PHP
     */
    templateData = jQuery.extend( true, {}, fields, { customization: this.control.control.params.sections[ data.type ].customization } );

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
    row = new EpsilonRepeaterSectionRow( this.control, rowContainer, data.type );

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

    newSetting.type = data.type;

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
    if ( 1 === _.size( data ) && data.hasOwnProperty( 'type' ) ) {
      self.setValue( value );
    }

    this.control.handleRowIncrementor();
    return row;
  }

  /**
   * Override parent method
   */
  public updateLabel( section: EpsilonRepeaterSectionRow ) {
    section.header.find( '.repeater-row-label' ).html( '<span class="repeater-index">#' + ( section.index + 1 ) + ' - </span>' + section.label );
  };

  /**
   * Update a section repeater
   * @param instance
   * @param {string} fieldId
   * @param {JQuery} element
   * @public
   */
  public updateRepeater( instance: any, fieldId: string, element: JQuery ) {
    const self = this;
    let row: EpsilonRepeaterSectionRow | any = this.control.rows[ instance.index ],
        value: { [key: number]: any } = this.getValue(),
        section: EpsilonRepeaterSectionRow,
        type: string,
        data: any;

    if ( ! row ) {
      return;
    }

    if ( _.isUndefined( value[ row.index ][ fieldId ] ) ) {
      return;
    }

    type = this.getFieldGroup( fieldId, row.type );
    switch ( type ) {
      case 'checkbox':
      case 'epsilon-toggle':
        value[ row.index ][ fieldId ] = jQuery( element ).prop( 'checked' );
        break;
      default:
        value[ row.index ][ fieldId ] = jQuery( element ).val();
        break;
    }

    if ( self.control.control.params[ 'selective_refresh' ] ) {
      /**
       * Partial refresh
       * @type {{control; postId; index: any; value: any}}
       */
      data = {
        control: this.control.control.id,
        postId: this.control.control.params[ 'save_as_meta' ],
        value: value[ row.index ],
        index: row.index,
      };

      wp.customize.previewer.send( 'updated-section-repeater', data );
    }

    this.setValue( value );
  }

  /**
   *
   * @param instance
   * @param sectionType
   */
  public getFieldGroup( fieldId: string, sectionType: string ) {
    if ( this.control.control.params.sections[ sectionType ].fields[ fieldId ] ) {
      return this.control.control.params.sections[ sectionType ].fields[ fieldId ].type;
    }

    if ( this.control.control.params.sections[ sectionType ].customization.styling[ fieldId ] ) {
      return this.control.control.params.sections[ sectionType ].customization.styling[ fieldId ].type;
    }

    if ( this.control.control.params.sections[ sectionType ].customization.layout[ fieldId ] ) {
      return this.control.control.params.sections[ sectionType ].customization.layout[ fieldId ].type;
    }
  }
}