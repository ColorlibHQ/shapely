<?php

/**
 * Shapely functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Shapely
 */
if ( ! function_exists( 'shapely_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function shapely_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Shapely, use a find and replace
		 * to change 'shapely' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'shapely', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Add support for the custom logo functionality
		 */
		add_theme_support( 'custom-logo', array(
			'height'     => 55,
			'width'      => 135,
			'flex-width' => true,
		) );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			                    'primary' => esc_html__( 'Primary', 'shapely' ),
		                    ) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'shapely_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'shapely-featured', 848, 566, true );

		// Welcome screen
		if ( is_admin() ) {
			global $shapely_required_actions, $shapely_recommended_plugins;

			$shapely_recommended_plugins = array(
				'fancybox-for-wordpress' => array( 'recommended' => false )
			);

			/*
			 * id - unique id; required
			 * title
			 * description
			 * check - check for plugins (if installed)
			 * plugin_slug - the plugin's slug (used for installing the plugin)
			 *
			 */
			$shapely_required_actions = array(
				array(
					"id"          => 'shapely-req-ac-install-companion-plugin',
					"title"       => Shapely_Notify_System::shapely_companion_title(),
					"description" => Shapely_Notify_System::shapely_companion_description(),
					"check"       => Shapely_Notify_System::shapely_has_plugin( 'shapely-companion' ),
					"plugin_slug" => 'shapely-companion'
				),
				array(
					"id"          => 'shapely-req-ac-install-wp-jetpack-plugin',
					"title"       => Shapely_Notify_System::shapely_jetpack_title(),
					"description" => Shapely_Notify_System::shapely_jetpack_description(),
					"check"       => Shapely_Notify_System::shapely_has_plugin( 'jetpack' ),
					"plugin_slug" => 'jetpack'
				),
				array(
					"id"          => 'shapely-req-ac-install-wp-yoast-plugin',
					"title"       => Shapely_Notify_System::shapely_yoast_title(),
					'description' => Shapely_Notify_System::shapely_yoast_description(),
					"check"       => Shapely_Notify_System::shapely_has_plugin( 'wordpress-seo' ),
					"plugin_slug" => 'wordpress-seo'
				),
				array(
					"id"          => 'shapely-req-import-content',
					"title"       => esc_html__( 'Import content', 'shapely' ),
					"external"    => ABSPATH . 'wp-content/plugins/shapely-companion/inc/views/shapely-demo-content.php',
					"check"       => Shapely_Notify_System::shapely_check_import_req(),
				),

			);

			require get_template_directory() . '/inc/admin/welcome-screen/welcome-screen.php';
		}
	}
endif;
add_action( 'after_setup_theme', 'shapely_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function shapely_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'shapely_content_width', 1140 );
}

add_action( 'after_setup_theme', 'shapely_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function shapely_widgets_init() {
	register_sidebar( array(
		                  'id'            => 'sidebar-1',
		                  'name'          => __( 'Sidebar', 'shapely' ),
		                  'before_widget' => '<div id="%1$s" class="widget %2$s">',
		                  'after_widget'  => '</div>',
		                  'before_title'  => '<h2 class="widget-title">',
		                  'after_title'   => '</h2>',
	                  ) );

	register_sidebar( array(
		                  'id'            => 'sidebar-home',
		                  'name'          => __( 'Homepage', 'shapely' ),
		                  'before_widget' => '<div id="%1$s" class="widget %2$s">',
		                  'after_widget'  => '</div>',
		                  'before_title'  => '<h2 class="widget-title">',
		                  'after_title'   => '</h2>',
	                  ) );

	for ( $i = 1; $i < 5; $i ++ ) {
		register_sidebar( array(
			                  'id'            => 'footer-widget-' . $i,
			                  'name'          => sprintf( __( 'Footer Widget %s', 'shapely' ), $i ),
			                  'description'   => __( 'Used for footer widget area', 'shapely' ),
			                  'before_widget' => '<div id="%1$s" class="widget %2$s">',
			                  'after_widget'  => '</div>',
			                  'before_title'  => '<h2 class="widget-title">',
			                  'after_title'   => '</h2>',
		                  ) );
	}

}

add_action( 'widgets_init', 'shapely_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function shapely_scripts() {
	// Add Bootstrap default CSS
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/inc/css/bootstrap.min.css' );

	// Add Font Awesome stylesheet
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/inc/css/font-awesome.min.css' );

	// Add Google Fonts
	wp_enqueue_style( 'shapely-fonts', '//fonts.googleapis.com/css?family=Raleway:100,300,400,500,600,700%7COpen+Sans:400,500,600' );


	// Add slider CSS
	wp_enqueue_style( 'flexslider', get_template_directory_uri() . '/inc/css/flexslider.css' );

	//Add custom theme css
	wp_enqueue_style( 'shapely-style', get_stylesheet_uri() );


	wp_enqueue_script( 'shapely-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'shapely-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}


	if ( post_type_exists( 'jetpack-portfolio' ) ) {
		wp_enqueue_script( 'jquery-masonry' );
	}

	// Add slider JS
	wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/js/flexslider.min.js', array( 'jquery' ), '20160222', true );

	if ( is_page_template( 'template-home.php' ) ) {
		wp_enqueue_script( 'shapely-parallax', get_template_directory_uri() . '/js/parallax.min.js', array( 'jquery' ), '20160115', true );
	}

	wp_enqueue_script( 'shapely-scripts', get_template_directory_uri() . '/js/shapely-scripts.js', array( 'jquery' ), '20160115', true );
}

add_action( 'wp_enqueue_scripts', 'shapely_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom nav walker
 */
require get_template_directory() . '/inc/navwalker.php';

/**
 * Load Social Navition
 */
require get_template_directory() . '/inc/socialnav.php';

/**
 * Load Metboxes
 */
require get_template_directory() . '/inc/metaboxes.php';

/* Globals */
global $shapely_site_layout;
$shapely_site_layout = array(
	'pull-right' => esc_html__( 'Left Sidebar', 'shapely' ),
	'side-right' => esc_html__( 'Right Sidebar', 'shapely' ),
	'no-sidebar' => esc_html__( 'No Sidebar', 'shapely' ),
	'full-width' => esc_html__( 'Full Width', 'shapely' )
);


/**
 * Load the system checks ( used for notifications )
 */
require get_template_directory() . '/inc/admin/welcome-screen/notify-system-checks.php';