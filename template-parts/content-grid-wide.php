<?php
/**
 * Template part for displaying posts.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */

?>
	<div class="row">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-content post-grid-wide col-md-12' ); ?>>
			<header class="entry-header nolist">
				<?php
				$category      = get_the_category();
				$show_category = true;
				if ( is_category() ) {
					$show_category = get_theme_mod( 'show_category_on_category_page', 1 );
				}
				
				// Check the global category display setting
				$show_categories_globally = get_theme_mod( 'show_categories_globally', true );
				$show_category = $show_category && $show_categories_globally;
				
				if ( has_post_thumbnail() ) {
					$layout = shapely_get_layout_class();
					$size   = 'shapely-featured';

					if ( 'full-width' == $layout ) {
						$size = 'shapely-full';
					}
					$image = get_the_post_thumbnail( get_the_ID(), $size );
				} else {
					/*
					 * Route through shapely_get_thumbnail() so this layout honours the
					 * "enable placeholder" and custom-placeholder customizer settings.
					 * It previously hardcoded placeholder_wide.jpg and ignored both.
					 */
					$image = shapely_get_thumbnail( 'shapely-featured', 'placeholder_wide.jpg' );
				}
				?>
				<?php if ( ! empty( $image ) ) : ?>
				<a href="<?php echo esc_url( get_the_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
					<?php echo wp_kses( $image, shapely_image_allowed_html() ); ?>
				</a>
				<?php endif; ?>

				<?php if ( isset( $category[0] ) && $show_category ) : ?>
					<span class="shapely-category">
					<a href="<?php echo esc_url( get_category_link( $category[0]->term_id ) ); ?>">
						<?php echo esc_html( $category[0]->name ); ?>
					</a>
				</span>
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

				<?php
				the_content(
					sprintf(
						/* translators: %s: Name of current post. */
								wp_kses(
									__( 'Read more %s <span class="meta-nav">&rarr;</span>', 'shapely' ), array(
										'span' => array(
											'class' => array(),
										),
									)
								),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					)
				);

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
						'after'  => '</div>',
					)
				);
				?>
			</div><!-- .entry-content -->
		</article><!-- #post-## -->
	</div>
<?php

