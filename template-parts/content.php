<?php
/**
 * Template part for displaying posts.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Shapely
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-snippet mb64' . ( is_single() ? ' content' : "" ) ); ?>>
	<header class="entry-header nolist">
		<?php
		if ( has_post_thumbnail() ){ ?>
		<a class="text-center" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php
			the_post_thumbnail( 'shapely-featured', array( 'class' => 'mb24' ) ); ?>
		</a><?php
		}

		if ( is_single() ) {
			the_title( '<h1 class="post-title entry-title">', '</h1>' );
		} else {
			the_title( '<h2 class="post-title entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}

		shapely_posted_on(); ?><!-- post-meta -->

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		if ( ! is_single() ) {
			the_content( sprintf(
			             /* translators: %s: Name of current post. */
				             wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'shapely' ), array( 'span' => array( 'class' => array() ) ) ),
				             the_title( '<span class="screen-reader-text">"', '"</span>', false )
			             ) );

			echo '<hr>';
		} else {
			the_content();
		}

		wp_link_pages( array(
			               'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shapely' ),
			               'after'  => '</div>',
		               ) );
		?>
	</div><!-- .entry-content -->

	<?php shapely_entry_footer(); ?>
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
</article><!-- #post-## -->
