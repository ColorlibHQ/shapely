<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Shapely
 */

if ( ! function_exists( 'shapely_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function shapely_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	); ?>

	<ul class="post-meta">
        <li><i class="fa fa-user"></i><span><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>" title="<?php echo get_the_author(); ?>"><?php the_author(); ?></a></span></li>
        <li><i class="fa fa-calendar"></i><span class="posted-on"><?php echo $time_string; ?></span></li>
        <?php shapely_post_category(); ?>
    </ul><?php
    echo ( is_archive() ) ? '<hr>' : '';
}
endif;

if ( ! function_exists( 'shapely_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function shapely_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'shapely' ) );
		if ( $categories_list && shapely_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'shapely' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'shapely' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'shapely' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'shapely' ), esc_html__( '1 Comment', 'shapely' ), esc_html__( '% Comments', 'shapely' ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'shapely' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function shapely_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'shapely_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'shapely_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so shapely_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so shapely_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in shapely_categorized_blog.
 */
function shapely_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'shapely_categories' );
}
add_action( 'edit_category', 'shapely_category_transient_flusher' );
add_action( 'save_post',     'shapely_category_transient_flusher' );


if ( ! function_exists( 'shapely_post_category' ) ) :
/**
 * Get category attached to post.
 */
function shapely_post_category() {
    $category = get_the_category();
    if ( !empty( $category ) ) {
      $i = ( $category[0]->slug == "uncategorized" && array_key_exists( '1', $category ) ) ? 1 : 0 ;
      echo '<li><i class="fa fa-folder-open-o"></i><span class="cat-links"><a href="' . get_category_link( $category[$i]->term_id ) . '" title="' . sprintf( __( "View all posts in %s", 'shapely' ), $category[$i]->name ) . '" ' . '>' . $category[$i]->name.'</a></span></li> ';
    }
}
endif;
