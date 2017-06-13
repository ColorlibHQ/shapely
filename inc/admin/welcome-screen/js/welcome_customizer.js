jQuery( document ).ready(function() {// jscs:ignore validateLineBreaks
	var shapelyAboutpage = shapelyWelcomeScreenCustomizerObject.aboutpage;
	var shapelyNrActionsRequired = shapelyWelcomeScreenCustomizerObject.nr_actions_required;

	/* Number of required actions */
	if ( ( 'undefined' !== typeof shapelyNrActionsRequired ) && ( 'undefined' !== typeof shapelyNrActionsRequired ) && ( '0' !== shapelyNrActionsRequired ) ) {
		jQuery( '#accordion-section-themes .accordion-section-title' ).append( '<a href="' + shapelyNrActionsRequired + '"><span class="shapely-actions-count">' + shapelyNrActionsRequired + '</span></a>' );
	}
});
