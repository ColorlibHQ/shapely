<?php

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function shapely_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( get_theme_mod( 'shapely_sidebar_position' ) == 'pull-right' ) {
		$classes[] = 'has-sidebar-left';
	} elseif ( get_theme_mod( 'shapely_sidebar_position' ) == 'no-sidebar' ) {
		$classes[] = 'has-no-sidebar';
	} elseif ( get_theme_mod( 'shapely_sidebar_position' ) == 'full-width' ) {
		$classes[] = 'has-full-width';
	} else {
		$classes[] = 'has-sidebar-right';
	}

	return $classes;
}

add_filter( 'body_class', 'shapely_body_classes' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 *
 * @return array
 */
function shapely_page_menu_args( $args ) {
	$args['show_home'] = true;

	return $args;
}

add_filter( 'wp_page_menu_args', 'shapely_page_menu_args' );

// Mark Posts/Pages as Untiled when no title is used
add_filter( 'the_title', 'shapely_title' );

function shapely_title( $title ) {
	if ( '' == $title ) {
		return esc_html__( 'Untitled', 'shapely' );
	} else {
		return $title;
	}
}

/**
 * Password protected post form using Boostrap classes
 */
add_filter( 'the_password_form', 'shapely_custom_password_form' );

function shapely_custom_password_form() {
	global $post;
	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
	$o     = '<form class="protected-post-form" action="' . get_option( 'siteurl' ) . '/wp-login.php?action=postpass" method="post">
  <div class="row">
    <div class="col-lg-10">
        <p>' . esc_html__( 'This post is password protected. To view it please enter your password below:', 'shapely' ) . '</p>
        <label for="' . esc_attr( $label ) . '">' . esc_html__( 'Password:', 'shapely' ) . ' </label>
      <div class="input-group">
        <input class="form-control" value="' . esc_attr( get_search_query() ) . '" name="post_password" id="' . esc_attr( $label ) . '" type="password">
        <span class="input-group-btn"><button type="submit" class="btn btn-default" name="submit" id="searchsubmit" value="' . esc_attr__( 'Submit', 'shapely' ) . '">' . esc_html__( 'Submit', 'shapely' ) . '</button>
        </span>
      </div>
    </div>
  </div>
</form>';

	return $o;
}

// Add Bootstrap classes for table
add_filter( 'the_content', 'shapely_add_custom_table_class' );
function shapely_add_custom_table_class( $content ) {
	return preg_replace( '/(<table) ?(([^>]*)class="([^"]*)")?/', '$1 $3 class="$4 table table-hover" ', $content );
}

if ( ! function_exists( 'shapely_header_menu' ) ) :
	/**
	 * Header menu (should you choose to use one)
	 */
	function shapely_header_menu() {
		// display the WordPress Custom Menu if available
		wp_nav_menu(
			array(
				'menu_id'         => 'menu',
				'theme_location'  => 'primary',
				'depth'           => 0,
				'container'       => 'div',
				'container_class' => 'collapse navbar-collapse navbar-ex1-collapse',
				'menu_class'      => 'menu',
				'fallback_cb'     => 'Wp_Bootstrap_Navwalker::fallback',
				'walker'          => new Wp_Bootstrap_Navwalker(),
			)
		);
	} /* end header menu */
endif;

/**
 * function to show the footer info, copyright information
 */
function shapely_footer_info() {
	printf( esc_html__( 'Theme by %1$s Powered by %2$s', 'shapely' ), '<a href="https://colorlib.com/" target="_blank" title="Colorlib">Colorlib</a>', '<a href="http://wordpress.org/" target="_blank" title="WordPress.org">WordPress</a>' );
}


if ( ! function_exists( 'shapely_get_theme_options' ) ) {
	/**
	 * Get information from Theme Options and add it into wp_head
	 */
	function shapely_get_theme_options() {

		echo '<style type="text/css">';

		if ( get_theme_mod( 'link_color' ) ) {
			echo 'a, .image-bg a, .contact-section .social-icons li a, a:visited, .footer .footer-credits a, .post-content .post-meta li a, .post-content .shapely-category a, .module.widget-handle i {color:' . esc_attr( get_theme_mod( 'link_color' ) ) . ' }';
		}
		if ( get_theme_mod( 'link_hover_color' ) ) {
			echo 'a:hover,
				a:active,
				a:focus,
				.post-title a:hover,
				.post-title a:focus,
				.image-bg a:hover,
				.image-bg a:focus,
				.contact-section .social-icons li a:hover,
				.contact-section .social-icons li a:focus,
				.footer .footer-credits a:hover,
				.footer .footer-credits a:focus,
				.post-content .post-meta li a:hover,
				.post-content .post-meta li a:focus,
				.widget.widget_recent_entries ul li a:focus,
				.widget.widget_recent_entries ul li a:hover,
				.widget.widget_recent_comments ul li .comment-author-link a:focus,
				.widget.widget_recent_comments ul li .comment-author-link a:hover,
				.widget.widget_archive > div ul li a:focus,
				.widget.widget_archive > div ul li a:hover,
				.widget.widget_archive ul li a:focus,
				.widget.widget_archive ul li a:hover,
				.widget.widget_categories > div ul li a:focus,
				.widget.widget_categories > div ul li a:hover,
				.widget.widget_categories ul li a:focus,
				.widget.widget_categories ul li a:hover,
				.widget.widget_meta > div ul li a:focus,
				.widget.widget_meta > div ul li a:hover,
				.widget.widget_meta ul li a:focus,
				.widget.widget_meta ul li a:hover,
				.widget.widget_pages > div ul li a:focus,
				.widget.widget_pages > div ul li a:hover,
				.widget.widget_pages ul li a:focus,
				.widget.widget_pages ul li a:hover,
				.widget.widget_nav_menu > div ul li a:focus,
				.widget.widget_nav_menu > div ul li a:hover,
				.widget.widget_nav_menu ul li a:focus,
				.widget.widget_nav_menu ul li a:hover,
				.widget.widget_nav_menu .menu > li a:focus,
				.widget.widget_nav_menu .menu > li a:hover,
				.widget.widget_tag_cloud a:focus,
				.widget.widget_tag_cloud a:hover,
				.widget_product_categories ul.product-categories li a:hover,
				.widget_product_categories ul.product-categories li a:focus,
				.widget_product_tag_cloud .tagcloud a:hover,
				.widget_product_tag_cloud .tagcloud a:focus,
				.widget_products .product_list_widget a:hover,
				.widget_products .product_list_widget a:focus,
				.woocommerce.widget ul.cart_list li a:hover,
				.woocommerce.widget ul.cart_list li a:focus,
				.woocommerce.widget ul.product_list_widget li a:hover,
				.woocommerce.widget ul.product_list_widget li a:focus,
				.woocommerce .widget_layered_nav_filters ul li a:hover,
				.woocommerce .widget_layered_nav_filters ul li a:focus,
				.woocommerce .widget_layered_nav ul li a:hover,
				.woocommerce .widget_layered_nav ul li a:focus,
				.main-navigation .menu > li > ul li:hover > a,
				.main-navigation .menu > li > ul li:focus > a,
				.main-navigation .menu > li > ul .dropdown:hover:after,
				.main-navigation .menu > li > ul .dropdown:focus:after,
				.main-navigation .menu li a:focus,
				.main-navigation .menu li:focus > a,
				.main-navigation .menu > li > ul li a:focus,
				.post-content .shapely-category a:hover,
				.post-content .shapely-category a:focus,
				.main-navigation .menu li:hover > a,
				.main-navigation .menu li:focus > a,
				.main-navigation .menu > li:hover:after,
				.main-navigation .menu > li:focus-within:after,
				.bg-dark .social-list a:hover,
				.bg-dark .social-list a:focus,
				.shapely-social .shapely-social-icon:hover,
				.shapely-social .shapely-social-icon:focus { color: ' . esc_attr( get_theme_mod( 'link_hover_color' ) ) . ';}';
		}

		if ( get_theme_mod( 'button_color' ) ) {
			echo '.btn-filled, .btn-filled:visited, .woocommerce #respond input#submit.alt,
          .woocommerce a.button.alt, .woocommerce button.button.alt,
          .woocommerce input.button.alt, .woocommerce #respond input#submit,
          .woocommerce a.button, .woocommerce button.button,
          .woocommerce input.button,
          .video-widget .video-controls button,
          input[type="submit"],
          button[type="submit"],
          .post-content .more-link { background:' . esc_attr( get_theme_mod( 'button_color' ) ) . ' !important; border: 2px solid ' . esc_attr( get_theme_mod( 'button_color' ) ) . ' !important;}';
			echo '.shapely_home_parallax > section:not(.image-bg) .btn-white { color:' . esc_attr( get_theme_mod( 'button_color' ) ) . ' !important; border: 2px solid' . esc_attr( get_theme_mod( 'button_color' ) ) . ' !important; }';
		}

		if ( get_theme_mod( 'button_hover_color' ) ) {
			echo '.btn-filled:hover,
				.btn-filled:focus,
				.woocommerce #respond input#submit.alt:hover,
				.woocommerce #respond input#submit.alt:focus,
				.woocommerce a.button.alt:hover,
				.woocommerce a.button.alt:focus,
				.woocommerce button.button.alt:hover,
				.woocommerce button.button.alt:focus,
				.woocommerce input.button.alt:hover,
				.woocommerce input.button.alt:focus,
				.woocommerce #respond input#submit:hover,
				.woocommerce #respond input#submit:focus,
				.woocommerce a.button:hover,
				.woocommerce a.button:focus,
				.woocommerce button.button:hover,
				.woocommerce button.button:focus,
				.woocommerce input.button:hover,
				.woocommerce input.button:focus,
				.video-widget .video-controls button:hover,
				.video-widget .video-controls button:focus,
				input[type="submit"]:hover,
				input[type="submit"]:focus,
				button[type="submit"]:hover,
				button[type="submit"]:focus,
				.post-content .more-link:hover,
				.post-content .more-link:focus,
				.btn:not(.btn-white):hover,
				.btn:not(.btn-white):focus,
				.button:not(.btn-white):hover,
				.button:not(.btn-white):focus
				{ background: ' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' !important; border: 2px solid' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' !important;}';

			echo '.shapely_home_parallax > section:not(.image-bg) .btn-white:hover,
				.shapely_home_parallax > section:not(.image-bg) .btn-white:focus,
				.pagination span:not( .dots ),
				.pagination a:hover,
				.pagination a:focus,
				.woocommerce-pagination ul.page-numbers span.page-numbers,
				.woocommerce nav.woocommerce-pagination ul li a:focus,
				.woocommerce nav.woocommerce-pagination ul li a:hover,
				.woocommerce nav.woocommerce-pagination ul li span.current { background-color: ' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' !important; border-color: ' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' !important;color: #fff !important; }';

			echo '.widget.widget_search .search-form > input#s:hover,
				.widget.widget_search .search-form > input#s:focus,
				.widget.widget_calendar #wp-calendar td:not(.pad):not(#next):not(#prev)#today,
				.widget_product_search .woocommerce-product-search > input.search-field:hover,
				.widget_product_search .woocommerce-product-search > input.search-field:focus,
				.widget.widget_search input[type="text"]:focus + button[type="submit"].searchsubmit,
				.widget.widget_search input[type="text"]:hover + button[type="submit"].searchsubmit,
				textarea:hover,
				textarea:focus,
				input[type="text"]:hover,
				input[type="search"]:hover,
				input[type="email"]:hover,
				input[type="tel"]:hover,
				input[type="text"]:focus,
				input[type="search"]:focus,
				input[type="email"]:focus,
				input[type="tel"]:focus,
				.widget.widget_product_search input[type="text"]:focus + button[type="submit"].searchsubmit,
				.widget.widget_product_search input[type="text"]:hover + button[type="submit"].searchsubmit
				{ border-color: ' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' !important }';

			echo '.widget.widget_calendar #wp-calendar > caption:after,
				.widget.widget_calendar #wp-calendar td:not(.pad):not(#next):not(#prev)#today:hover,
				.widget.widget_calendar #wp-calendar td:not(.pad):not(#next):not(#prev)#today:focus
				{ background-color: ' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' }';

			echo '.widget.widget_search input[type="text"]:focus + button[type="submit"].searchsubmit,
				.widget.widget_search input[type="text"]:hover + button[type="submit"].searchsubmit,
				.widget.widget_product_search input[type="text"]:focus + button[type="submit"].searchsubmit,
				.widget.widget_product_search input[type="text"]:hover + button[type="submit"].searchsubmit,
				.image-bg .text-slider .flex-direction-nav li a:focus:before
				{ color: ' . esc_attr( get_theme_mod( 'button_hover_color' ) ) . ' }';


		}

		echo '</style>';
	}
}// End if().
add_action( 'wp_head', 'shapely_get_theme_options', 10 );

/**
 * Add Bootstrap thumbnail styling to images with captions
 * Use <figure> and <figcaption>
 *
 * @link http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
 */
function shapely_caption( $output, $attr, $content ) {
	if ( is_feed() ) {
		return $output;
	}

	$defaults = array(
		'id'      => 'shapely_caption_' . rand( 1, 192282 ),
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => '',
	);

	$attr = shortcode_atts( $defaults, $attr );

	// If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
	if ( $attr['width'] < 1 || empty( $attr['caption'] ) ) {
		return $content;
	}

	$output  = '<figure id="' . esc_attr( $attr['id'] ) . '" class="thumbnail wp-caption ' . esc_attr( $attr['align'] ) . ' style="width: ' . ( esc_attr( $attr['width'] ) + 10 ) . 'px">';
	$output .= do_shortcode( $content );
	$output .= '<figcaption class="caption wp-caption-text">' . wp_kses_post( $attr['caption'] ) . '</figcaption>';
	$output .= '</figure>';

	return $output;
}

add_filter( 'img_caption_shortcode', 'shapely_caption', 10, 3 );

/**
 * Adds the URL to the top level navigation menu item
 */
function shapely_add_top_level_menu_url( $atts, $item, $args ) {
	if ( ! wp_is_mobile() && isset( $args->has_children ) && $args->has_children ) {
		$atts['href'] = ! empty( $item->url ) ? esc_url( $item->url ) : '';
	}

	return $atts;
}

add_filter( 'nav_menu_link_attributes', 'shapely_add_top_level_menu_url', 99, 3 );

/**
 * Makes the top level navigation menu item clickable
 */
function shapely_make_top_level_menu_clickable() {
	if ( ! wp_is_mobile() ) { ?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				if ( $( window ).width() >= 767 ) {
					$( '.navbar-nav > li.menu-item > a' ).click( function() {
						window.location = $( this ).attr( 'href' );
					} );
				}
			} );
		</script>
		<?php
	}
}

