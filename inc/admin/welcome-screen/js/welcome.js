jQuery( document ).ready(function() {// jscs:ignore validateLineBreaks

	/* If there are required actions, add an icon with the number of required actions in the About shapely page -> Actions required tab */
	var shapelyNrActionsRequired = shapelyWelcomeScreenObject.nr_actions_required;

	if ( ( 'undefined' !== typeof shapelyNrActionsRequired ) && ( 0 !== shapelyNrActionsRequired ) ) {
		jQuery( 'li.shapely-w-red-tab a' ).append( '<span class="shapely-actions-count">' + shapelyNrActionsRequired + '</span>' );
	}

	/* Dismiss required actions */
	jQuery( '.shapely-required-action-button' ).click(function() {

		var id = jQuery( this ).attr( 'id' ),
				action = jQuery( this ).attr( 'data-action' );
		jQuery.ajax({
			type: 'GET',
			data: { action: 'shapely_dismiss_required_action', id: id, todo: action },
			dataType: 'html',
			url: shapelyWelcomeScreenObject.ajaxurl,
			beforeSend: function( data, settings ) {
				jQuery( '.shapely-tab-pane#actions_required h1' ).append( '<div id="temp_load" style="text-align:center"><img src="' + shapelyWelcomeScreenObject.template_directory + '/inc/admin/welcome-screen/img/ajax-loader.gif" /></div>' );
			},
			success: function( data ) {
				location.reload();
				jQuery( '#temp_load' ).remove();
				/* Remove loading gif */
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR + ' :: ' + textStatus + ' :: ' + errorThrown );
			}
		});
	});
});
