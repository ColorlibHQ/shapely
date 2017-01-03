<?php
/**
 * Shapely Theme Customizer.
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function shapely_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	$wp_customize->get_setting( 'custom_logo' )->transport      = 'refresh';

	// Abort if selective refresh is not available.
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector'        => '.site-title',
		'render_callback' => function () {
			bloginfo( 'name' );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'footer_callout_text', array(
		'selector'        => '.footer-callout',
		'render_callback' => function () {
			shapely_footer_callout();
		}
	) );
	$wp_customize->selective_refresh->add_partial( 'footer_callout_btntext', array(
		'selector'        => '.footer-callout',
		'render_callback' => function () {
			shapely_footer_callout();
		}
	) );
	$wp_customize->selective_refresh->add_partial( 'footer_callout_link', array(
		'selector'        => '.footer-callout',
		'render_callback' => function () {
			shapely_footer_callout();
		}
	) );
	$wp_customize->selective_refresh->add_partial( 'blog_name', array(
		'selector'        => '.header-callout',
		'render_callback' => function () {
			shapely_top_callout();
		}
	) );
	$wp_customize->selective_refresh->add_partial( 'header_textcolor', array(
		'selector'        => '.header-callout',
		'render_callback' => function () {
			shapely_top_callout();
		}
	) );

}

add_action( 'customize_register', 'shapely_customize_register' );

/**
 * Options for WordPress Theme Customizer.
 */