add_action( 'wp_footer', 'shapely_make_top_level_menu_clickable', 1 );

/*
 * Add Read More button to post archive
 */
function shapely_excerpt_more( $more ) {
	return '<div><a class="btn-filled btn" href="' . esc_url( get_the_permalink() ) . '" title="' . the_title_attribute(
		array(
			'echo' => false,
		)
	) . '">' . esc_html_x( 'Read More', 'Read More', 'shapely' ) . '</a></div>';
}

add_filter( 'excerpt_more', 'shapely_excerpt_more' );

/*
 * Pagination
 */
if ( ! function_exists( 'shapely_pagination' ) ) {

	function shapely_pagination() {
		?>
		<div class="text-center">
			<nav class="pagination">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '<icon class="fa fa-angle-left"></icon>',
						'next_text' => '<icon class="fa fa-angle-right"></icon>',
					)
				);
				?>
			</nav>
		</div>
		<?php
	}
}

/*
 * Search Widget
 */
function shapely_search_form( $form ) {
	$form = '<form role="search" method="get" id="searchform" class="search-form" action="' . esc_url( home_url( '/' ) ) . '" >
    <label class="screen-reader-text" for="s">' . esc_html__( 'Search for:', 'shapely' ) . '</label>
    <input type="text" placeholder="' . esc_html__( 'Type Here', 'shapely' ) . '" value="' . esc_attr( get_search_query() ) . '" name="s" id="s" />
    <button type="submit" class="searchsubmit"><i class="fa fa-search" aria-hidden="true"></i><span class="screen-reader-text">' . esc_attr__( 'Search', 'shapely' ) . '</span></button>
    </form>';

	return $form;
}

