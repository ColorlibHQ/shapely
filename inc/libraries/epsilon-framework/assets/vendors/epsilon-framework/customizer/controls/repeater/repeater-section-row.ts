import { EpsilonRepeaterRow } from './repeater-row';
import { EpsilonRepeaterSectionUtils } from './repeater-section-utils';
import { EpsilonSectionRepeater } from '../section-repeater';
import { EpsilonFieldRepeater } from '../repeater';

export class EpsilonRepeaterSectionRow extends EpsilonRepeaterRow {
  /**
   * Redeclare label as string
   */
  public label: any;
  /**
   * Section row has a type
   */
  public type: string;

  /**
   * Constructor
   * @param {EpsilonSectionRepeater | EpsilonFieldRepeater} instance
   * @param {JQuery} rowContainer
   * @param {string} type
   */
  public constructor(
      instance: EpsilonSectionRepeater | EpsilonFieldRepeater,
      rowContainer: JQuery,
      type: string ) {
    super( instance, rowContainer );
    this.type = type;
    this.label = instance.control.params.sections[ type ].title;

    /**
     * Update labels
     */
    instance.utils.updateLabel( this );
    this.addTabs();
  }

  /**
   * Add tabs functionality ( sections have layout/styling optional settings )
   */
  public addTabs(): void {
    jQuery( this.container ).find( '[data-group="regular"]' ).wrapAll( '<div data-tab-id="regular" class="tab-panel regular active"></div>' );
    jQuery( this.container ).find( '[data-group="styling"]' ).wrapAll( '<div data-tab-id="styling" class="tab-panel styling"></div>' );
    jQuery( this.container ).find( '[data-group="layout"]' ).wrapAll( '<div data-tab-id="layout" class="tab-panel layout"></div>' );
    this._handleTabs();
  }

  /**
   * Handle tabs functionality
   * @param container
   */
  private _handleTabs() {
    const self = this;
    let wrapper = self.container.find( 'nav' ),
        tabs = self.container.find( '[data-tab-id]' ),
        tab: JQuery;

    jQuery( wrapper ).on( 'click', 'a', function( this: any, event: JQueryEventConstructor ) {
      event.preventDefault();
      jQuery( this ).siblings().removeClass( 'active' );
      jQuery( this ).addClass( 'active' );
      tab = self.container.find( '[data-tab-id="' + jQuery( this ).attr( 'data-item' ) + '"]' );
      if ( tab.length ) {
        tabs.removeClass( 'active' );
        tab.addClass( 'active' );
      }

    } );
  }

  /**
   * We'll overwrite label immediatly after construct
   * @returns {any}
   */
  public getLabel(): any {
    return this.label;
  }
}