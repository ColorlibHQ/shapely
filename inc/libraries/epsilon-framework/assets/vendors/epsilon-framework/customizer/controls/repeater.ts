declare var wp: any;
declare var _: any;

import { EpsilonRepeaterRow } from './repeater/repeater-row';
import { EpsilonRepeaterAddons } from './repeater/repeater-addons';
import { EpsilonRepeaterUtils } from './repeater/repeater-utils';
import { EpsilonRepeaterSectionRow } from './repeater/repeater-section-row';

export class EpsilonFieldRepeater {
  /**
   * Repeater control
   */
  public control: any;
  /**
   * Context ( container )
   */
  public context: JQuery | any;
  /**
   * Repeater container ( where fields are stored );
   */
  public repeaterContainer: JQuery;
  /**
   * Setting field (where we'll save)
   */
  public settingField: JQuery;
  /**
   * Rows
   * @type {Array}
   */
  public rows: Array<EpsilonRepeaterRow | EpsilonRepeaterSectionRow> = [];
  /**
   * Current index
   */
  public currentIndex: number = 0;
  /**
   * Field limit
   */
  public limit: number | boolean;
  /**
   * Memoize template
   */
  public template: string;
  /**
   * Utilities object
   * @type {EpsilonRepeaterUtils}
   */
  public utils: any;

  /**
   * Object constructor
   * @param control
   */
  public constructor( control: { container: JQuery, setting: void, params: { rowLabel: any, value: number, id: string, fields: object, choices: { limit: number } } } ) {
    this.control = control;
    this.context = control.container;
    this.utils = this.loadUtils();
    this.template = this.loadTemplate();
    /**
     * Create a reference of the container
     */
    this.repeaterContainer = this.getRepeaterContainer();
    /**
     * Setting field reference
     */
    this.settingField = this.context.find( '[data-customize-setting-link]' );
    /**
     * Setup Limit
     *
     * @type {boolean}
     */
    if ( ! _.isUndefined( this.control.params.choices.limit ) ) {
      this.limit = (0 >= this.control.params.choices.limit) ? false : parseInt( this.control.params.choices.limit );
    }
    /**
     * Handle events
     */
    this.handleEvents();

    /**
     * Create the existing rows
     */
    this.createExistingRows();

    /**
     * Make rows sortable
     */
    this.initSortable();
  }

  /**
   * Repeater container
   * @returns {any}
   */
  public getRepeaterContainer() {
    return this.context.find( '.repeater-fields' );
  }

  /**
   * Load utilities
   * @public
   */
  public loadUtils(): any {
    return new EpsilonRepeaterUtils( this );
  }

  /**
   * Load template
   */
  public loadTemplate(): string {
    return this.utils.repeaterTemplate();
  }

  /**
   * Create existing rows
   */
  public createExistingRows(): void {
    const control = this;
    if ( this.control.params.value.length ) {
      for ( let i = 0; i < this.control.params.value.length; i ++ ) {
        let row: EpsilonRepeaterRow,
            addons: EpsilonRepeaterAddons;

        row = control.utils.add( this.control.params.value[ i ] );
        addons = new EpsilonRepeaterAddons( control, row );
        addons.initPlugins();
      }
    }
  }

  /**
   * Handle click/saving/etc events
   */
  public handleEvents(): void {
    const self = this;
    /**
     * 1. Add row button
     */
    this.context.on( 'click', 'button.epsilon-repeater-add', function( this: any, e: Event ) {
      let newRow: EpsilonRepeaterRow,
          addons: EpsilonRepeaterAddons;
      e.preventDefault();
      if ( ! self.limit || self.currentIndex < self.limit ) {
        newRow = self.utils.add();
        addons = new EpsilonRepeaterAddons( self, newRow );
        addons.initPlugins();
      } else {
        jQuery( self.control.selector + ' .limit' ).addClass( 'highlight' );
      }
    } );

    /**
     * 2. REMOVE Row button
     */
    this.context.on( 'click', '.repeater-row-remove', function( this: any, e: Event ) {
      self.handleRowDecrementor();
      if ( ! self.limit || self.currentIndex < self.limit ) {

        jQuery( self.control.selector + ' .limit' ).removeClass( 'highlight' );
      }
    } );
  }

  /**
   * Initiate sortable functionality
   */
  public initSortable(): void {
    const control = this;

    this.repeaterContainer.sortable( {
      handle: '.repeater-row-header',
      axis: 'y',
      distance: 15,
      stop: function( e, data ) {
        setTimeout( control.utils.sort( data ), 200 );
      },
    } );

  }

  /**
   * Handles row addition
   */
  public handleRowIncrementor(): void {
    /**
     * Update index;
     */
    this.currentIndex ++;
  }

  /**
   * Handles row addition
   */
  public handleRowDecrementor(): void {
    /**
     * Update index;
     */
    this.currentIndex --;
  }
}
