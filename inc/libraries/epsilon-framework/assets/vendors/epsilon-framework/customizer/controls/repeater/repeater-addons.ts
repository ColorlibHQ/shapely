declare var EpsilonTranslations: any;
declare var EpsilonWPUrls: any;
declare var wp: any;
declare var _: any;

import { EpsilonAjaxRequest } from '../../../utils/epsilon-ajax-request';
import { EpsilonButtonGroup } from '../button-group';
import { EpsilonRangeSlider } from '../range-slider';
import { EpsilonRepeaterRow } from './repeater-row';
import { EpsilonIconPicker } from '../icon-picker';
import { EpsilonTextEditor } from '../text-editor';
import { EpsilonColorPicker } from '../color-picker';
import { EpsilonCustomizerNavigation } from '../customizer-navigation';
import { EpsilonFieldRepeater } from '../repeater';
import { EpsilonSectionRepeater } from '../section-repeater';
import { EpsilonRepeaterSectionRow } from './repeater-section-row';

export class EpsilonRepeaterAddons {
  /**
   * Control reference
   */
  protected control: EpsilonFieldRepeater | EpsilonSectionRepeater;
  /**
   * Row
   */
  public row: EpsilonRepeaterSectionRow | EpsilonRepeaterRow | any;

  /**
   * Proxy to retrieve fields
   */
  public proxy: any;

  /**
   *
   * @param {EpsilonFieldRepeater | EpsilonSectionRepeater} control
   * @param {EpsilonRepeaterSectionRow | EpsilonRepeaterRow | any} row
   */
  public constructor( control: EpsilonFieldRepeater | EpsilonSectionRepeater, row: EpsilonRepeaterSectionRow | EpsilonRepeaterRow | any ) {
    this.control = control;
    this.row = row;
    this.proxy = this.control.control.params;

    if ( this.row.hasOwnProperty( 'type' ) ) {
      this.proxy = this.proxy.sections[ this.row.type ];
    }
  }

  /**
   * Init all plugins
   */
  public initPlugins() {
    this.initRangeSliders();
    this.initImageUploads();
    this.initIconPicker();
    this.initTextEditor();
    this.initColorPickers();
    this.initButtonGroup();
    if ( this.row.hasOwnProperty( 'type' ) ) {
      this.initCustomizerNavigation();
      this.initSelectize();
    }
  }

  /**
   * Initiate button group
   */
  public initButtonGroup(): void {
    const self = this;
    let settings: any = {
      container: self.row.container,
      repeater: true,
    };

    new EpsilonButtonGroup( settings );
  }

  /**
   * Selectize inputs
   */
  public initSelectize(): void {
    this.row.container.find( '.epsilon-selectize' ).selectize( {
      plugins: [ 'remove_button' ],
    } );
  }

  /**
   * Init navigation between sections
   */
  public initCustomizerNavigation(): void {
    const self = this;
    let init: boolean = false,
        settings: any;
    for ( let k in self.proxy.fields ) {
      if ( 'epsilon-customizer-navigation' === self.proxy.fields[ k ].type ) {
        settings = self.row;
        new EpsilonCustomizerNavigation( settings );
      }
    }
  }

  /**
   * Init color pickers
   */
  public initColorPickers(): void {
    const self = this;
    let init: boolean = false,
        settings: any;

    settings = self.row;
    new EpsilonColorPicker( settings );
  }

  /**
   * Handle image uploads
   */
  public initImageUploads(): void {
    const self = this;
    let temp: JQuery;

    /**
     * Image controls - Upload
     */
    self.row.container.on( 'click keypress', '.epsilon-controller-image-container .image-upload-button', function( this: any, e: Event ) {
      e.preventDefault();

      if ( wp.customize.utils.isKeydownButNotEnterEvent( e ) ) {
        return;
      }
      temp = jQuery( this ).parents( '.epsilon-controller-image-container' );
      self._imageUpload( temp );
    } );

    /**
     * Image Controls - Removal
     */
    self.row.container.on( 'click keypress', '.epsilon-controller-image-container .image-upload-remove-button', function( this: any, e: Event ) {
      e.preventDefault();

      if ( wp.customize.utils.isKeydownButNotEnterEvent( e ) ) {
        return;
      }

      temp = jQuery( this ).parents( '.epsilon-controller-image-container' );
      self._imageRemoval( temp );
    } );
  }

