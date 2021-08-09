declare var require: any;
declare var EpsilonWPUrls: any;
declare var wp: any;
declare var _: any;
import { EpsilonAjaxRequest } from '../../utils/epsilon-ajax-request';

export class EpsilonTypographyPreviewer {
  /**
   * Constructor
   */
  public constructor() {
    const self = this;
    wp.customize.preview.bind( 'update-inline-typography-css',
        function( object: { action: string, class: string, id: string, data: { selectors: string, stylesheet: string, json: any, id: any } } ) {
          let style: JQuery,
              data = {
                action: [ object.class, object.action ],
                nonce: EpsilonWPUrls.ajax_nonce,
                args: object.data,
                id: object.id
              },
              Ajax: EpsilonAjaxRequest;
          Ajax = new EpsilonAjaxRequest( data );
          Ajax.request();

          jQuery( Ajax ).on( 'epsilon-received-success', function( this: any, e: JQueryEventConstructor ) {
            style = jQuery( '#' + Ajax.result.stylesheet + '-inline-css' );
            if ( ! style.length ) {
              style = jQuery( 'body' ).append( '<style type="text/css" id="' + Ajax.result.stylesheet + '-inline-css" />' ).find( '#' + Ajax.result.stylesheet + '-inline-css' );
            }

            for ( let i = 0; i < Ajax.result.fonts.length; i ++ ) {
              if ( ! jQuery( 'link[href="https://fonts.googleapis.com/css?family=' + Ajax.result.fonts[ i ] + '"]' ).length ) {
                jQuery( 'head' ).append( '<link href="https://fonts.googleapis.com/css?family=' + Ajax.result.fonts[ i ] + '" rel="stylesheet">' );
              }
            }

            style.html( Ajax.result.css );
          } );
        } );
  }
}