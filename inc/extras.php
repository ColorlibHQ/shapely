<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Shapely
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
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

	if ( get_theme_mod( 'shapely_sidebar_position' ) == "pull-right" ) {
		$classes[] = 'has-sidebar-left';
	} else if ( get_theme_mod( 'shapely_sidebar_position' ) == "no-sidebar" ) {
		$classes[] = 'has-no-sidebar';
	} else if ( get_theme_mod( 'shapely_sidebar_position' ) == "full-width" ) {
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
  if ( $title == '' ) {
    return 'Untitled';
  } else {
    return $title;
  }
}

/**
 * Password protected post form using Boostrap classes
 */
add_filter( 'the_password_form', 'custom_password_form' );

function custom_password_form() {
  global $post;
  $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
  $o = '<form class="protected-post-form" action="' . get_option('siteurl') . '/wp-login.php?action=postpass" method="post">
  <div class="row">
    <div class="col-lg-10">
        <p>' . esc_html__( "This post is password protected. To view it please enter your password below:" ,'shapely') . '</p>
        <label for="' . $label . '">' . esc_html__( "Password:" ,'shapely') . ' </label>
      <div class="input-group">
        <input class="form-control" value="' . get_search_query() . '" name="post_password" id="' . $label . '" type="password">
        <span class="input-group-btn"><button type="submit" class="btn btn-default" name="submit" id="searchsubmit" value="' . esc_attr__( "Submit",'shapely' ) . '">' . esc_html__( "Submit" ,'shapely') . '</button>
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
  return preg_replace( '/(<table) ?(([^>]*)class="([^"]*)")?/', '$1 $3 class="$4 table table-hover" ', $content);
}

if ( ! function_exists( 'shapely_header_menu' ) ) :
/**
 * Header menu (should you choose to use one)
 */
function shapely_header_menu() {
  // display the WordPress Custom Menu if available
  wp_nav_menu(array(
    'menu'              => 'primary',
    'menu_id'           => 'menu',
    'theme_location'    => 'primary',
    'depth'             => 3,
    'container'         => 'div',
    'container_class'   => 'collapse navbar-collapse navbar-ex1-collapse',
    'menu_class'        => 'menu',
    'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
    'walker'            => new wp_bootstrap_navwalker()
  ));
} /* end header menu */
endif;

/**
 * function to show the footer info, copyright information
 */
function shapely_footer_info() {
  printf( esc_html__( 'Theme by %1$s Powered by %2$s', 'shapely' ) , '<a href="https://colorlib.com/" target="_blank" title="Colorlib">Colorlib</a>', '<a href="http://wordpress.org/" target="_blank" title="WordPress.org">WordPress</a>');
}


if ( ! function_exists( 'get_shapely_theme_options' ) ) {
/**
 * Get information from Theme Options and add it into wp_head
 */
    function get_shapely_theme_options(){

      echo '<style type="text/css">';

      if ( get_theme_mod('link_color')) {
        echo 'a {color:' . get_theme_mod('link_color') . '}';
      }
      if ( get_theme_mod('link_hover_color')) {
        echo 'a:hover, a:active, .post-title a:hover,
        .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover,
        .woocommerce nav.woocommerce-pagination ul li span.current  { color: '.get_theme_mod('link_hover_color').';}';
      }

      if ( get_theme_mod('button_color')) {
        echo '.btn-filled, .btn-filled:visited, .woocommerce #respond input#submit.alt,
          .woocommerce a.button.alt, .woocommerce button.button.alt,
          .woocommerce input.button.alt, .woocommerce #respond input#submit,
          .woocommerce a.button, .woocommerce button.button,
          .woocommerce input.button { background:' . get_theme_mod('button_color') . ' !important; border: 2px solid'. get_theme_mod('button_color').' !important;}';
      }
      if ( get_theme_mod('button_hover_color')) {
        echo '.btn-filled:hover, .woocommerce #respond input#submit.alt:hover,
          .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover,
          .woocommerce input.button.alt:hover, .woocommerce #respond input#submit:hover,
          .woocommerce a.button:hover, .woocommerce button.button:hover,
          .woocommerce input.button:hover  { background: '.get_theme_mod('button_hover_color').' !important; border: 2px solid'. get_theme_mod('button_hover_color').' !important;}';
      }

      if ( get_theme_mod('social_color')) {
        echo '.social-icons li a {color: '.get_theme_mod('social_color').' !important ;}';
      }

      if ( get_theme_mod('custom_css')) {
        echo html_entity_decode( get_theme_mod( 'custom_css', 'no entry' ) );
      }
        echo '</style>';
    }
}
add_action( 'wp_head', 'get_shapely_theme_options', 10 );

