<?php

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
		},
	) );
	$wp_customize->selective_refresh->add_partial( 'footer_callout_btntext', array(
		'selector'        => '.footer-callout',
		'render_callback' => function () {
			shapely_footer_callout();
		},
	) );
	$wp_customize->selective_refresh->add_partial( 'footer_callout_link', array(
		'selector'        => '.footer-callout',
		'render_callback' => function () {
			shapely_footer_callout();
		},
	) );
	$wp_customize->selective_refresh->add_partial( 'blog_name', array(
		'selector'        => '.header-callout',
		'render_callback' => function () {
			shapely_top_callout();
		},
	) );
	$wp_customize->selective_refresh->add_partial( 'header_textcolor', array(
		'selector'        => '.header-callout',
		'render_callback' => function () {
			shapely_top_callout();
		},
	) );

}

add_action( 'customize_register', 'shapely_customize_register' );

/**
 * Options for WordPress Theme Customizer.
 */
function shapely_customizer( $wp_customize ) {

	// Recomended actions
	global $shapely_required_actions, $shapely_recommended_plugins;

	$customizer_recommended_plugins = array();
	if ( is_array( $shapely_recommended_plugins ) ) {
		foreach ( $shapely_recommended_plugins as $k => $s ) {
			if ( $s['recommended'] ) {
				$customizer_recommended_plugins[ $k ] = $s;
			}
		}
	}

	$customizer_shapely_required_actions = array();
	if ( ! empty( $shapely_required_actions ) ) {
		foreach ( $shapely_required_actions as $required_action ) {
			if ( 'shapely-req-import-content' == $required_action['id'] ) {
				$required_action['description'] = sprintf(
					esc_html__( 'In oder to import the demo content go %s', 'shapely' ),
					'<a href="' . admin_url( 'themes.php?page=shapely-welcome&tab=recommended_actions' ) . '">' . esc_html__( 'here', 'shapely' ) . '</a>'
				);
			}
			$customizer_shapely_required_actions[] = $required_action;
		}
	}

	$theme_slug = 'shapely';

	$wp_customize->add_section( new Epsilon_Section_Recommended_Actions( $wp_customize, 'epsilon_recomended_section', array(
		'title'                        => esc_html__( 'Recomended Actions', 'shapely' ),
		'social_text'                  => esc_html__( 'We are social', 'shapely' ),
		'plugin_text'                  => esc_html__( 'Recomended Plugins', 'shapely' ),
		'actions'                      => $customizer_shapely_required_actions,
		'plugins'                      => $customizer_recommended_plugins,
		'theme_specific_option'        => $theme_slug . '_show_required_actions',
		'theme_specific_plugin_option' => $theme_slug . '_show_recommended_plugins',
		'facebook'                     => 'https://www.facebook.com/colorlib',
		'twitter'                      => 'https://twitter.com/colorlib',
		'wp_review'                    => true,
		'priority'                     => 0,
	) ) );

	$wp_customize->add_section( new Epsilon_Section_Pro( $wp_customize, 'epsilon-section-pro', array(
		'title'       => esc_html__( 'Theme documentation', 'shapely' ),
		'button_text' => esc_html__( 'Learn more', 'shapely' ),
		'button_url'  => 'https://colorlib.com/wp/support/shapely/',
		'priority'    => 0,
	) ) );

	/* Main option Settings Panel */
	$wp_customize->add_panel( 'shapely_main_options', array(
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => esc_html__( 'Theme Options', 'shapely' ),
		'description'    => esc_html__( 'Panel to update shapely theme options', 'shapely' ), // Include html tags such as <p>.
		'priority'       => 10, // Mixed with top-level-section hierarchy.
	) );
	$title_tagline = $wp_customize->get_section( 'title_tagline' );
	if ( $title_tagline ) {
		$title_tagline->panel = 'shapely_main_options';
		$title_tagline->priority = 1;
	}

	// add "Sidebar" section
	$color_section = $wp_customize->get_section( 'colors' );
	if ( $color_section ) {
		$color_section->panel = 'shapely_main_options';
		$color_section->priority = 31;
	}

	$wp_customize->add_section( 'shapely_blog_section', array(
		'title'    => esc_html__( 'Blog Settings', 'shapely' ),
		'panel'    => 'shapely_main_options',
		'priority' => 33,
	) );

	$wp_customize->add_section( 'shapely_single_post_section', array(
		'title'    => esc_html__( 'Single Post Settings', 'shapely' ),
		'panel'    => 'shapely_main_options',
		'priority' => 35,
	) );
	$wp_customize->add_setting( 'link_color', array(
		'default'           => '#745cf9',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
		'label'       => esc_html__( 'Link Color', 'shapely' ),
		'description' => esc_html__( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'colors',
	) ) );
	$wp_customize->add_setting( 'link_hover_color', array(
		'default'           => '#5234f9',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_hover_color', array(
		'label'       => esc_html__( 'Link Hover Color', 'shapely' ),
		'description' => esc_html__( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'colors',
	) ) );
	$wp_customize->add_setting( 'button_color', array(
		'default'           => '#745cf9',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_color', array(
		'label'       => esc_html__( 'Button Color', 'shapely' ),
		'description' => esc_html__( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'colors',
	) ) );
	$wp_customize->add_setting( 'button_hover_color', array(
		'default'           => '#5234f9',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_hover_color', array(
		'label'       => esc_html__( 'Button Hover Color', 'shapely' ),
		'description' => esc_html__( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'colors',
	) ) );

	$wp_customize->add_setting( 'social_color', array(
		'default'           => '#fff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'social_color', array(
		'label'       => esc_html__( 'Social Icon Color', 'shapely' ),
		'description' => esc_html__( 'Default used if no color is selected', 'shapely' ),
		'section'     => 'colors',
	) ) );

	// add "Sidebar" section
	$wp_customize->add_section( 'shapely_main_section', array(
		'title'    => esc_html__( 'Main Options', 'shapely' ),
		'priority' => 11,
		'panel'    => 'shapely_main_options',
	) );

	$wp_customize->add_setting( 'top_callout', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );

	if ( class_exists( 'Epsilon_Control_Toggle' ) ) {
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'top_callout', array(
			'type'     => 'mte-toggle',
			'label'    => esc_html__( 'Show Blog Title', 'shapely' ),
			'description' => esc_html__( 'Show/hide the title from the Blog Page', 'shapely' ),
			'section'  => 'shapely_blog_section',
			'priority' => 20,
		) ) );
	} else {
		$wp_customize->add_control( 'top_callout', array(
			'label'    => esc_html__( 'Show Blog Title', 'shapely' ),
			'description' => esc_html__( 'Show/hide the title from the Blog Page', 'shapely' ),
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
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'hide_post_title', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Title in Blog Post', 'shapely' ),
			'section' => 'wpseo_breadcrumbs_customizer_section',
		) ) );
	} else {
		$wp_customize->add_control( 'hide_post_title', array(
			'label'   => esc_html__( 'Title in Blog Post', 'shapely' ),
			'section' => 'wpseo_breadcrumbs_customizer_section',
			'type'    => 'checkbox',
		) );
	}

	$wp_customize->add_setting( 'blog_name', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_stripslashes',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'blog_name', array(
		'label'       => esc_html__( 'Blog Name in Top Callout', 'shapely' ),
		'description' => esc_html__( 'Heading for the Blog page', 'shapely' ),
		'section'     => 'shapely_blog_section',
	) );

	if ( post_type_exists( 'jetpack-portfolio' ) ) {
		$wp_customize->add_setting( 'portfolio_name', array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_stripslashes',
		) );
		$wp_customize->add_control( 'portfolio_name', array(
			'label'   => esc_html__( 'Portfolio Archive Title', 'shapely' ),
			'description' => esc_html__( 'Add a title on the Portfolio Archive Page.', 'shapely' ),
			'section' => 'shapely_main_section',
		) );

		$wp_customize->add_setting( 'portfolio_description', array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_stripslashes',
		) );
		$wp_customize->add_control( 'portfolio_description', array(
			'type'    => 'textarea',
			'label'   => esc_html__( 'Portfolio Archive Description', 'shapely' ),
			'description' => esc_html__( 'Add a description on the Portfolio Archive Page.', 'shapely' ),
			'section' => 'shapely_main_section',
		) );
	}

	$wp_customize->add_setting( 'footer_callout_text', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_stripslashes',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'footer_callout_text', array(
		'label'       => esc_html__( 'Text for Footer Callout', 'shapely' ),
		'description' => esc_html__( 'The title of the call to action section from footer', 'shapely' ),
		'section'     => 'shapely_main_section',
	) );

	$wp_customize->add_setting( 'footer_callout_btntext', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_stripslashes',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'footer_callout_btntext', array(
		'label'   => esc_html__( 'Text for Footer Callout Button', 'shapely' ),
		'description' => esc_html__( 'The label of the call to action section\'s button from the footer', 'shapely' ),
		'section' => 'shapely_main_section',
	) );
	$wp_customize->add_setting( 'footer_callout_link', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'footer_callout_link', array(
		'label'       => esc_html__( 'CFA Button Link', 'shapely' ),
		'section'     => 'shapely_main_section',
		'description' => esc_html__( 'The URL of the call to action section\'s button from footer', 'shapely' ),
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
		'label'   => esc_html__( 'Copyright Text', 'shapely' ),
		'section' => 'shapely_footer_section',
	) );

	if ( class_exists( 'Epsilon_Control_Color_Scheme' ) ) {
		$wp_customize->add_setting( 'shapely_color_scheme', array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'default',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( new Epsilon_Control_Color_Scheme( $wp_customize, 'shapely_color_scheme', array(
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
						'footer-links'         => '#ffffff',
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
						'footer-links'         => '#ffffff',
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
						'footer-links'         => '#ffffff',
					),
				),
			),
			'priority'    => 0,
			'default'     => 'purple',
			'section'     => 'colors',
		) ) );
	} // End if().

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
	$wp_customize->add_setting( 'post_author_email', array(
		'default'           => 0,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );

	// Single Post Settings
	if ( class_exists( 'Epsilon_Control_Toggle' ) ) {
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'first_letter_caps', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'First Letter Caps', 'shapely' ),
			'description' => esc_html__( 'This will transform your first letter from a post into uppercase', 'shapely' ),
			'section' => 'shapely_single_post_section',
		) ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'tags_post_meta', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Tags Post Meta', 'shapely' ),
			'description' => esc_html__( 'This will show/hide tags from the end of post', 'shapely' ),
			'section' => 'shapely_single_post_section',
		) ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'related_posts_area', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Related Posts Area', 'shapely' ),
			'description' => esc_html__( 'This will enable/disable the related posts', 'shapely' ),
			'section' => 'shapely_single_post_section',
		) ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'post_author_area', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Post Author Area', 'shapely' ),
			'description' => esc_html__( 'This will show/hide the author box', 'shapely' ),
			'section' => 'shapely_single_post_section',
		) ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'post_author_left_side', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Post Author Left Side', 'shapely' ),
			'description' => esc_html__( 'This will move the author box from the bottom of the post on top on the left side', 'shapely' ),
			'section' => 'shapely_single_post_section',
		) ) );
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'post_author_email', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Show Author Email', 'shapely' ),
			'description' => esc_html__( 'This will show/hide the author\'s email from the author box', 'shapely' ),
			'section' => 'shapely_single_post_section',
		) ) );
	} else {
		$wp_customize->add_control( 'first_letter_caps', array(
			'label'   => esc_html__( 'First Letter Caps', 'shapely' ),
			'description' => esc_html__( 'This will transform your first letter from a post into uppercase', 'shapely' ),
			'section' => 'shapely_single_post_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'tags_post_meta', array(
			'label'   => esc_html__( 'Tags Post Meta', 'shapely' ),
			'description' => esc_html__( 'This will show/hide tags from the end of post', 'shapely' ),
			'section' => 'shapely_single_post_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'related_posts_area', array(
			'label'   => esc_html__( 'Related Posts Area', 'shapely' ),
			'description' => esc_html__( 'This will enable/disable the related posts', 'shapely' ),
			'section' => 'shapely_single_post_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'post_author_area', array(
			'label'   => esc_html__( 'Post Author Area', 'shapely' ),
			'description' => esc_html__( 'This will show/hide the author box', 'shapely' ),
			'section' => 'shapely_single_post_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'post_author_left_side', array(
			'label'   => esc_html__( 'Post Author Left Side', 'shapely' ),
			'description' => esc_html__( 'This will move the author box from the bottom of the post on top on the left side', 'shapely' ),
			'section' => 'shapely_single_post_section',
			'type'    => 'checkbox',
		) );
		$wp_customize->add_control( 'post_author_email', array(
			'label'   => esc_html__( 'Show Author Email', 'shapely' ),
			'description' => esc_html__( 'This will show/hide the author\'s email from the author box', 'shapely' ),
			'section' => 'shapely_single_post_section',
			'type'    => 'checkbox',
		) );
	} // End if().
	$wp_customize->add_setting( 'single_post_layout_template', array(
		'default'           => 'sidebar-right',
		'sanitize_callback' => 'shapely_sanitize_blog_layout',
	) );

	$wp_customize->add_control( 'single_post_layout_template', array(
		'label'   => esc_html__( 'Single Post Template', 'shapely' ),
		'description' => esc_html__( 'Set the default template for single posts', 'shapely' ),
		'section' => 'shapely_single_post_section',
		'type'    => 'select',
		'choices' => array(
			'full-width'    => esc_html__( 'Full Width', 'shapely' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
			'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
			'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' ),
		),
	) );

	$wp_customize->add_setting( 'blog_layout_view', array(
		'default'           => 'grid',
		'sanitize_callback' => 'wp_kses_stripslashes',
	) );

	$wp_customize->add_control( 'blog_layout_view', array(
		'label'   => esc_html__( 'Blog Layout', 'shapely' ),
		'description' => esc_html__( 'Choose how you want to display posts in grid', 'shapely' ),
		'section' => 'shapely_blog_section',
		'type'    => 'select',
		'choices' => array(
			'grid'             => esc_html__( 'Grid only', 'shapely' ),
			'large_image_grid' => esc_html__( 'Large Image and Grid', 'shapely' ),
			'large_image'      => esc_html__( 'Large Images', 'shapely' ),
		),
	) );

	$wp_customize->add_setting( 'blog_layout_template', array(
		'default'           => 'sidebar-right',
		'sanitize_callback' => 'shapely_sanitize_blog_layout',
	) );

	$wp_customize->add_control( 'blog_layout_template', array(
		'label'   => esc_html__( 'Blog Template', 'shapely' ),
		'description' => esc_html__( 'Choose the template for your posts page', 'shapely' ),
		'section' => 'shapely_blog_section',
		'type'    => 'select',
		'choices' => array(
			'full-width'    => esc_html__( 'Full Width', 'shapely' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'shapely' ),
			'sidebar-left'  => esc_html__( 'Sidebar Left', 'shapely' ),
			'sidebar-right' => esc_html__( 'Sidebar Right', 'shapely' ),
		),
	) );

	// shapely_category_page_section
	$wp_customize->add_setting( 'show_category_on_category_page', array(
		'default'           => 1,
		'sanitize_callback' => 'shapely_sanitize_checkbox',
	) );
	if ( class_exists( 'Epsilon_Control_Toggle' ) ) {
		$wp_customize->add_control( new Epsilon_Control_Toggle( $wp_customize, 'show_category_on_category_page', array(
			'type'    => 'mte-toggle',
			'label'   => esc_html__( 'Show Category on Posts', 'shapely' ),
			'description' => esc_html__( 'Show/hide posts\' categories from the Category Page', 'shapely' ),
			'section' => 'shapely_blog_section',
		) ) );
	} else {
		$wp_customize->add_control( 'show_category_on_category_page', array(
			'label'   => esc_html__( 'Show Category on Posts', 'shapely' ),
			'description' => esc_html__( 'Show/hide posts\' categories from the Category Page', 'shapely' ),
			'section' => 'shapely_blog_section',
			'type'    => 'checkbox',
		) );
	}

}

add_action( 'customize_register', 'shapely_customizer' );

/**
 * Sanitize checkbox for WordPress customizer.
 */
function shapely_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Sanitize layout control.
 */
function shapely_sanitize_blog_layout( $input ) {
	if ( in_array( $input, array( 'full-width', 'no-sidebar', 'sidebar-left', 'sidebar-right' ) ) ) {
		return $input;
	} else {
		return 'sidebar-right';
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
		'full-width' => esc_html__( 'Full Width', 'shapely' ),
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
	</style>
	<?php
}

add_action( 'customize_controls_print_styles', 'shapely_customizer_custom_control_css' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function shapely_customize_preview_js() {
	wp_enqueue_script( 'shapely_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20140317', true );
}
add_action( 'customize_preview_init', 'shapely_customize_preview_js' );