  /**
   * Image Upload
   * @private
   */
  private _imageUpload( container: JQuery ): void {
    const self = this;
    let setting: any = {},
        temp: any,
        size: any,
        val: string | any,
        input: JQuery,
        image = wp.media( {
          multiple: false,
        } ).open();

    /**
     * On selection, save the data in a JSON
     */
    image.on( 'select', function() {
      input = container.find( 'input' );
      temp = image.state().get( 'selection' ).first();
      size = input.attr( 'data-size' );

      if ( 'undefined' === typeof (temp.toJSON().sizes[ size ]) ) {
        size = 'full';
      }

      setting.id = temp.id;
      setting.url = temp.toJSON().sizes[ size ].url;

      val = ('url' === input.attr( 'data-save-mode' )) ? setting.url : setting.id;
      input.val( val );

      self._setImage( container, setting.url );
      container.find( '.actions .image-upload-remove-button' ).show();
    } );
  }

  /**
   * Removes image
   * @private
   */
  private _imageRemoval( container: JQuery ): void {
    const self = this;
    let thumb: JQuery = container.find( '.epsilon-image' );

    if ( thumb.length ) {
      thumb.find( 'img' ).fadeOut( 200, function() {
        let html = EpsilonTranslations.selectFile + '<span class="recommended-size"></span>',
            Ajax: EpsilonAjaxRequest,
            data = {
              action: [ 'Epsilon_Helper', 'get_image_sizes' ],
              nonce: EpsilonWPUrls.ajax_nonce,
              args: [],
            },
            size: any = container.find( 'input' ).attr( 'data-size' );
        thumb.removeClass( 'epsilon-image' ).addClass( 'placeholder' ).html( html );
        Ajax = new EpsilonAjaxRequest( data );
        Ajax.request();
        jQuery( Ajax ).on( 'epsilon-received-success', function( this: any, e: JQueryEventConstructor ) {
          if ( ! _.isUndefined( Ajax.result[ size ] ) ) {
            thumb.find( '.recommended-size' ).text( Ajax.result[ size ].width + ' x ' + Ajax.result[ size ].height );
          }
        } );

      } );
    }

    container.find( '.actions .image-upload-remove-button' ).hide();
    container.find( 'input' ).attr( 'value', '' ).trigger( 'change' );
  }

  /**
   * Sets the uploaded image
   * @private
   */
  private _setImage( container: JQuery, url: string ): void {
    /**
     * If we already have an image, we need to return that div, else we grab the placeholder
     *
     * @type {*}
     */
    let thumb = container.find( '.epsilon-image' ).length ? container.find( '.epsilon-image' ) : container.find( '.placeholder' );

    /**
     * We "reload" the image container
     */
    if ( thumb.length ) {
      thumb.removeClass( 'epsilon-image placeholder' ).addClass( 'epsilon-image' );
      thumb.html( '' );
      thumb.append( '<img style="display:none" src="' + url + '" />' );
      thumb.find( 'img' ).fadeIn( 200 );
    }

    container.find( 'input' ).trigger( 'change' );
  }

  /**
   * Icon picker
   */
  public initIconPicker(): void {
    const self = this;
    let init: boolean = false,
        temp: JQuery;

    for ( let k in self.proxy.fields ) {
      if ( 'epsilon-icon-picker' === self.proxy.fields[ k ].type ) {
        init = true;

        /***
         * Toggle
         */
        self.row.container.on( 'click keypress', '.epsilon-icon-picker-repeater-container .epsilon-icon-container', function( this: any, e: any ) {
          e.preventDefault();

          if ( wp.customize.utils.isKeydownButNotEnterEvent( e ) ) {
            return;
          }

          jQuery( this ).toggleClass( 'opened-icon-picker' );
          temp = jQuery( this ).parents( '.epsilon-icon-picker-repeater-container' );
          self._iconPickerToggle( temp );
        } );

        /**
         * Selection
         */
        self.row.container.on( 'click keypress', '.epsilon-icon-picker-repeater-container .epsilon-icons-container .epsilon-icons > i', function( this: any, e: any ) {
          e.preventDefault();

          if ( wp.customize.utils.isKeydownButNotEnterEvent( e ) ) {
            return;
          }

          temp = jQuery( this ).parents( '.epsilon-icon-picker-repeater-container' );
          self._iconPickerSelection( this, temp );
        } );

        /**
         * Filtering
         */
        self.row.container.on( 'keyup change', '.epsilon-icon-picker-repeater-container .search-container input', _.debounce( function( this: any, e: Event ) {
          e.preventDefault();

          if ( wp.customize.utils.isKeydownButNotEnterEvent( e ) ) {
            return;
          }

          temp = jQuery( this ).parents( '.epsilon-icon-picker-repeater-container' );
          self._iconPickerFilter( this, temp );

        }, 1000 ) );
      }
    }
  }

