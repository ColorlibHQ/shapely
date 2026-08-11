<?php
/**
 * Block editor styles and patterns.
 *
 * Shapely is a classic theme, but posts and pages are still written in the
 * block editor, so the blocks people actually use should be able to take the
 * theme's own appearance rather than core's defaults.
 *
 * Everything here mirrors values that already exist in style.css (the .btn
 * rules around line 1392 and the brand colour #745cf9) instead of introducing
 * a second, competing palette.
 *
 * @package Shapely
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'shapely_register_block_styles' ) ) :
	/**
	 * Register block styles that reuse the theme's button and section looks.
	 */
	function shapely_register_block_styles() {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}

		/*
		 * The CSS is passed as inline_style so each style is self-contained and
		 * is only printed when the block is actually on the page, rather than
		 * growing style.css for markup most sites never use.
		 */
		register_block_style(
			'core/button',
			array(
				'name'         => 'shapely-filled',
				'label'        => esc_html__( 'Shapely Filled', 'shapely' ),
				'inline_style' => '
					.wp-block-button.is-style-shapely-filled .wp-block-button__link {
						background: #745cf9;
						border: 2px solid #745cf9;
						border-radius: 0;
						color: #fff;
						font-size: 12px;
						font-weight: 600;
						letter-spacing: 1px;
						padding: 12px 26px;
						text-transform: uppercase;
					}
					.wp-block-button.is-style-shapely-filled .wp-block-button__link:hover,
					.wp-block-button.is-style-shapely-filled .wp-block-button__link:focus {
						background: #5d47d7;
						border-color: #5d47d7;
						color: #fff;
					}',
			)
		);

		register_block_style(
			'core/button',
			array(
				'name'         => 'shapely-outline',
				'label'        => esc_html__( 'Shapely Outline', 'shapely' ),
				'inline_style' => '
					.wp-block-button.is-style-shapely-outline .wp-block-button__link {
						background: transparent;
						border: 2px solid #745cf9;
						border-radius: 0;
						color: #745cf9;
						font-size: 12px;
						font-weight: 600;
						letter-spacing: 1px;
						padding: 12px 26px;
						text-transform: uppercase;
					}
					.wp-block-button.is-style-shapely-outline .wp-block-button__link:hover,
					.wp-block-button.is-style-shapely-outline .wp-block-button__link:focus {
						background: #745cf9;
						color: #fff;
					}',
			)
		);

		register_block_style(
			'core/separator',
			array(
				'name'         => 'shapely-short',
				'label'        => esc_html__( 'Shapely Short Rule', 'shapely' ),
				'inline_style' => '
					.wp-block-separator.is-style-shapely-short {
						background: #745cf9;
						border: 0;
						height: 3px;
						margin: 32px auto;
						max-width: 60px;
						opacity: 1;
					}',
			)
		);
	}
endif;
add_action( 'init', 'shapely_register_block_styles' );

if ( ! function_exists( 'shapely_register_block_patterns' ) ) :
	/**
	 * Register a small set of patterns built from core blocks.
	 */
	function shapely_register_block_patterns() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		if ( function_exists( 'register_block_pattern_category' ) ) {
			register_block_pattern_category(
				'shapely',
				array( 'label' => esc_html__( 'Shapely', 'shapely' ) )
			);
		}

		register_block_pattern(
			'shapely/call-to-action',
			array(
				'title'      => esc_html__( 'Centred call to action', 'shapely' ),
				'categories' => array( 'shapely', 'call-to-action' ),
				'content'    => '
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"64px","bottom":"64px"}}}} -->
<div class="wp-block-group alignwide" style="padding-top:64px;padding-bottom:64px">
<!-- wp:heading {"textAlign":"center","level":2} -->
<h2 class="wp-block-heading has-text-align-center">' . esc_html__( 'Ready to get started?', 'shapely' ) . '</h2>
<!-- /wp:heading -->
<!-- wp:separator {"className":"is-style-shapely-short"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-shapely-short"/>
<!-- /wp:separator -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">' . esc_html__( 'Say a little about what you offer and why someone should take the next step.', 'shapely' ) . '</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-shapely-filled"} -->
<div class="wp-block-button is-style-shapely-filled"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Get in touch', 'shapely' ) . '</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->',
			)
		);

		register_block_pattern(
			'shapely/two-column-feature',
			array(
				'title'      => esc_html__( 'Two column feature', 'shapely' ),
				'categories' => array( 'shapely', 'columns' ),
				'content'    => '
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'What we do', 'shapely' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'Describe the service in a sentence or two. Keep it short enough to read at a glance.', 'shapely' ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">' . esc_html__( 'How we do it', 'shapely' ) . '</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>' . esc_html__( 'A second short paragraph, so the two columns balance rather than one running long.', 'shapely' ) . '</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
			)
		);
	}
endif;
add_action( 'init', 'shapely_register_block_patterns' );
