<?php
/* Start the Loop */

global $wp_query;
while ( have_posts() ) : the_post();
	/*
	 * Include the Post-Format-specific template for the content.
	 * If you want to override this in a child theme, then include a file
	 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
	 */
	?> <div class="row"> <?php
	get_template_part( 'template-parts/content', 'grid-wide' );
	?> </div> <?php
endwhile;

