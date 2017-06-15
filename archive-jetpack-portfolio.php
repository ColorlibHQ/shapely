<?php
get_header(); ?>

	<div id="primary" class="content-area col-md-12 mb-xs-24">
		<main id="main" class="site-main" role="main">

			<?php
			if ( have_posts() ) : ?>

			<header>
				<?php
				echo ( get_theme_mod( 'portfolio_name' ) != '' ) ? '<h1 class="post-title">' . esc_html( get_theme_mod( 'portfolio_name' ) ) . '</h1>' : '';
				echo ( get_theme_mod( 'portfolio_description' ) != '' ) ? '<p>' . esc_html( get_theme_mod( 'portfolio_description' ) ) . '</p>' : '';
				?>
			</header><!-- .page-header -->

			<div class="masonry-loader fixed-center">
				<div class="col-sm-12 text-center">
					<div class="spinner"></div>
				</div>
			</div>
			<div class="masonry masonryFlyIn">
				<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();
					$projects_args = array(
						'fields' => 'names',
					);
					$project_types = wp_get_post_terms( $post->ID, 'jetpack-portfolio-type', $projects_args );
				?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-snippet col-md-3 col-sm-6 masonry-item project' ); ?>>
						<div class="image-tile inner-title hover-reveal text-center"><?php
						if ( has_post_thumbnail() ) { ?>
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<?php the_post_thumbnail( 'medium' ); ?>
								<div class="title"><?php
								the_title( '<h5 class="mb0">', '</h5>' );
								if ( ! empty( $project_types ) ) {
									echo '<span>' . implode( ' / ', $project_types ) . '</span>';
								} ?>
								</div>
								</a><?php
						} ?>
						</div>
					</article><!-- #post-## --><?php

				endwhile;

				the_posts_navigation();

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
