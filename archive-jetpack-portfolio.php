<?php
get_header();

$layout       = get_theme_mod( 'projects_layout_view', 'mansonry' );
$layout_class = shapely_get_layout_class();

$item_classes = 'post-snippet col-md-3 col-sm-6 project';
if ( 'mansonry' == $layout ) {
	$item_classes .= ' masonry-item';
}

?>
	<div class="row">
	<?php
	if ( 'sidebar-left' == $layout_class ) :
		get_sidebar();
	endif;
	?>
	<div id="primary" class="content-area col-md-8 mb-xs-24 <?php echo esc_attr( $layout_class ); ?>">
		<div class="site-main">

			<?php
			if ( have_posts() ) :
			?>

			<?php if ( 'mansonry' == $layout ) : ?>
				<div class="masonry-loader fixed-center">
					<div class="col-sm-12 text-center">
						<div class="spinner"></div>
					</div>
				</div>
			<?php endif ?>

			<div class="<?php echo 'mansonry' == $layout ? 'masonry masonryFlyIn' : ''; ?>">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();
					$projects_args = array(
						'fields' => 'names',
					);
					$project_types = wp_get_post_terms( get_the_ID(), 'jetpack-portfolio-type', $projects_args );
					// wp_get_post_terms() returns WP_Error for an unregistered taxonomy,
					// which would fatal in the implode() below.
					if ( is_wp_error( $project_types ) ) {
						$project_types = array();
					}

					$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
					$item_style    = '';
					if ( 'mansonry' != $layout && $thumbnail_url ) {
						$item_style = 'background-image: url(' . esc_url( $thumbnail_url ) . ')';
					}
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( $item_classes ); ?>>
						<div class="image-tile inner-title hover-reveal text-center" style="<?php echo esc_attr( $item_style ); ?>">
							<?php
							if ( has_post_thumbnail() ) {

								$portfolio_custom_url = get_post_meta( get_the_ID(), 'shapely_companion_portfolio_link', true );

								if ( ! $portfolio_custom_url ) {
									$portfolio_custom_url = get_the_permalink();
								}

								?>
								<a href="<?php echo esc_url( $portfolio_custom_url ); ?>" title="<?php the_title_attribute(); ?>">
									<?php
									if ( 'mansonry' == $layout ) {
										the_post_thumbnail( 'medium' );
									}
									?>
									<div class="title">
										<?php
										the_title( '<h5 class="mb0">', '</h5>' );
										if ( ! empty( $project_types ) ) {
											// Term names are author-editable; escape each before joining.
											echo '<span>' . esc_html( implode( ' / ', $project_types ) ) . '</span>';
										}
										?>
									</div>
								</a>
								<?php
							}
							?>
						</div>
					</article><!-- #post-## -->
				<?php

				endwhile;

				the_posts_navigation();

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>

		</div><!-- .site-main -->
	</div><!-- #primary -->
	<?php
	if ( 'sidebar-right' == $layout_class ) :
		get_sidebar();
	endif;
	?>
	</div><!-- .row -->
<?php
get_footer();
