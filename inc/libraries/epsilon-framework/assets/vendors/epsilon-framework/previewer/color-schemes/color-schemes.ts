declare var require: any;
declare var EpsilonWPUrls: any;
declare var wp: any;
declare var _: any;
import { EpsilonAjaxRequest } from '../../utils/epsilon-ajax-request';

export class EpsilonColorSchemesPreviewer {
  /**
   * Constructor
   */
  public constructor() {
    const self = this;
    wp.customize.preview.bind( 'update-inline-color-schemes-css', function( object: { action: string, class: string, data: object, id: string } ) {
      let data = {
            action: [ object.class, object.action ],
            nonce: EpsilonWPUrls.ajax_nonce,
            args: object.data,
            id: object.id
          },
          Ajax: EpsilonAjaxRequest;
      Ajax = new EpsilonAjaxRequest( data );
      Ajax.request();

      jQuery( Ajax ).on( 'epsilon-received-success', function( this: any, e: JQueryEventConstructor ) {
        let sufix = object.action + object.id,
            style = jQuery( '#epsilon-stylesheet-' + sufix );

        if ( ! style.length ) {
          style = jQuery( 'body' ).
              append( '<style type="text/css" id="epsilon-stylesheet-' + sufix + '" />' ).
              find( '#epsilon-stylesheet-' + sufix );
        }

        if ( style.html() !== Ajax.result.css ) {
          style.html( Ajax.result.css );
        }
      } );
    } );
  }
}