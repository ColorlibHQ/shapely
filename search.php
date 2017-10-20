<?php
/**
 * The template for displaying search results pages.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Shapely
 */
get_header();
$layout_class = shapely_get_layout_class(); ?>
	<div class="row">
		<?php
		if ( 'sidebar-left' == $layout_class ) :
			get_sidebar();
		endif;
		?>
		<section id="primary" class="content-area col-md-8 mb-xs-24 <?php echo esc_attr( $layout_class ); ?>">
			<main id="main" class="site-main" role="main">

				<?php
				if ( have_posts() ) :
				?>

					<header class="entry-header nolist">
						<h1 class="post-title entry-title"><?php printf( esc_html__( 'Search Results for: %s', 'shapely' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					</header><!-- .page-header -->

					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'template-parts/content', 'search' );

					endwhile;

					shapely_pagination();
				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>

			</main><!-- #main -->
		</section><!-- #primary -->

		<?php
		if ( 'sidebar-right' == $layout_class ) :
			get_sidebar();
		endif;
		?>
	</div>
<?php
get_footer();
