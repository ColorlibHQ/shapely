<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shapely
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ensure WordPress is loaded
if ( ! function_exists( 'get_theme_mod' ) ) {
	die( 'WordPress is not loaded properly.' );
}

// Get theme mods
$shapely_transparent_header         = get_theme_mod( 'shapely_transparent_header', 0 );
$shapely_transparent_header_opacity = get_theme_mod( 'shapely_sticky_header_transparency', 100 );

// Set header style.
$shapely_nav_style = '';
if ( 1 == $shapely_transparent_header && $shapely_transparent_header_opacity ) {
	/*
	 * The customizer stores 0-100; rgba() needs a 0-1 alpha. The old string
	 * concatenation ("0." . $value) only worked because the slider emits
	 * two-digit values, and produced an invalid "rgba(255,255,255,100)" at the
	 * top of the range that browsers merely happened to clamp.
	 */
	$shapely_alpha     = max( 0, min( 100, (int) $shapely_transparent_header_opacity ) ) / 100;
	$shapely_nav_style = sprintf(
		'background: rgba(255, 255, 255, %s);',
		rtrim( rtrim( number_format( $shapely_alpha, 2, '.', '' ), '0' ), '.' )
	);
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php if ( 'open' === get_option( 'default_ping_status' ) ) : ?>
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'shapely' ); ?></a>

	<header id="masthead" class="site-header<?php echo esc_attr( get_theme_mod( 'mobile_menu_on_desktop', false ) ? ' mobile-menu' : '' ); ?>" role="banner">
		<div class="nav-container">
			<nav <?php echo $shapely_nav_style ? 'style="' . esc_attr( $shapely_nav_style ) . '"' : ''; ?> id="site-navigation" class="main-navigation" role="navigation">
				<div class="container nav-bar">
					<div class="flex-row">
						<div class="module left site-title-container">
							<?php shapely_get_header_logo(); ?>
						</div>
						<button class="module widget-handle mobile-toggle right visible-sm visible-xs"
							type="button"
							aria-expanded="false"
							aria-controls="menu"
							aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'shapely' ); ?>">
							<i class="fa fa-bars" aria-hidden="true"></i>
						</button>
						<div class="module-group right">
							<div class="module left">
								<?php shapely_header_menu(); ?>
							</div>
							<!--end of menu module-->
							<div class="module widget-handle search-widget-handle hidden-xs hidden-sm">
								<button class="search">
									<i class="fa fa-search"></i>
									<span class="title"><?php esc_html_e( 'Site Search', 'shapely' ); ?></span>
								</button>
								<div class="function">
									<?php
									get_search_form();
									?>
								</div>
							</div>
						</div>
						<!--end of module group-->
					</div>
				</div>
			</nav><!-- #site-navigation -->
		</div>
	</header><!-- #masthead -->
	<div id="content" class="main-container">
		<?php if ( ! is_page_template( 'page-templates/template-home.php' ) && ! is_404() && ! is_page_template( 'page-templates/template-widget.php' ) ) : ?>
			<div class="header-callout">
				<?php shapely_top_callout(); ?>
			</div>
		<?php endif; ?>

		<section class="content-area <?php echo ( get_theme_mod( 'top_callout', true ) ) ? '' : ' pt0 '; ?>">
			<div id="main" class="<?php echo ( ! is_page_template( 'page-templates/template-home.php' ) && ! is_page_template( 'page-templates/template-widget.php' ) ) ? 'container' : ''; ?>" role="main">
