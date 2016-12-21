<?php
/* Start the Loop */

global $wp_query;
while ( have_posts() ) : the_post();
	/*
	 * Include the Post-Format-specific template for the content.
	 * If you want to override this in a child theme, then include a file
	 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
	 */
	if ( $wp_query->current_post == 0 ) {
		get_template_part( 'template-parts/content', 'grid-wide' );
	} else {
		if ( $wp_query->current_post == 1 ) {
			echo '<div class="row">';
		}

		get_template_part( 'template-parts/content', 'grid-small' );

		if ( fmod( $wp_query->current_post, 2 ) == 0 && $wp_query->current_post != (int) $wp_query->post_count ) {
			echo '</div><div class="row">';
		} elseif ( $wp_query->current_post == (int) $wp_query->post_count ) {
			continue;
		}

	}

endwhile;
?>
	</div>
<?php

