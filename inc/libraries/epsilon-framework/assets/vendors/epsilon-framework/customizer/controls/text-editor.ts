declare var wp: any;

/**
 * Espilon Text Editor Module
 */
export class EpsilonTextEditor {
  /**
   * Object Context
   */
  context: JQuery | any;
  /**
   * Editor ID ( link between editor and textarea )
   */
  editorId: string;

  /**
   * Class Constructor
   * @param {{container: JQuery; params: {value: number; id: string}}} control
   */
  public constructor( control: { container: JQuery, setting: void, params: { value: number, id: string } }, repeater: boolean = false, deconstruct: boolean = false ) {
    this.context = jQuery( control.container );
    this.editorId = control.params.id + '-editor';

    if ( repeater ) {
      this.editorId = control.params.id;
    }

    if ( deconstruct ) {
      wp.editor.remove( this.editorId );
    }
    this.init();
  }

  /**
   * Text editor initiator
   */
  public init() {
    wp.editor.initialize( this.editorId, {
      tinymce: {
        wpautop: true,
        setup: function( editor: any ) {
          editor.on( 'change', function( e: Event ) {
            editor.save();
            jQuery( editor.getElement() ).trigger( 'change' );
          } );
        }
      },
      quicktags: true
    } );
  }
}