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
	<article id="post-<?php the_ID(); ?>" <?php post_class('post-content post-grid-wide col-md-12'); ?>>
		<header class="entry-header">
			<?php
			$image    = '<img class="wp-post-image" alt="" src="' . get_template_directory_uri() . '/assets/images/placeholder_wide.jpg" />';
			if ( has_post_thumbnail() ) {
				$layout = shapely_get_layout_class();
				$size   = 'shapely-featured';

				if ( $layout == 'full-width' ) {
					$size = 'shapely-full';
				}
				$image = get_the_post_thumbnail( get_the_ID(), $size );
			}
			$allowed_tags = array(
				'img'      => array(
					'data-srcset' => true,
					'data-src'    => true,
					'srcset'      => true,
					'sizes'       => true,
					'src'         => true,
					'class'       => true,
					'alt'         => true,
					'width'       => true,
					'height'      => true
				),
				'noscript' => array()
			);
			?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>">
				<?php echo wp_kses( $image, $allowed_tags ); ?>
			</a>

		</header><!-- .entry-header -->
		<div class="entry-content">
			<h2 class="post-title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo wp_trim_words( get_the_title(), 9 ); ?></a>
			</h2>

			<div class="entry-meta">
				<?php
				shapely_posted_on_no_cat(); ?><!-- post-meta -->
			</div>

			<?php the_excerpt(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
</div>