function shapely_customizer( $wp_customize ) {
	/* Main option Settings Panel */
	$wp_customize->add_panel( 'shapely_main_options', array(
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => __( 'Shapely Options', 'shapely' ),
		'description'    => __( 'Panel to update shapely theme options', 'shapely' ), // Include html tags such as <p>.
		'priority'       => 10, // Mixed with top-level-section hierarchy.
	) );

	// add "Sidebar" section
	$wp_customize->add_section( 'shapely_layout_section', array(
		'title'       => __( 'Layout options', 'shapely' ),
		'description' => '',
		'priority'    => 31,
		'panel'       => 'shapely_main_options',
	) );

	$wp_customize->add_section( 'shapely_blog_section', array(
		'title'    => esc_html__( 'Blog Settings', 'shapely' ),
		'panel'    => 'shapely_main_options',
		'priority' => 33,
	) );

	$wp_customize->add_setting( 'link_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
		'label'       => __( 'Link Color', 'shapely' ),
		'description' => __( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'shapely_layout_section',
	) ) );
	$wp_customize->add_setting( 'link_hover_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_hover_color', array(
		'label'       => __( 'Link Hover Color', 'shapely' ),
		'description' => __( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'shapely_layout_section',
	) ) );
	$wp_customize->add_setting( 'button_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_color', array(
		'label'       => __( 'Button Color', 'shapely' ),
		'description' => __( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'shapely_layout_section',
	) ) );
	$wp_customize->add_setting( 'button_hover_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_hover_color', array(
		'label'       => __( 'Button Hover Color', 'shapely' ),
		'description' => __( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'shapely_layout_section',
	) ) );

	$wp_customize->add_setting( 'social_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'social_color', array(
		'label'       => __( 'Social Icon Color', 'shapely' ),
		'description' => __( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'shapely_layout_section',
	) ) );

	// add "Sidebar" section
	$wp_customize->add_section( 'shapely_main_section', array(
		'title'    => __( 'Main options', 'shapely' ),
		'priority' => 11,
		'panel'    => 'shapely_main_options',
	) );

	$wp_customize->add_setting( 'top_callout', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );

	if ( class_exists( 'Epsilon_Control_Toggle' ) ) {
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'top_callout',
			                            array(
				                            'type'     => 'mte-toggle',
				                            'label'    => esc_html__( 'Show title in top call out box', 'shapely' ),
				                            'section'  => 'shapely_blog_section',
				                            'priority' => 20
			                            )
		                            )
		);
	} else {
		$wp_customize->add_control( 'top_callout', array(
			'label'    => esc_html__( 'check to show title in top call out box', 'shapely' ),
			'section'  => 'shapely_blog_section',
			'priority' => 20,
			'type'     => 'checkbox',
		) );
	}

	$wp_customize->add_setting( 'hide_post_title', array(
		'default'           => 0,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );

	if ( class_exists( 'Epsilon_Control_Toggle' ) ) {
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'hide_post_title',
			                            array(
				                            'type'    => 'mte-toggle',
				                            'label'   => esc_html__( 'Title in blog post', 'shapely' ),
				                            'section' => 'wpseo_breadcrumbs_customizer_section',
			                            )
		                            )
		);
	} else {
		$wp_customize->add_control( 'hide_post_title', array(
			'label'   => esc_html__( 'Title in blog post', 'shapely' ),
			'section' => 'wpseo_breadcrumbs_customizer_section',
			'type'    => 'checkbox',
		) );
	}

	$wp_customize->add_setting( 'blog_name', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_stripslashes',
		'transport'         => 'postMessage'
	) );
	$wp_customize->add_control( 'blog_name', array(
		'label'       => __( 'Blog Name in top callout', 'shapely' ),
		'description' => __( 'Heading for the Blog page', 'shapely' ),
		'section'     => 'shapely_blog_section',
	) );

	if ( post_type_exists( 'jetpack-portfolio' ) ) {
		$wp_customize->add_setting( 'portfolio_name', array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_stripslashes',
		) );
		$wp_customize->add_control( 'portfolio_name', array(
			'label'   => __( 'Portfolio Archive Title', 'shapely' ),
			'section' => 'shapely_main_section',
		) );

		$wp_customize->add_setting( 'portfolio_description', array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_stripslashes',
		) );
		$wp_customize->add_control( 'portfolio_description', array(
			'type'    => 'textarea',
			'label'   => __( 'Portfolio Archive Description', 'shapely' ),
			'section' => 'shapely_main_section',
		) );
	}

	$wp_customize->add_setting( 'footer_callout_text', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_stripslashes',
		'transport'         => 'postMessage'
	) );
	$wp_customize->add_control( 'footer_callout_text', array(
		'label'       => __( 'Text for footer callout', 'shapely' ),
		'description' => __( 'Footer Callout', 'shapely' ),
		'section'     => 'shapely_main_section',
	) );

	$wp_customize->add_setting( 'footer_callout_btntext', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_stripslashes',
		'transport'         => 'postMessage'
	) );
	$wp_customize->add_control( 'footer_callout_btntext', array(
		'label'   => __( 'Text for footer callout button', 'shapely' ),
		'section' => 'shapely_main_section',
	) );
	$wp_customize->add_setting( 'footer_callout_link', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'postMessage'
	) );
	$wp_customize->add_control( 'footer_callout_link', array(
		'label'       => __( 'CFA button link', 'shapely' ),
		'section'     => 'shapely_main_section',
		'description' => __( 'Enter the link for Call For Action button in footer', 'shapely' ),
		'type'        => 'text',
	) );

	// add "Footer" section
	$wp_customize->add_section( 'shapely_footer_section', array(
		'title'    => esc_html__( 'Footer', 'shapely' ),
		'priority' => 90,
	) );

	$wp_customize->add_setting( 'shapely_footer_copyright', array(
		'default'           => '',
		'transport'         => 'refresh',
		'sanitize_callback' => 'wp_kses_stripslashes',
	) );

	$wp_customize->add_control( 'shapely_footer_copyright', array(
		'type'    => 'textarea',
		'label'   => __( 'Copyright Text', 'shapely' ),
		'section' => 'shapely_footer_section',
	) );

	if ( class_exists( 'Epsilon_Control_Color_Scheme' ) ) {
		$wp_customize->add_setting( 'shapely_color_scheme',
		                            array(
			                            'sanitize_callback' => 'sanitize_text_field',
			                            'default'           => 'default',
			                            'transport'         => 'postMessage',
		                            ) );

		$wp_customize->add_control( new Epsilon_Control_Color_Scheme(
			                            $wp_customize,
			                            'shapely_color_scheme',
			                            array(
				                            'label'       => esc_html__( 'Color scheme', 'shapely' ),
				                            'description' => esc_html__( 'Select a color scheme', 'shapely' ),
				                            'choices'     => array(
					                            array(
						                            'id'     => 'purple',
						                            'name'   => 'Default',
						                            'colors' => array(
							                            'accent'               => '#745cf9',
							                            'text'                 => '#666666',
							                            'content-widget-title' => '#0e1015',
							                            'footer-bg'            => '#0e1015',
							                            'footer-widget-title'  => '#ffffff',
							                            'footer-links'         => '#ffffff'
						                            ),
					                            ),
					                            array(
						                            'id'     => 'yellow',
						                            'name'   => 'Yellow',
						                            'colors' => array(
							                            'accent'               => '#FFC107',
							                            'text'                 => '#666666',
							                            'content-widget-title' => '#0e1015',
							                            'footer-bg'            => '#0e1015',
							                            'footer-widget-title'  => '#ffffff',
							                            'footer-links'         => '#ffffff'
						                            ),
					                            ),
					                            array(
						                            'id'     => 'green',
						                            'name'   => 'Green',
						                            'colors' => array(
							                            'accent'               => '#2ecc71',
							                            'text'                 => '#666666',
							                            'content-widget-title' => '#0e1015',
							                            'footer-bg'            => '#0e1015',
							                            'footer-widget-title'  => '#ffffff',
							                            'footer-links'         => '#ffffff'
						                            ),
					                            ),
				                            ),
				                            'priority'    => 0,
				                            'default'     => 'purple',
				                            'section'     => 'colors',
			                            )
		                            )
		);
	}

	$wp_customize->add_setting( 'first_letter_caps', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );
	$wp_customize->add_setting( 'tags_post_meta', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );
	$wp_customize->add_setting( 'related_posts_area', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );
	$wp_customize->add_setting( 'post_author_area', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );
	$wp_customize->add_setting( 'post_author_left_side', array(
		'default'           => 0,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );


	if ( class_exists( 'Epsilon_Control_Toggle' ) ) {
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'first_letter_caps',
			                            array(
				                            'type'    => 'mte-toggle',
				                            'label'   => esc_html__( 'First Letter Caps', 'shapely' ),
				                            'section' => 'shapely_blog_section',
			                            )
		                            ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'tags_post_meta',
			                            array(
				                            'type'    => 'mte-toggle',
				                            'label'   => esc_html__( 'Tags Post Meta', 'shapely' ),
				                            'section' => 'shapely_blog_section',
			                            )
		                            ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'related_posts_area',
			                            array(
				                            'type'    => 'mte-toggle',
				                            'label'   => esc_html__( 'Related Posts Area', 'shapely' ),
				                            'section' => 'shapely_blog_section',
			                            )
		                            ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'post_author_area',
			                            array(
				                            'type'    => 'mte-toggle',
				                            'label'   => esc_html__( 'Post Author Area', 'shapely' ),
				                            'section' => 'shapely_blog_section',
			                            )
		                            ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle(
			                            $wp_customize,
			                            'post_author_left_side',
			                            array(
				                            'type'    => 'mte-toggle',
				                            'label'   => esc_html__( 'Post Author Left Side', 'shapely' ),
				                            'section' => 'shapely_blog_section',
			                            )
		                            ) );
	} else {
		$wp_customize->add_control( 'first_letter_caps', array(
			'label'   => esc_html__( 'First Letter Caps', 'shapely' ),
			'section' => 'shapely_blog_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'tags_post_meta', array(
			'label'   => esc_html__( 'Tags Post Meta', 'shapely' ),
			'section' => 'shapely_blog_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'related_posts_area', array(
			'label'   => esc_html__( 'Related Posts Area', 'shapely' ),
			'section' => 'shapely_blog_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'post_author_area', array(
			'label'   => esc_html__( 'Post Author Area', 'shapely' ),
			'section' => 'shapely_blog_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'post_author_left_side', array(
			'label'   => esc_html__( 'Post Author Left Side', 'shapely' ),
			'section' => 'shapely_blog_section',
			'type'    => 'checkbox',
		) );
	}

	$wp_customize->add_setting( 'blog_layout_view', array(
		'default'           => 'grid',
		'sanitize_callback' => 'wp_kses_stripslashes',
	) );

	$wp_customize->add_control( 'blog_layout_view', array(
		'label'   => esc_html__( 'Blog Layout', 'shapely' ),
		'section' => 'shapely_blog_section',
		'type'    => 'select',
		'choices' => array(
			'grid'             => esc_html__( 'Grid only', 'shapely' ),
			'large_image_grid' => esc_html__( 'Large Image and Grid', 'shapely' ),
			'large_image'      => esc_html__( 'Large Images', 'shapely' )
		)
	) );

	$wp_customize->add_setting( 'blog_layout_template', array(
		'default'           => 'sidebar-right',
		'sanitize_callback' => 'wp_kses_stripslashes',
	) );

	$wp_customize->add_control( 'blog_layout_template', array(
		'label'   => esc_html__( 'Blog Template', 'shapely' ),
		'section' => 'shapely_blog_section',
		'type'    => 'select',
		'choices' => array(
			'full-width'    => esc_html__( 'Full Width', 'shapely' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
			'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
			'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' )
		)
	) );
}

add_action( 'customize_register', 'shapely_customizer' );

/**
 * Sanitize checkbox for WordPress customizer.
 */
function shapely_sanitize_checkbox( $input ) {
	if ( $input == 1 ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Adds sanitization callback function: Sidebar Layout.
 */
function shapely_sanitize_layout( $input ) {
	$shapely_site_layout = array(
		'pull-right' => esc_html__( 'Left Sidebar', 'shapely' ),
		'side-right' => esc_html__( 'Right Sidebar', 'shapely' ),
		'no-sidebar' => esc_html__( 'No Sidebar', 'shapely' ),
		'full-width' => esc_html__( 'Full Width', 'shapely' )
	);

	if ( array_key_exists( $input, $shapely_site_layout ) ) {
		return $input;
	} else {
		return '';
	}
}

/**
 * Add CSS for custom controls.
 */
function shapely_customizer_custom_control_css() {
	?>
	<style>
		#customize-control-shapely-main_body_typography-size select, #customize-control-shapely-main_body_typography-face select, #customize-control-shapely-main_body_typography-style select {
			width: 60%;
		}
	</style><?php

}

add_action( 'customize_controls_print_styles', 'shapely_customizer_custom_control_css' );

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}
