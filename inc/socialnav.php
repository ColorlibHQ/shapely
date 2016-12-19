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
					'link_before'     => '<i class="social_icon fa"><span>',
					'link_after'      => '</span></i>'
				)
			);
		}
	}
endif;