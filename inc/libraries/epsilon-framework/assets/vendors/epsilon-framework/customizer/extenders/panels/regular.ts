declare var wp: any;
declare var _: any;

/**
 * Nestable Regular Panels Constructor
 */
wp.customize.panelConstructor[ 'epsilon-panel-regular' ] = wp.customize.Panel.extend( {
  /**
   * Embed Panel
   */
  _panelEmbed: wp.customize.Panel.prototype.embed,
  /**
   * Check if is contextually active
   */
  _panelIsContextuallyActive: wp.customize.Panel.prototype.isContextuallyActive,
  /**
   * Attach Events
   */
  _panelAttachEvents: wp.customize.Panel.prototype.attachEvents,
  /**
   * Ready event
   */
  ready: function() {
    /**
     * Hide the panel
     */
    if ( this.params.hidden ) {
      this.container.addClass( 'epsilon-hidden' );
    }
    wp.customize.bind( 'pane-contents-reflowed', function() {
      // Reflow panels
      let panels: any = [];

      wp.customize.panel.each( function( panel: any ) {
        if ( 'epsilon-panel-regular' !== panel.params.type || 'undefined' === typeof panel.params.panel ) {
          return;
        }
        panels.push( panel );
      } );

      panels.sort( wp.customize.utils.prioritySort ).reverse();

      jQuery.each( panels, function( i, panel ) {
        var parentContainer = jQuery( '#sub-accordion-panel-' + panel.params.panel );
        parentContainer.children( '.panel-meta' ).after( panel.headContainer );
      } );

      jQuery( document ).trigger( 'epsilon-reflown-panels' );
    } );
  },
  /**
   * Attach events
   */
  attachEvents: function() {
    var panel = this;
    if ( 'epsilon-panel-regular' !== this.params.type || 'undefined' === typeof this.params.panel ) {
      this._panelAttachEvents.call( this );
      return;
    }

    this._panelAttachEvents.call( this );

    panel.expanded.bind( function( expanded: any ) {

      var parent = wp.customize.panel( panel.params.panel );
      if ( expanded ) {
        parent.contentContainer.addClass( 'current-panel-parent' );
      } else {
        parent.contentContainer.removeClass( 'current-panel-parent' );
      }

    } );

    panel.container.find( '.customize-panel-back' ).off( 'click keydown' ).on( 'click keydown', function( event: Event ) {
      if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
        return;
      }

      event.preventDefault();

      if ( panel.expanded() ) {
        wp.customize.panel( panel.params.panel ).expand();
      }

    } );
  },
  /**
   * Is contextually active
   * @returns {*}
   */
  isContextuallyActive: function() {
    var panel = this,
        children = this._children( 'panel', 'section' ),
        activeCount = 0;

    if ( 'epsilon-panel-regular' !== this.params.type ) {
      return this._panelIsContextuallyActive.call( this );
    }

    wp.customize.panel.each( function( child: any ) {
      if ( ! child.params.panel ) {
        return;
      }

      if ( child.params.panel !== panel.id ) {
        return;
      }

      children.push( child );
    } );

    children.sort( wp.customize.utils.prioritySort );

    _( children ).each( function( child: any ) {
      if ( child.active() && child.isContextuallyActive() ) {
        activeCount += 1;
      }
    } );

    return ( 0 !== activeCount );
  },
  /**
   * Embed
   */
  embed: function() {
    var panel = this,
        parentContainer = jQuery( '#sub-accordion-panel-' + this.params.panel );

    if ( 'epsilon-panel-regular' !== this.params.type || 'undefined' === typeof this.params.panel ) {
      this._panelEmbed.call( this );
      return;

    }

    this._panelEmbed.call( this );
    parentContainer.append( panel.headContainer );
  },
} );