add_filter( 'get_search_form', 'shapely_search_form', 100 );


/*
 * Author bio on single page
 */
if ( ! function_exists( 'shapely_author_bio' ) ) {

	function shapely_author_bio() {

		if ( ! get_the_ID() ) {
			return;
		}

		$author_displayname       = get_the_author_meta( 'display_name' );
		$author_nickname          = get_the_author_meta( 'nickname' );
		$author_fullname          = ( '' != get_the_author_meta( 'first_name' ) && '' != get_the_author_meta( 'last_name' ) ) ? get_the_author_meta( 'first_name' ) . ' ' . get_the_author_meta( 'last_name' ) : '';
		$author_email             = get_the_author_meta( 'email' );
		$author_description       = get_the_author_meta( 'description' );
		$author_name              = ( '' != trim( $author_nickname ) ) ? $author_nickname : ( trim( $author_displayname ) != '' ) ? $author_displayname : $author_fullname;
		$show_athor_email         = get_theme_mod( 'post_author_email', false );
		$show_project_athor_email = get_theme_mod( 'project_author_email', false );
		?>

		<div class="author-bio">
			<div class="row">
				<div class="col-sm-2">
					<div class="avatar">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
					</div>
				</div>
				<div class="col-sm-10">
					<span class="vcard author"><b class="fn"><?php echo esc_html( $author_name ); ?></b></span>
					<div>
						<?php
						if ( '' != trim( $author_description ) ) {
							echo wp_kses_post( $author_description );
						}
						?>
					</div>
					<?php if ( ( $show_athor_email && ! is_singular( 'jetpack-portfolio' ) ) || ( is_singular( 'jetpack-portfolio' ) && $show_project_athor_email ) ) : ?>
						<a class="author-email" href="mailto:<?php echo esc_attr( antispambot( $author_email ) ); ?>"><?php echo esc_html( antispambot( $author_email ) ); ?></a>
					<?php endif ?>
					<ul class="list-inline social-list author-social">
						<?php
						$twitter_profile = get_the_author_meta( 'twitter' );
						if ( $twitter_profile && '' != $twitter_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $twitter_profile ); ?>">
									<i class="fa fa-twitter"></i>
								</a>
							</li>
							<?php
						}

						$fb_profile = get_the_author_meta( 'facebook' );
						if ( $fb_profile && '' != $fb_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $fb_profile ); ?>">
									<i class="fa fa-facebook"></i>
								</a>
							</li>
							<?php
						}

						$dribble_profile = get_the_author_meta( 'dribble' );
						if ( $dribble_profile && '' != $dribble_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $dribble_profile ); ?>">
									<i class="fa fa-dribbble"></i>
								</a>
							</li>
							<?php
						}

						$github_profile = get_the_author_meta( 'github' );
						if ( $github_profile && '' != $github_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $github_profile ); ?>">
									<i class="fa fa-github"></i>
								</a>
							</li>
							<?php
						}

						$vimeo_profile = get_the_author_meta( 'vimeo' );
						if ( $vimeo_profile && '' != $vimeo_profile ) {
							?>
							<li>
								<a href="<?php echo esc_url( $vimeo_profile ); ?>">
									<i class="fa fa-vimeo"></i>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<!--end of author-bio-->
		<?php
	}
} // End if().

