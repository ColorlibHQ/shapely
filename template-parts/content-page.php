<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( has_post_thumbnail() ) { ?>
			<a class="text-center" href="<?php the_permalink(); ?>"
			   title="<?php esc_attr( the_title_attribute() ); ?>"><?php
			the_post_thumbnail( 'shapely-featured', array( 'class' => 'mb24' ) ); ?>
			</a><?php
		}
		?>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			               'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
			               'after'  => '</div>',
		               ) );
		?>
	</div><!-- .entry-content -->
	<?php
	if ( is_single() ):
		$prev = get_previous_post_link();
		$prev = str_replace( '&laquo;', '<div class="wrapper"><span class="fa fa-angle-left"></span>', $prev );
		$prev = str_replace( '</a>', '</a></div>', $prev );
		$next = get_next_post_link();
		$next = str_replace( '&raquo;', '<span class="fa fa-angle-right"></span></div>', $next );
		$next = str_replace( '<a', '<div class="wrapper"><a', $next );
		?>
		<hr/>
		<div class="shapely-next-prev row">
			<div class="col-md-6 text-left">
				<?php echo wp_kses_post( $prev ) ?>
			</div>
			<div class="col-md-6 text-right">
				<?php echo wp_kses_post( $next ) ?>
			</div>
		</div>
	<?php endif; ?>
	<footer class="entry-footer">
		<?php
		edit_post_link(
			sprintf(
			/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', 'shapely' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