/**
 * Add Bootstrap thumbnail styling to images with captions
 * Use <figure> and <figcaption>
 *
 * @link http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
 */
function shapely_caption($output, $attr, $content) {
  if (is_feed()) {
    return $output;
  }

  $defaults = array(
    'id'      => '',
    'align'   => 'alignnone',
    'width'   => '',
    'caption' => ''
  );

  $attr = shortcode_atts($defaults, $attr);

  // If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
  if ($attr['width'] < 1 || empty($attr['caption'])) {
    return $content;
  }

  // Set up the attributes for the caption <figure>
  $attributes  = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '' );
  $attributes .= ' class="thumbnail wp-caption ' . esc_attr($attr['align']) . '"';
  $attributes .= ' style="width: ' . (esc_attr($attr['width']) + 10) . 'px"';

  $output  = '<figure' . $attributes .'>';
  $output .= do_shortcode($content);
  $output .= '<figcaption class="caption wp-caption-text">' . $attr['caption'] . '</figcaption>';
  $output .= '</figure>';

  return $output;
}
add_filter('img_caption_shortcode', 'shapely_caption', 10, 3);

/**
 * Skype URI support for social media icons
 */
function shapely_allow_skype_protocol( $protocols ){
    $protocols[] = 'skype';
    return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'shapely_allow_skype_protocol' );

/**
 * Adds the URL to the top level navigation menu item
 */
function  shapely_add_top_level_menu_url( $atts, $item, $args ){
  if ( !wp_is_mobile() && isset($args->has_children) && $args->has_children  ) {
    $atts['href'] = ! empty( $item->url ) ? $item->url : '';
  }
  return $atts;
}
add_filter( 'nav_menu_link_attributes', 'shapely_add_top_level_menu_url', 99, 3 );

/**
 * Makes the top level navigation menu item clickable
 */
function shapely_make_top_level_menu_clickable(){
if ( !wp_is_mobile() ) { ?>
  <script type="text/javascript">
    jQuery( document ).ready( function( $ ){
      if ( $( window ).width() >= 767 ){
        $( '.navbar-nav > li.menu-item > a' ).click( function(){
          window.location = $( this ).attr( 'href' );
        });
      }
    });
  </script>
<?php }
}
add_action('wp_footer', 'shapely_make_top_level_menu_clickable', 1);

/*
 * Add Read More button to post archive
 */
function shapely_excerpt_more( $more ) {
	return '<div><a class="btn-filled btn" href="'.get_the_permalink().'" title="'.get_the_title().'">'.esc_html_x( 'Read More', 'Read More', 'shapely' ).'</a></div>';
}
add_filter('excerpt_more', 'shapely_excerpt_more');

/*
 * Pagination
 */