/**
 * Custom comment template
 */
function shapely_cb_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'ul' == $args['style'] ) {
		$tag       = 'ul';
		$add_below = 'comment';
	} else {
		$tag       = 'li';
		$add_below = 'div-comment';
	}
	?>
	<li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<?php endif; ?>
			<div class="avatar">
				<?php
				if ( 0 != $args['avatar_size'] ) {
					echo get_avatar( $comment, $args['avatar_size'] );
				}
				?>
			</div>
			<div class="comment">
				<b class="fn"><?php echo esc_html( get_comment_author() ); ?></b>
				<div class="comment-date">
					<time datetime="2016-01-28T12:43:17+00:00">
						<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s at %2$s', 'shapely' ), get_comment_date(), get_comment_time() );
						?>
					</time>
					<?php
					edit_comment_link( esc_html__( 'Edit', 'shapely' ), '  ', '' );
					?>
				</div>
				<?php
				$comment_reply_args = array(
					'add_below' => $add_below,
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
				);

				comment_reply_link( array_merge( $args, $comment_reply_args ) );
				?>

				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p>
						<em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'shapely' ); ?></em>
						<br />
					</p>
				<?php endif; ?>

				<?php comment_text(); ?>

			</div>
			<?php if ( 'div' != $args['style'] ) : ?>
		</div>
	<?php endif; ?>
	</li>
	<?php
}

