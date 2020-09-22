declare var require: any;
declare var EpsilonWPUrls: any;
declare var wp: any;
declare var _: any;

import { EpsilonAjaxRequest } from '../../utils/epsilon-ajax-request';

export class EpsilonPartialRefresh {
  /**
   * Frontend sections
   */
  public sections: Array<{ id: number | any, section: JQuery }> = [];

  /**
   * Handle the section partial refresh
   */
  constructor() {
    /**
     * Register sections
     */
    this.registerSections();
    this.handleEvents();
  }

  /**
   * Register sectiosn
   */
  public registerSections() {
    const self = this;
    var $sections = jQuery( '[data-customizer-section-id]' );
    for ( let i = 0; i < $sections.length; i ++ ) {
      var id: any = jQuery( $sections[ i ] ).attr( 'data-section' );
      var section = {
        id: parseInt( id ),
        section: jQuery( $sections[ i ] ),
      };

      self.sections.push( section );
    }
  }

  /**
   * Handle events
   */
  public handleEvents() {
    const self = this;
    wp.customize.preview.bind( 'updated-section-repeater', _.debounce( function( object: any ) {
      self.changeSection( object );
    }, 300 ) );
  }

  /**
   * Change the section
   * @param object
   */
  public changeSection( object: any ) {
    const self = this;
    let args: {
          action: Array<string>,
          nonce: string,
          args: any,
        } = {
          action: [ 'Epsilon_Page_Generator', 'generate_partial_section' ],
          nonce: EpsilonWPUrls.ajax_nonce,
          args: {
            control: object.control,
            postId: object.postId,
            id: object.index,
            value: object.value,
          }
        },
        Ajax: EpsilonAjaxRequest;

    Ajax = new EpsilonAjaxRequest( args );
    Ajax.request();
    this.standBySection( self.sections[ object.index ].section );

    jQuery( Ajax ).on( 'epsilon-received-success', function( e: JQueryEventConstructor ) {
      self.liveSection( object.index, self.sections[ object.index ].section, Ajax.result.section );
      jQuery( document ).trigger( 'epsilon-selective-refresh-ready' );
    } );
  }

  /**
   * Stand by section
   * @param {JQuery} section
   */
  public standBySection( section: JQuery ) {
    section.animate( { opacity: .5 } );
  }

  /**
   *
   * @param {JQuery} section
   */
  public liveSection( sectionIndex: number, section: JQuery, result: any ) {
    const self = this;
    section.replaceWith( result );
    self.sections = [];
    self.registerSections();
    section.animate( { opacity: 1 } );
  }
}