if( !function_exists('shapely_pagination') ){

function shapely_pagination($pages = '', $range = 2){
    global $paged;
    $showitems = ( $range * 2 )+1;

    if( empty( $paged) ) $paged = 1;

    if($pages == ''){
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if(!$pages){
            $pages = 1;
        }
    }

    if(1 != $pages)
    {
        echo "<div class=\"text-center\"><ul class=\"pagination\">";
        //if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
        if($paged > 1 && $showitems < $pages) echo "</li><a aria-label=\"Previous\" href='".get_pagenum_link($paged - 1)."'><span aria-hidden=\"true\">&laquo;</span></a></li>";
        for ($i=1; $i <= $pages; $i++)
        {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
                echo ($paged == $i)? "<li class=\"active\"><a href=\"#\">".$i."</li>":"<li><a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a></li>";
            }
        }
        if ($paged < $pages && $showitems < $pages) echo "<li><a aria-label=\"Next\" href='".get_pagenum_link($paged + 1)."'><span aria-hidden=\"true\">&raquo;</span></a></li>";
        //if ($paged < ($pages-1) && ( $paged+$range-1 < $pages ) && $showitems < $pages) echo '<a href="'. get_pagenum_link($pages).'">Last &raquo;</a>';
        echo "</ul></div>\n";
    }
}}

/*
 * Search Widget
 */
function shapely_search_form( $form ) {
    $form = '<form role="search" method="get" id="searchform" class="search-form" action="' . home_url( '/' ) . '" >
    <label class="screen-reader-text" for="s">' . __( 'Search for:', 'shapely' ) . '</label>
    <input type="text" placeholder="'.  __('Type Here', 'shapely').'" type="text" value="' . get_search_query() . '" name="s" id="s" />
    <input type="submit" class="btn btn-fillded searchsubmit" id="searchsubmit" value="'. esc_attr__( 'Search', 'shapely' ) .'" />

    </form>';

    return $form;
}

add_filter( 'get_search_form', 'shapely_search_form', 100 );

/*
 * Admin Css
 */
function shapely_admin_style() {
  echo '<style>
        #setting-error-tgmpa.notice{  display : block; }
        .client-sortable .logo_heading{
                        background: #f3f3f3;
                        border: 1px dotted;
                        cursor: move;
                        display: block;
                        font-size: 14px;
                        padding: 8px 0;
                        text-align: center;
                        width: 100%;
                      }
        .client-sortable .logo_heading:hover{
                      border: 1px solid;
                    }
        .client-sortable .cloneya a.clone,
        .client-sortable .cloneya a.delete { display: none; }

        .client-sortable .cloneya:last-child a.clone,
        .client-sortable .cloneya:last-child a.delete { display: inline-block; }

      </style>';
}
add_action('admin_head', 'shapely_admin_style');
add_action('customize_controls_print_styles', 'shapely_admin_style');

/* Social Fields in Author Profile */
if( !function_exists('shapely_author_socialLinks') ){

  function shapely_author_socialLinks( $contactmethods ) {
    // Add Twitter
    $contactmethods['twitter'] = 'Twitter';
    //add Facebook
    $contactmethods['facebook'] = 'Facebook';
    //add Github
    $contactmethods['github'] = 'Github';
    //add Dribble
    $contactmethods['dribble'] = 'Dribble';
    //add Vimeo
    $contactmethods['vimeo'] = 'Vimeo';

    return $contactmethods;
  }
}

add_filter('user_contactmethods','shapely_author_socialLinks',10,1);


/*
 * Author bio on single page
 */
