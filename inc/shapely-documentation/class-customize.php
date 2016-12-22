<?php

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Shapely_Documentation_Customize {
	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		static $instance = NULL;
		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {
	}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {
		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );
		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  object $manager
	 *
	 * @return void
	 */
	public function sections( $manager ) {
		// Load custom sections.
		require_once( trailingslashit( get_template_directory() ) . 'inc/shapely-documentation/class-shapely-documentation-customizer.php' );
		// Register custom section types.
		$manager->register_section_type( 'Shapely_Documentation_Customize_Section' );
		// Register sections.
		$manager->add_section(
			new Shapely_Documentation_Customize_Section(
				$manager,
				'shapely_documentation',
				array(
					'title'    => esc_html__( 'Shapely', 'shapely' ),
					'pro_text' => esc_html__( 'Documentation', 'shapely' ),
					'pro_url'  => 'https://colorlib.com/wp/support/shapely/',
					'priority' => 0,
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {
		wp_enqueue_script( 'shapely-documentation-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/shapely-documentation/customize-controls.js', array( 'customize-controls' ) );
		wp_enqueue_style( 'shapely-documentation-customize-controls', trailingslashit( get_template_directory_uri() ) . 'inc/shapely-documentation/customize-controls.css' );
	}
}

Shapely_Documentation_Customize::get_instance();