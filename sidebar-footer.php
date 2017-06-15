<?php
/**
 * The Sidebar widget area for footer.
 *
 * @package shapely
 */
?>

<?php
// If footer sidebars do not have widget let's bail.

if ( ! is_active_sidebar( 'footer-widget-1' ) && ! is_active_sidebar( 'footer-widget-2' ) && ! is_active_sidebar( 'footer-widget-3' ) && ! is_active_sidebar( 'footer-widget-4' ) ) {
	return;
}
// If we made it this far we must have widgets.
?>

<div class="footer-widget-area">
	<?php if ( is_active_sidebar( 'footer-widget-1' ) ) : ?>
		<div class="col-md-3 col-sm-6 footer-widget" role="complementary">
			<?php dynamic_sidebar( 'footer-widget-1' ); ?>
		</div><!-- .widget-area .first -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'footer-widget-2' ) ) : ?>
		<div class="col-md-3 col-sm-6 footer-widget" role="complementary">
			<?php dynamic_sidebar( 'footer-widget-2' ); ?>
		</div><!-- .widget-area .second -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'footer-widget-3' ) ) : ?>
		<div class="col-md-3 col-sm-6 footer-widget" role="complementary">
			<?php dynamic_sidebar( 'footer-widget-3' ); ?>
		</div><!-- .widget-area .third -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'footer-widget-4' ) ) : ?>
		<div class="col-md-3 col-sm-6 footer-widget" role="complementary">
			<?php dynamic_sidebar( 'footer-widget-4' ); ?>
		</div><!-- .widget-area .third -->
	<?php endif; ?>
</div>