  /**
   * Toggle the icon picker dropdown
   * @private
   */
  private _iconPickerToggle( container: JQuery ): void {
    container.find( '.epsilon-icon-picker-container' ).toggleClass( 'opened' );
  }

  /**
   * Icon pick
   * @private
   */
  private _iconPickerSelection( clicked: JQuery, container: JQuery ): void {
    let icon: string | any, label: string | any;

    container.find( '.epsilon-icons > i.selected' ).removeClass( 'selected' );
    icon = jQuery( clicked ).addClass( 'selected' ).attr( 'data-icon' );
    label = jQuery( clicked ).attr( 'data-search' );
    container.find( '.epsilon-icon-name > i' ).removeClass().addClass( icon );
    container.find( '.epsilon-icon-name > .icon-label' ).html( label );

    /**
     * Set value
     */
    container.find( '.epsilon-icon-picker' ).attr( 'value', icon ).trigger( 'change' );
  }

  /**
   * Icon picker filtering
   * @private
   */
  private _iconPickerFilter( input: JQuery, container: JQuery ): void {
    let filter: string | any, temp: string | any,
        collection = jQuery( container ).find( '.epsilon-icons > i' );

    filter = jQuery( input ).val();
    if ( 'undefined' !== typeof filter ) {
      filter = filter.toLowerCase();
    }

    jQuery.each( collection, function() {
      temp = jQuery( this ).attr( 'data-search' );
      if ( 'undefined' !== typeof temp ) {
        temp = temp.toLowerCase();
      }

      jQuery( this )[ temp.indexOf( filter ) !== - 1 ? 'show' : 'hide' ]();
    } );
  }

  /**
   * WP Texteditor
   */
  public initTextEditor(): void {
    const self = this;
    let textareas: JQuery = jQuery( self.row.container ).find( 'textarea' );

    for ( let i = 0; i < textareas.length; i ++ ) {
      setTimeout( function() {
        let settings: any = {
          container: jQuery( textareas[ i ] ).parent(),
          params: {
            id: jQuery( textareas[ i ] ).attr( 'id' )
          }
        };

        new EpsilonTextEditor( settings, true );
      }, 100 * i );

    }
  }

  /**
   * initiate range sliders
   */
  private initRangeSliders() {
    const self = this;
    let init = false,
        sliderSettings: any,
        val: any;

    for ( let k in self.proxy.fields ) {
      if ( 'epsilon-slider' === self.proxy.fields[ k ].type ) {
        init = true;
        sliderSettings = {
          container: self.row.container,
          params: {
            id: self.proxy.fields[ k ].id,
            sliderControls: {
              min: self.proxy.fields[ k ].choices.min,
              max: self.proxy.fields[ k ].choices.max,
              step: self.proxy.fields[ k ].choices.step
            }
          }
        };

        if ( self.row.hasOwnProperty( 'type' ) ) {
          sliderSettings.params.value = parseFloat( self.proxy.fields[ k ].default );
          if ( 'undefined' !== typeof self.control.control.params.value[ self.row.index ] ) {
            sliderSettings.params.value = parseFloat( self.control.control.params.value[ self.row.index ][ k ] );
          }

          new EpsilonRangeSlider( sliderSettings );
          return;
        }

        if ( 'undefined' !== typeof self.proxy.value[ self.row.index ] ) {
          sliderSettings.params.value = parseFloat( self.proxy.value[ self.row.index ][ k ] );
        }

        sliderSettings.container = jQuery( sliderSettings.container ).find( '.epsilon-slider:not(.initiated)' ).first().addClass( 'initiated' );

        new EpsilonRangeSlider( sliderSettings );
      }
    }
  }
}
