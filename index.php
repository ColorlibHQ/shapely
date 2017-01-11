<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */
get_header(); ?>
<?php $layout_class = shapely_get_layout_class();?>
	<div class="row">
		<?php
		if ( $layout_class == 'sidebar-left' ):
			get_sidebar();
		endif;
		?>
		<div id="primary" class="col-md-8 mb-xs-24 <?php echo esc_attr( $layout_class ); ?>"><?php
			if ( have_posts() ) :

				if ( is_home() && ! is_front_page() ) : ?>
					<header>
						<h1 class="page-title screen-reader-text"><?php esc_html( single_post_title() ); ?></h1>
					</header>

					<?php
				endif;

				$layout_type = get_theme_mod( 'blog_layout_view', 'grid' );

				get_template_part( 'template-parts/layouts/blog', $layout_type );

				shapely_pagination();

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif; ?>
		</div><!-- #primary -->
		<?php
		if ( $layout_class == 'sidebar-right' ):
			get_sidebar();
		endif;
		?>
	</div>
<?php
get_footer();
