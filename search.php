<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Shapely
 */

get_header(); ?>

	
    <?php $layout_class = ( function_exists('shapely_get_layout_class') ) ? shapely_get_layout_class(): ''; ?>  
        <section id="primary" class="content-area col-md-9 mb-xs-24 <?php echo $layout_class; ?>">
          <main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>

			<header class="entry-header nolist">
				<h1 class="post-title entry-title"><?php printf( esc_html__( 'Search Results for: %s', 'shapely' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