if( !function_exists('shapely_author_bio') ){

function shapely_author_bio(){

  if( !get_the_ID() )
    return;

  $author_fields = "'user_url','display_name', 'nickname', 'first_name','last_name','description', 'ID'";
  $author_displayname = get_the_author_meta('display_name');
  $author_nickname = get_the_author_meta('nickname');
  $author_fullname = ( get_the_author_meta('first_name') != "" && get_the_author_meta('last_name') != "" ) ? get_the_author_meta('first_name')." ".get_the_author_meta('last_name') : "";
  $author_url = get_the_author_meta('user_url');
  $author_description = get_the_author_meta('description');
  $author_name = ( trim( $author_nickname ) != "" ) ? $author_nickname : ( trim( $author_displayname ) != "" ) ? $author_displayname : $author_fullname ?>

  <div class="author-bio">
    <div class="row">
      <div class="col-sm-2">
        <div class="avatar">
            <?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
        </div>
      </div>
      <div class="col-sm-10">
        <b class="fn"><?php echo $author_name; ?></b>
        <p><?php
          if( trim( $author_description ) != "" )
            echo $author_description; ?>
        </p>
        <ul class="list-inline social-list author-social">
            <?php
              $twitter_profile = get_the_author_meta( 'twitter' );
              if ( $twitter_profile && $twitter_profile != '' ) { ?>
              <li>
                <a href="<?php echo esc_url($twitter_profile); ?>">
                    <i class="fa fa-twitter"></i>
                </a>
              </li><?php
              }

              $fb_profile = get_the_author_meta( 'facebook' );
              if ( $fb_profile && $fb_profile != '' ) { ?>
              <li>
                <a href="<?php echo esc_url($fb_profile); ?>">
                  <i class="fa fa-facebook"></i>
                </a>
              </li><?php
              }

              $dribble_profile = get_the_author_meta( 'dribble' );
              if ( $dribble_profile && $dribble_profile != '' ) { ?>
              <li>
                <a href="<?php echo esc_url($dribble_profile); ?>">
                  <i class="fa fa-dribbble"></i>
                </a>
              </li>
                <?php
              }

              $github_profile = get_the_author_meta( 'github' );
              if ( $github_profile && $github_profile != '' ) { ?>
              <li>
                <a href="<?php echo esc_url($github_profile); ?>">
                  <i class="fa fa-vimeo"></i>
                </a>
              </li><?php
              }

              $vimeo_profile = get_the_author_meta( 'vimeo' );
              if ( $vimeo_profile && $vimeo_profile != '' ) { ?>
              <li>
                <a href="<?php echo esc_url($vimeo_profile); ?>">
                  <i class="fa fa-github"></i>
                </a>
              </li><?php
              } ?>
        </ul>
      </div>
    </div>
  </div>
  <!--end of author-bio-->
<?php

}
}

/**
 * Custom comment template
 */
function shapely_cb_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

    if ( 'ul' == $args['style'] ) {
		$tag = 'ul';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
    ?>
	<li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
      <?php if ( 'div' != $args['style'] ) : ?>
      <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
      <?php endif; ?>
        <div class="avatar">
          <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
        </div>
        <div class="comment">
            <b class="fn"><?php echo get_comment_author(); ?></b>
            <div class="comment-date">
              <time datetime="2016-01-28T12:43:17+00:00">
              <?php
                /* translators: 1: date, 2: time */
                printf( __( '%1$s at %2$s', 'shapely' ), get_comment_date(), get_comment_time() ); ?></time><?php edit_comment_link( __( 'Edit', 'shapely' ), '  ', '' );
              ?>
            </div>

            <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

            <?php if ( $comment->comment_approved == '0' ) : ?>
              <p>
                  <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'shapely' ); ?></em>
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
function shapely_reply_link_class($class){
    $class = str_replace("class='comment-reply-link", "class='btn btn-sm comment-reply", $class);
    return $class;
}

/*
 * Comment form template
 */
function shapely_custom_comment_form(){
    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $fields =  array(
      'author' =>
        '<input id="author" placeholder="'.__( 'Your Name', 'shapely' ).( $req ? '*' : '' ).'" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
        '" size="30"' . $aria_req . ' required="required" />',

      'email' =>
        '<input id="email" name="email" type="email" placeholder="'. __( 'Email Address', 'shapely' ) .( $req ? '*' : '' ).'" value="' . esc_attr(  $commenter['comment_author_email'] ) .
        '" size="30"' . $aria_req . ' required="required" />',

      'url' =>
        '<input placeholder="'.__( 'Your Website (optional)', 'shapely' ).'" id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
        '" size="30" />',
    );

    $comments_args = array(
        'label_submit'      => __( 'Leave Comment', 'shapely' ),
        'comment_field' =>  '<textarea placeholder="'._x( 'Comment', 'noun', 'shapely' ).'" id="comment" name="comment" cols="45" rows="8" aria-required="true" required="required">' .
                            '</textarea>',
        'fields' => apply_filters( 'comment_form_default_fields', $fields )
    );
    return $comments_args;
}

