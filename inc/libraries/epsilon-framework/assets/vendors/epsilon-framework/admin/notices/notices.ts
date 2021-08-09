import { EpsilonAjaxRequest } from '../../utils/epsilon-ajax-request';

declare var EpsilonWPUrls: any;
declare var userSettings: any;

/**
 * Epsilon Notices Class
 */
export class EpsilonNotices {
  /**
   * Class constructor
   */
  public constructor() {
    const self = this;
  }

  /**
   * Initiate notice dismissal
   */
  public init() {
    const self = this;
    let notices: JQuery = jQuery( '.epsilon-framework-notice' ),
        id: any, args: any, Ajax: EpsilonAjaxRequest;
    jQuery.each( notices, function( this: any ) {
      jQuery( this ).on( 'click', '.notice-dismiss', function() {
        id = jQuery( this ).parent().attr( 'data-unique-id' );

        args = {
          action: [ 'Epsilon_Notifications', 'dismiss_notice' ],
          nonce: EpsilonWPUrls.ajax_nonce,
          args: {
            notice_id: jQuery( this ).parent().attr( 'data-unique-id' ),
            user_id: userSettings.uid,
          }
        };

        Ajax = new EpsilonAjaxRequest( args );
        Ajax.request();
      } );
    } );
  }
}
