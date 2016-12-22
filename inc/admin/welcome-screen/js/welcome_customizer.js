jQuery(document).ready(function () {
	var shapely_aboutpage = shapelyWelcomeScreenCustomizerObject.aboutpage;
	var shapely_nr_actions_required = shapelyWelcomeScreenCustomizerObject.nr_actions_required;

	/* Number of required actions */
	if ( (typeof shapely_aboutpage !== 'undefined') && (typeof shapely_nr_actions_required !== 'undefined') && (shapely_nr_actions_required != '0') ) {
		jQuery('#accordion-section-themes .accordion-section-title').append('<a href="' + shapely_aboutpage + '"><span class="shapely-actions-count">' + shapely_nr_actions_required + '</span></a>');
	}
});
