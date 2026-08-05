<?php

/**
 * Social Navigation Menu
 *
 * @package Shapely
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'shapely_social_icons' ) ) :
	/**
	 * Display social links in footer and widgets
	 *
	 * The per-item icon markup is attached in shapely_social_menu_item_args(),
	 * which runs once per menu item and can therefore pick the right Font Awesome
	 * family for each network.
	 *
	 * @package shapely
	 */
	function shapely_social_icons() {
		if ( ! has_nav_menu( 'social-menu' ) ) {
			return;
		}

		wp_nav_menu(
			array(
				'theme_location'  => 'social-menu',
				'container'       => 'nav',
				'container_id'    => 'social',
				'container_class' => 'social-icons',
				'menu_id'         => 'menu-social-items',
				'menu_class'      => 'list-inline social-list',
				'depth'           => 1,
				'fallback_cb'     => '',
			)
		);
	}
endif;

if ( ! function_exists( 'shapely_get_social_networks' ) ) :
	/**
	 * Map of host fragment => array( icon slug, Font Awesome family ).
	 *
	 * Longer/more specific hosts must come first so that, for example, a
	 * "mastodon.social" URL is not swallowed by a broader rule.
	 *
	 * @return array
	 */
	function shapely_get_social_networks() {
		$networks = array(
			'x.com'           => array( 'x-twitter', 'brands' ),
			'twitter.com'     => array( 'x-twitter', 'brands' ),
			'facebook.com'    => array( 'facebook-f', 'brands' ),
			'github.com'      => array( 'github', 'brands' ),
			'pinterest.com'   => array( 'pinterest-p', 'brands' ),
			'linkedin.com'    => array( 'linkedin-in', 'brands' ),
			'youtube.com'     => array( 'youtube', 'brands' ),
			'youtu.be'        => array( 'youtube', 'brands' ),
			'instagram.com'   => array( 'instagram', 'brands' ),
			'flickr.com'      => array( 'flickr', 'brands' ),
			'tumblr.com'      => array( 'tumblr', 'brands' ),
			'dribbble.com'    => array( 'dribbble', 'brands' ),
			'vimeo.com'       => array( 'vimeo-v', 'brands' ),
			'spotify.com'     => array( 'spotify', 'brands' ),
			'soundcloud.com'  => array( 'soundcloud', 'brands' ),
			'tiktok.com'      => array( 'tiktok', 'brands' ),
			'threads.net'     => array( 'threads', 'brands' ),
			'threads.com'     => array( 'threads', 'brands' ),
			'discord.com'     => array( 'discord', 'brands' ),
			'discord.gg'      => array( 'discord', 'brands' ),
			'twitch.tv'       => array( 'twitch', 'brands' ),
			'mastodon.social' => array( 'mastodon', 'brands' ),
			'mastodon.'       => array( 'mastodon', 'brands' ),
			'medium.com'      => array( 'medium', 'brands' ),
			'slack.com'       => array( 'slack', 'brands' ),
			'telegram.org'    => array( 'telegram', 'brands' ),
			't.me'            => array( 'telegram', 'brands' ),
			'whatsapp.com'    => array( 'whatsapp', 'brands' ),
			'wa.me'           => array( 'whatsapp', 'brands' ),
		);
		// Note: fa-bluesky ships from Font Awesome 6.6; the bundled build is 6.4.2,
		// so Bluesky URLs intentionally fall through to the generic link glyph.

		/**
		 * Filter the recognised social networks.
		 *
		 * @param array $networks Host fragment => array( icon slug, FA family ).
		 */
		return apply_filters( 'shapely_social_networks', $networks );
	}
endif;

if ( ! function_exists( 'shapely_get_social_icon' ) ) :
	/**
	 * Resolve a menu item URL to its icon slug and Font Awesome family.
	 *
	 * @param string $url Menu item URL.
	 *
	 * @return array{slug:string,family:string}
	 */
	function shapely_get_social_icon( $url ) {
		$url = (string) $url;

		// mailto:/tel: links have no host, so test the raw URL first.
		if ( 0 === stripos( $url, 'mailto:' ) ) {
			return array(
				'slug'   => 'envelope',
				'family' => 'solid',
			);
		}

		// Feeds are identified by path, not host. RSS is a solid glyph, not a brand:
		// rendering it with the brands font produced an empty box.
		if ( false !== stripos( $url, '/feed' ) ) {
			return array(
				'slug'   => 'rss',
				'family' => 'solid',
			);
		}

		$host = (string) wp_parse_url( $url, PHP_URL_HOST );
		$host = strtolower( preg_replace( '/^www\./', '', $host ) );

		if ( '' !== $host ) {
			foreach ( shapely_get_social_networks() as $fragment => $icon ) {
				if ( false !== strpos( $host, $fragment ) ) {
					return array(
						'slug'   => $icon[0],
						'family' => $icon[1],
					);
				}
			}
		}

		// Unknown network: a generic link glyph beats a blank square.
		return array(
			'slug'   => 'link',
			'family' => 'solid',
		);
	}
endif;

if ( ! function_exists( 'shapely_social_menu_item_args' ) ) :
	/**
	 * Wrap each social menu item's label in its icon.
	 *
	 * Uses nav_menu_item_args (per item) rather than the menu-wide link_before /
	 * link_after so each network gets the correct Font Awesome family. The label
	 * is kept inside .screen-reader-text so the link has an accessible name —
	 * previously that span was rendered empty, leaving the links unnamed.
	 *
	 * @param stdClass $args  wp_nav_menu arguments for this item.
	 * @param WP_Post  $item  Menu item.
	 * @param int      $depth Depth of the item.
	 *
	 * @return stdClass
	 */
	function shapely_social_menu_item_args( $args, $item, $depth ) {
		if ( ! is_object( $args ) || empty( $args->theme_location ) || 'social-menu' !== $args->theme_location ) {
			return $args;
		}

		$icon = shapely_get_social_icon( isset( $item->url ) ? $item->url : '' );

		$args->link_before = sprintf(
			'<i class="fa-%1$s fa-%2$s" aria-hidden="true"></i><span class="screen-reader-text">',
			esc_attr( $icon['family'] ),
			esc_attr( $icon['slug'] )
		);
		$args->link_after  = '</span>';

		return $args;
	}
endif;
add_filter( 'nav_menu_item_args', 'shapely_social_menu_item_args', 10, 3 );

if ( ! function_exists( 'shapely_social_menu_filter' ) ) :
	/**
	 * Add a network slug class to each social menu item for CSS targeting.
	 *
	 * @param string[] $classes Menu item classes.
	 * @param WP_Post  $item    Menu item.
	 * @param stdClass $args    wp_nav_menu arguments.
	 *
	 * @return string[]
	 */
	function shapely_social_menu_filter( $classes, $item, $args ) {
		if ( ! is_object( $args ) || empty( $args->theme_location ) || 'social-menu' !== $args->theme_location ) {
			return $classes;
		}

		$icon      = shapely_get_social_icon( isset( $item->url ) ? $item->url : '' );
		$classes[] = 'social-' . $icon['slug'];

		return $classes;
	}
endif;
add_filter( 'nav_menu_css_class', 'shapely_social_menu_filter', 10, 3 );
