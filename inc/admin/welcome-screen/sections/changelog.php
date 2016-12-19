<?php
/**
 * Changelog
 */

$shapely = wp_get_theme( 'shapely' );

?>
<div class="featured-section changelog">


	<?php
	WP_Filesystem();
	global $wp_filesystem;
	$shapely_changelog       = $wp_filesystem->get_contents( get_template_directory() . '/changelog.txt' );
	$shapely_changelog_lines = explode( PHP_EOL, $shapely_changelog );
	foreach ( $shapely_changelog_lines as $shapely_changelog_line ) {
		if ( substr( $shapely_changelog_line, 0, 3 ) === "###" ) {
			echo '<h4>' . esc_html( substr( $shapely_changelog_line, 3 ) ) . '</h4>';
		} else {
			echo esc_html( $shapely_changelog_line ), '<br/>';
		}


	}

	echo '<hr />';


	?>

</div>