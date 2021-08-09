declare var require: any, wp: any, EpsilonWPUrls: any;

/**
 * Ajax request class
 */
export class EpsilonAjaxRequest {
  /**
   * Args
   */
  protected args: {
    action: Array<string>,
    nonce: string,
    args: any
  };

  /**
   * Result
   */
  public result: any;

  /**
   * Constructor
   * @param {Object} args
   */
  public constructor( args: { action: Array<string>, nonce: string, args: any } ) {
    this.args = args;
  }

  /**
   * Init Request
   */
  public request() {
    const self = this;
    jQuery.ajax( {
      type: 'POST',
      data: { action: 'epsilon_framework_ajax_action', args: self.args },
      dataType: 'json',
      url: EpsilonWPUrls.ajaxurl,
      success: function( data ) {
        self.result = data;
        jQuery( self ).trigger( 'epsilon-received-success' );
      },
      /**
       * Throw errors
       *
       * @param jqXHR
       * @param textStatus
       * @param errorThrown
       */
      error: function( jqXHR, textStatus, errorThrown ) {
        console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
      }
    } );
  }
}