/*
 * Header Logo
 */
function shapely_get_header_logo(){
  $logo_id = get_theme_mod('header_logo', '');
  $logo = wp_get_attachment_image_src($logo_id, 'full'); ?>

  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php
  if( $logo[0] != '' ){ ?>
    <img src="<?php echo $logo[0]; ?>" class="logo" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>"><?php
  }
  else{ ?>
    <h1 class="site-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1><?php
  } ?>
  </a><?php
}

/*
 * Get layout class from single page
 * then from themeoptions
 */
function shapely_get_layout_class(){
  global $post;
  if( is_singular() && get_post_meta($post->ID, 'site_layout', true) ){
      $layout_class = get_post_meta($post->ID, 'site_layout', true);
  }
  else{
      $layout_class = get_theme_mod( 'shapely_sidebar_position', 'side-right' );
  }
  return $layout_class;
}

/*
 * Show Sidebar or not
 */
function shapely_show_sidebar(){
  global $post;
  $show_sidebar = true;
  if( is_singular() && ( get_post_meta($post->ID, 'site_layout', true) ) ){
     if( get_post_meta($post->ID, 'site_layout', true) == 'no-sidebar' || get_post_meta($post->ID, 'site_layout', true) == 'full-width' ) {
         $show_sidebar = false;
     }
  }
  elseif( get_theme_mod( 'shapely_sidebar_position' ) == "no-sidebar" ||  get_theme_mod( 'shapely_sidebar_position' ) == "full-width" ) {
      $show_sidebar = false;
  }
  return $show_sidebar;
}

/*
 * Top Callout
 */
function shapely_top_callout(){
  if( get_theme_mod('top_callout', true) ) { ?>
    <section class="page-title-section bg-secondary">
      <div class="container">
          <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-12">
                  <h3 class="page-title">
                      <?php
                      if( is_home() ){
                        _e( ( get_theme_mod('blog_name') ) ? get_theme_mod('blog_name') : 'Blog'  , 'shapely' );
                      }
                      else if( is_search() ){
                        _e( 'Search'  , 'shapely' );
                      }
                      else if ( is_archive() ) {
                        echo ( is_post_type_archive('jetpack-portfolio') ) ? __('Portfolio', 'shapely') : get_the_archive_title();
                      }
                      else {
                        echo ( is_singular('jetpack-portfolio') ) ? __('Portfolio', 'shapely') : get_the_title();
                      }?>
                  </h3>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12 text-right">
              <?php if ( function_exists('yoast_breadcrumb') ) {
                  yoast_breadcrumb('<p id="breadcrumbs">','</p>');
              } ?>
              </div>
          </div>
          <!--end of row-->
      </div>
      <!--end of container-->
    </section><?php
  }
  else { ?>
    <?php if ( function_exists('yoast_breadcrumb') ) { ?>
      <div class="container mt20"><?php
          yoast_breadcrumb('<p id="breadcrumbs">','</p>'); ?>
      </div><?php
      }
  }
}

/*
 * Footer Callout
 */
function shapely_footer_callout(){
  if( get_theme_mod('footer_callout_text') != '' ){ ?>
    <section class="cfa-section bg-secondary">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 text-center p0">
              <div class="overflow-hidden">
                <div class="col-sm-9">
                  <h3 class="cfa-text"><?php esc_attr(get_theme_mod('footer_callout_text')); ?></h3>
                </div>
                <div class="col-sm-3">
                  <a href='<?php echo esc_url( get_theme_mod('footer_callout_link') ); ?>' class="mb0 btn btn-lg btn-filled cfa-button">
                   <?php esc_attr( get_theme_mod('footer_callout_btntext'), 'shapely'); ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <!--end of row-->
        </div>
        <!--end of container-->
    </section><?php
  }
}
