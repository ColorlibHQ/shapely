<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */

?>

<div class="row">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-content post-grid-wide col-md-12' ); ?>>
		<header class="entry-header">
			<?php
			if ( has_post_thumbnail() ) {
				$layout = shapely_get_layout_class();
				$size   = 'shapely-featured';

				if ( 'full-width' == $layout ) {
					$size = 'shapely-full';
				}
				$image = get_the_post_thumbnail( get_the_ID(), $size );
			} else {
				/*
				 * Was a hardcoded <img alt=""> that ignored the placeholder
				 * customizer settings and gave the wrapping link no accessible
				 * name. shapely_get_thumbnail() honours the settings and sets alt.
				 */
				$image = shapely_get_thumbnail( 'shapely-featured', 'placeholder_wide.jpg' );
			}
			?>
			<?php if ( ! empty( $image ) ) : ?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<?php echo wp_kses( $image, shapely_image_allowed_html() ); ?>
			</a>
			<?php endif; ?>

		</header><!-- .entry-header -->
		<div class="entry-content">
			<h2 class="post-title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( wp_trim_words( get_the_title(), 9 ) ); ?></a>
			</h2>

			<div class="entry-meta">
				<?php
				shapely_posted_on_no_cat();
				?>
				<!-- post-meta -->
			</div>

			<?php the_excerpt(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
</div>
