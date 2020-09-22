declare var wp: any;
declare var _: any;

import { EpsilonRepeaterUtils } from './repeater-utils';
import { EpsilonFieldRepeater } from '../repeater';
import { EpsilonSectionRepeater } from '../section-repeater';

export class EpsilonRepeaterRow {
  /**
   * Row index
   */
  public index: number;
  /**
   * Row Header
   */
  public header: JQuery;
  /**
   * Container
   */
  public container: JQuery;
  /**
   * Footer
   */
  public footer: JQuery;
  /**
   * Label
   */
  public label: { type: string, value: string, field: string };

  /**
   * Row Constructor
   * @param {{container: JQuery; setting: void; params: {value: number; id: string}}} instance
   * @param {JQuery} rowContainer
   */
  public constructor( instance: EpsilonFieldRepeater | EpsilonSectionRepeater, rowContainer: JQuery ) {
    const self = this;
    this.index = instance.currentIndex;
    this.container = rowContainer;
    this.label = this.getLabel( instance );
    this.header = this.container.find( '.repeater-row-header' );
    this.footer = this.container.find( '.repeater-row-footer' );

    this.handleEvents( instance );
    instance.utils.updateLabel( this );
  }

  /**
   *
   * @returns {any}
   */
  public getLabel( instance: any ): any {
    return instance.control.params.rowLabel;
  }

  /**
   * Handle row events
   */
  public handleEvents( instance: any ) {
    const control = instance,
        self = this;

    /**
     * Click event on header to toggle minimize
     */
    this.header.on( 'click', function() {
      control.utils.toggleMinimize( self );
    } );

    /**
     * Click event on handler to toggle minimize
     */
    this.container.on( 'click', '.repeater-row-minimize', function() {
      control.utils.toggleMinimize( self );
    } );

    /**
     * Remove row functionality
     */
    this.container.on( 'click', '.repeater-row-remove', function() {
      control.utils.removeRow( self );
    } );

    /**
     * Update row functionality
     */
    this.container.on( 'keyup change', 'input, select, textarea', function( this: any, e: any ) {
      control.utils.updateField( self, jQuery( e.target ).data( 'field' ), e.target );
      control.utils.updateLabel( self );

      self.container.trigger( 'row:update', [ self.index, jQuery( e.target ).data( 'field' ), e.target ] );
    } );
  }
}