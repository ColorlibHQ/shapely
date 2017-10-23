<?php
/*
Template Name: No Sidebar
Template Post Type: post, page
*/
get_header(); ?>
	<div class="row">
		<div id="primary" class="col-md-8 mb-xs-24 no-sidebar">
			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>
		</div><!-- #primary -->
	</div>
<?php
get_footer();
