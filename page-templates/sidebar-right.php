<?php
/*
Template Name: Sidebar Right
Template Post Type: post, page
*/

get_header();
$layout_class = shapely_get_layout_class(); ?>
	<div class="row">
		<div id="primary" class="col-md-8 mb-xs-24 <?php echo esc_attr( $layout_class ); ?>">
			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>
		</div><!-- #primary -->
		<?php
		get_sidebar();
		?>
	</div>
<?php
get_footer();
