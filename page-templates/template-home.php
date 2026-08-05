<?php
/**
 * Template Name: Home Page
 *
 * Displays the Home page with Parallax effects.
 *
 */
?>

<?php get_header(); ?>
<?php
if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'sidebar-home' ) ) :
?>
	<div class="container p24 wp-caption-text">
		<h5><?php esc_html_e( 'This is the "Home Sidebar Section", add some widgets to it to change it.', 'shapely' ); ?></h5>
		<p><?php esc_html_e( 'Go to Appearance &rarr; Widgets and add widgets to the Homepage area.', 'shapely' ); ?></p>
	</div>
<?php endif; ?>


<?php get_footer(); ?>
