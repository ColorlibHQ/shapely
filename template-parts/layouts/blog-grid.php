<?php
/* Start the Loop */
?>
	<div class="row">
		<?php
		global $wp_query;
		while ( have_posts() ) :
			the_post();
			$i = $wp_query->current_post + 1;
			/*
			 * Include the Post-Format-specific template for the content.
			 * If you want to override this in a child theme, then include a file
			 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
			 */
			get_template_part( 'template-parts/content', 'grid-small' );

			if ( fmod( $i, (int) 2 ) == 0 && $i != (int) $wp_query->post_count ) {
				echo '</div><div class="row">';
			} elseif ( $i == (int) $wp_query->post_count ) {
				continue;
			}
		endwhile;
		?>
	</div>
<?php