/*
 * Filter to replace
 * Reply button class
 */
function shapely_reply_link_class( $class ) {
	$class = str_replace( "class='comment-reply-link", "class='btn btn-xs comment-reply", $class );

	return $class;
}

/*
 * Comment form template
 */
function shapely_custom_comment_form() {
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$fields    = array(
		'author' => '<input id="author" placeholder="' . esc_html__( 'Your Name', 'shapely' ) . ( $req ? '*' : '' ) . '" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" ' . $aria_req . ' required="required" />',
		'email'  => '<input id="email" name="email" type="email" placeholder="' . esc_html__( 'Email Address', 'shapely' ) . ( $req ? '*' : '' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' required="required" />',
		'url'    => '<input placeholder="' . esc_html__( 'Your Website (optional)', 'shapely' ) . '" id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />',
	);

	$comments_args = array(
		'label_submit'  => esc_html__( 'Leave Comment', 'shapely' ),
		'comment_field' => '<textarea placeholder="' . _x( 'Comment', 'noun', 'shapely' ) . '" id="comment" name="comment" cols="45" rows="8" aria-required="true" required="required"></textarea>',
		'fields'        => apply_filters( 'comment_form_default_fields', $fields ),
	);

	return $comments_args;
}

/*
 * Header Logo
 */
function shapely_get_header_logo() {

	$logo_dimensions = get_theme_mod( 'shapely_logo_dimension', array() );
	if ( ! empty( $logo_dimensions ) && isset( $logo_dimensions['width'] ) && isset( $logo_dimensions['height'] ) ) {
		$dimension = array( $logo_dimensions['width'], $logo_dimensions['height'] );
	} else {
		$dimension = 'full';
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class'    => 'custom-logo logo',
			'itemprop' => 'logo',
		);
		/*
		 * If the logo alt attribute is empty, get the site title and explicitly
		 * pass it to the attributes used by wp_get_attachment_image().
		 */
		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}
		/*
		 * If the alt attribute is not empty, there's no need to explicitly pass
		 * it because wp_get_attachment_image() already adds the alt attribute.
		 */
		$html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>', esc_url( home_url( '/' ) ), wp_get_attachment_image( $custom_logo_id, $dimension, false, $custom_logo_attr ) );
	} // If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
	elseif ( is_customize_preview() ) {
		$html = sprintf( '<a href="%1$s" class="custom-logo-link"><img class="custom-logo"/ style="display:none;"><span class="site-title">%2$s</span></a>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
	} else {
		$html = sprintf( '<a href="%1$s" class="custom-logo-link"><span class="site-title">%2$s</span></a>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
	}

	echo $html;

}

/*
 * Get layout class from single page
 * then from themeoptions
 */
function shapely_get_layout_class() {
	if ( is_singular( 'jetpack-portfolio' ) ) {
		$layout_class = get_theme_mod( 'single_project_layout_template', 'sidebar-right' );
	} elseif ( is_single() ) {
		$template     = get_page_template_slug();
		$layout_class = '';
		switch ( $template ) {
			case 'page-templates/full-width.php':
				$layout_class = 'full-width';
				break;
			case 'page-templates/no-sidebar.php':
				$layout_class = 'no-sidebar';
				break;
			case 'page-templates/sidebar-left.php':
				$layout_class = 'sidebar-left';
				break;
			case 'page-templates/sidebar-right.php':
				$layout_class = 'sidebar-right';
				break;
			default:
				$layout_class = get_theme_mod( 'single_post_layout_template', 'sidebar-right' );
				break;
		}
	} elseif ( is_singular() ) {
		$template     = get_page_template_slug();
		$layout_class = '';
		switch ( $template ) {
			case 'page-templates/full-width.php':
				$layout_class = 'full-width';
				break;
			case 'page-templates/no-sidebar.php':
				$layout_class = 'no-sidebar';
				break;
			case 'page-templates/sidebar-left.php':
				$layout_class = 'sidebar-left';
				break;
			case 'page-templates/sidebar-right.php':
				$layout_class = 'sidebar-right';
				break;
			default:
				$layout_class = get_theme_mod( 'blog_layout_template', 'sidebar-right' );
				break;
		}
	} elseif ( is_archive() && is_post_type_archive( 'jetpack-portfolio' ) ) {
		$layout_class = get_theme_mod( 'projects_layout_template', 'full-width' );
	} else {
		$layout_class = get_theme_mod( 'blog_layout_template', 'sidebar-right' );
	} // End if().

	return $layout_class;
}

/*
 * Show Sidebar or not
 */
function shapely_show_sidebar() {
	global $post;
	$show_sidebar = true;
	if ( is_singular() && ( get_post_meta( $post->ID, 'site_layout', true ) ) ) {
		if ( get_post_meta( $post->ID, 'site_layout', true ) == 'no-sidebar' || get_post_meta( $post->ID, 'site_layout', true ) == 'full-width' ) {
			$show_sidebar = false;
		}
	} elseif ( get_theme_mod( 'shapely_sidebar_position' ) == 'no-sidebar' || get_theme_mod( 'shapely_sidebar_position' ) == 'full-width' ) {
		$show_sidebar = false;
	}

	return $show_sidebar;
}

/*
 * Top Callout
 */
function shapely_top_callout() {
	if ( ( get_theme_mod( 'portfolio_archive_title', true ) && is_post_type_archive( 'jetpack-portfolio' ) ) || ( get_theme_mod( 'top_callout', true ) && ! is_single() && ! is_post_type_archive( 'jetpack-portfolio' ) ) || ( is_single() && get_theme_mod( 'title_in_header', true ) && ! is_singular( 'jetpack-portfolio' ) ) || ( get_theme_mod( 'project_title_in_header', true ) && is_singular( 'jetpack-portfolio' ) ) ) {
		$header = get_header_image();
		?>
		<section class="page-title-section bg-secondary <?php echo $header ? 'header-image-bg' : ''; ?>" <?php echo $header ? 'style="background-image:url(' . esc_url( $header ) . ')"' : ''; ?>>
			<div class="container">
				<div class="row">
					<?php
					$breadcrumbs_enabled = false;
					$title_in_post       = true;
					if ( function_exists( 'yoast_breadcrumb' ) ) {
						$options             = get_option( 'wpseo_internallinks' );
						$breadcrumbs_enabled = ( true === $options['breadcrumbs-enable'] );
						$title_in_post       = get_theme_mod( 'hide_post_title', true );
					}
					$header_color = get_theme_mod( 'header_textcolor', false );
					?>
					<?php if ( $title_in_post ) : ?>
						<div class="<?php echo $breadcrumbs_enabled ? 'col-md-6 col-sm-6 col-xs-12' : 'col-xs-12'; ?>">
							<h3 class="page-title" <?php echo $header_color ? 'style="color:#' . esc_attr( $header_color ) . '"' : ''; ?>>
								<?php
								if ( is_home() ) {
									echo esc_html( get_theme_mod( 'blog_name' ) ? get_theme_mod( 'blog_name' ) : __( 'Blog', 'shapely' ) );
								} elseif ( is_search() ) {
									echo esc_html__( 'Search', 'shapely' );
								} elseif ( is_archive() ) {
									if ( is_post_type_archive( 'jetpack-portfolio' ) ) {
										$portfolio_title = get_theme_mod( 'portfolio_name', esc_html__( 'Portfolio', 'shapely' ) );
										echo $portfolio_title;
									} else {
										echo get_the_archive_title();
									}
								} elseif ( is_singular() ) {
									echo single_post_title();
								} else {
									echo get_the_title();
								}
								?>
							</h3>
							<?php

							if ( is_archive() && is_post_type_archive( 'jetpack-portfolio' ) ) {
								$portfolio_description = get_theme_mod( 'portfolio_description' );
								if ( $portfolio_description ) {
									echo '<p>' . wp_kses_post( nl2br( $portfolio_description ) ) . '</p>';
								}
							}

							?>
						</div>
					<?php endif; ?>
					<?php if ( function_exists( 'yoast_breadcrumb' ) ) { ?>
						<?php
						if ( $breadcrumbs_enabled ) {
							?>
							<div class="<?php echo $title_in_post ? 'col-md-6 col-sm-6' : ''; ?> col-xs-12 text-right">
								<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
							</div>
						<?php } ?>
					<?php } ?>

				</div>
				<!--end of row-->
			</div>
			<!--end of container-->
		</section>
		<?php
	} else {
		?>
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) { ?>
			<div class="container mt20">
				<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
			</div>
			<?php
}
	} // End if().
}

/*
 * Footer Callout
 */
function shapely_footer_callout() {
	if ( get_theme_mod( 'footer_callout_text' ) != '' ) {
		?>
		<section class="cfa-section bg-secondary">
			<div class="container">
				<div class="row">
					<div class="col-sm-12 text-center p0">
						<div class="overflow-hidden">
							<div class="col-sm-9">
								<h3 class="cfa-text"><?php echo wp_kses_post( nl2br( get_theme_mod( 'footer_callout_text' ) ) ); ?></h3>
							</div>
							<div class="col-sm-3">
								<a href="<?php echo esc_url( get_theme_mod( 'footer_callout_link' ) ); ?>" class="mb0 btn btn-lg btn-filled cfa-button">
									<?php echo wp_kses_post( get_theme_mod( 'footer_callout_btntext' ) ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

// Check WooCommerce
if ( ! function_exists( 'shapely_is_woocommerce_activated' ) ) {
	function shapely_is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}
}
