<?php

/**
 * Social Navigation Menu
 */

if ( ! function_exists( 'shapely_social_icons' ) ) :
	/**
	 * Display social links in footer and widgets
	 *
	 * @package shapely
	 */
	function shapely_social_icons() {
		if ( has_nav_menu( 'social-menu' ) ) {
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
					'link_before'     => '<i class="fa-brands fa-',
					'link_after'      => '"><span class="screen-reader-text"></span></i>',
				)
			);
		}
	}
endif;

/**
 * Filter the social menu items to add proper Font Awesome 6 classes
 */
if ( ! function_exists( 'shapely_social_menu_filter' ) ) :
	function shapely_social_menu_filter( $classes, $item, $args ) {
		if ( 'social-menu' === $args->theme_location ) {
			// Get the URL to determine which icon to use
			$url = $item->url;
			
			// Extract domain from URL
			$domain = '';
			if (preg_match('/\/\/([^\/]+)\//', $url, $matches)) {
				$domain = $matches[1];
			}
			
			// Determine which fontawesome brand icon to use and ensure lowercase titles
			if (strpos($domain, 'twitter.com') !== false || strpos($domain, 'x.com') !== false) {
				$classes[] = 'twitter';
				// Set a title attribute for use with Font Awesome (lowercase)
				$item->title = 'twitter';
			} elseif (strpos($domain, 'facebook.com') !== false) {
				$classes[] = 'facebook';
				$item->title = 'facebook';
			} elseif (strpos($domain, 'github.com') !== false) {
				$classes[] = 'github';
				$item->title = 'github';
			} elseif (strpos($url, '/feed') !== false) {
				$classes[] = 'rss';
				$item->title = 'rss';
			} elseif (strpos($domain, 'pinterest.com') !== false) {
				$classes[] = 'pinterest';
				$item->title = 'pinterest';
			} elseif (strpos($domain, 'linkedin.com') !== false) {
				$classes[] = 'linkedin';
				$item->title = 'linkedin';
			} elseif (strpos($domain, 'youtube.com') !== false) {
				$classes[] = 'youtube';
				$item->title = 'youtube';
			} elseif (strpos($domain, 'instagram.com') !== false) {
				$classes[] = 'instagram';
				$item->title = 'instagram';
			} elseif (strpos($domain, 'flickr.com') !== false) {
				$classes[] = 'flickr';
				$item->title = 'flickr';
			} elseif (strpos($domain, 'tumblr.com') !== false) {
				$classes[] = 'tumblr';
				$item->title = 'tumblr';
			} elseif (strpos($domain, 'dribbble.com') !== false) {
				$classes[] = 'dribbble';
				$item->title = 'dribbble';
			} elseif (strpos($domain, 'vimeo.com') !== false) {
				$classes[] = 'vimeo';
				$item->title = 'vimeo';
			} elseif (strpos($domain, 'spotify.com') !== false) {
				$classes[] = 'spotify';
				$item->title = 'spotify';
			} elseif (strpos($domain, 'soundcloud.com') !== false) {
				$classes[] = 'soundcloud';
				$item->title = 'soundcloud';
			} elseif (strpos($domain, 'tiktok.com') !== false) {
				$classes[] = 'tiktok';
				$item->title = 'tiktok';
			} elseif (strpos($domain, 'threads.net') !== false) {
				$classes[] = 'threads';
				$item->title = 'threads';
			} elseif (strpos($domain, 'discord.com') !== false || strpos($domain, 'discord.gg') !== false) {
				$classes[] = 'discord';
				$item->title = 'discord';
			} elseif (strpos($domain, 'twitch.tv') !== false) {
				$classes[] = 'twitch';
				$item->title = 'twitch';
			} elseif (strpos($domain, 'mastodon.social') !== false || strpos($domain, 'mastodon.') !== false) {
				$classes[] = 'mastodon';
				$item->title = 'mastodon';
			} elseif (strpos($domain, 'medium.com') !== false) {
				$classes[] = 'medium';
				$item->title = 'medium';
			} elseif (strpos($domain, 'slack.com') !== false) {
				$classes[] = 'slack';
				$item->title = 'slack';
			} elseif (strpos($domain, 'telegram.org') !== false || strpos($domain, 't.me') !== false) {
				$classes[] = 'telegram';
				$item->title = 'telegram';
			} elseif (strpos($domain, 'whatsapp.com') !== false) {
				$classes[] = 'whatsapp';
				$item->title = 'whatsapp';
			}
		}
		return $classes;
	}
endif;
add_filter( 'nav_menu_css_class', 'shapely_social_menu_filter', 10, 3 );
