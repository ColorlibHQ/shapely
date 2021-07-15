declare var require: any;
declare var EpsilonWPUrls: any;
declare var wp: any;
declare var _: any;

export class EpsilonSectionEditorPreviewer {
  /**
   * Class constructor
   */
  public constructor() {
    jQuery( document ).on( 'click', '.epsilon-section-editor', function( this: any, e: JQueryEventConstructor ) {
      e.preventDefault();
      let object = {
        section: jQuery( this ).parents( '[data-section]' ).attr( 'data-section' ),
        customizerSection: jQuery( this ).parents( '[data-section]' ).attr( 'data-customizer-section-id' )
      };

      wp.customize.preview.send( 'epsilon-section-edit', object );
    } );
  }
}