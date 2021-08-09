<?php
/**
 * Page rendering
 *
 * @package Portum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Epsilon_Page_Generator
 */
class Epsilon_Page_Generator {

	/**
	 * Holds the active sections
	 *
	 * @var array
	 */
	public $sections = array();

	/**
	 * The option that holds the sections
	 *
	 * @var string
	 */
	public $option = '';

	/**
	 * @var string
	 */
	public $post_id = '';

	/**
	 * Construct the frontpage class
	 *
	 * Epsilon_Page_Generator constructor.
	 */
	public function __construct( $option = '', $id = '' ) {
		$this->option  = $option;
		$this->post_id = $id;
		$this->set_sections();
	}

	/**
	 * We need to setup the sidebars, so we can load them appropriately
	 */
	public function set_sections() {
		if ( is_customize_preview() ) {
			$this->sections = $this->get_customizer_sections();
		} else {
			$this->sections = get_post_meta( $this->post_id, $this->option, true );
			$this->sections = isset( $this->sections[ $this->option ] ) ? $this->sections[ $this->option ] : array();
		}
	}

	/**
	 * Set the values manually
	 *
	 * @param $id
	 * @param $values
	 */
	public function set_manual_value( $id, $values ) {
		$this->sections[ $id ] = $values;
	}

	/**
	 * Get an instance of the frontpage renderer
	 */
	public static function get_instance( $option = '', $id = '' ) {
		static $inst;
		if ( ! $inst ) {
			$inst = new Epsilon_Page_Generator( $option, $id );
		}

		return $inst;
	}

	/**
	 * Return our current sections
	 * so we can update the customizer.
	 *
	 * @return bool
	 */
	private function get_customizer_sections() {
		global $wp_customize;

		$customizer_setting = get_theme_mod( $this->option, array() );
		if ( null === $wp_customize->get_setting( $this->option ) ) {
			return false;
		}

		$post = $wp_customize->post_value( $wp_customize->get_setting( $this->option ) );

		if ( null !== $post ) {
			return $customizer_setting;
		}

		if ( empty( $customizer_setting ) ) {
			$customizer_setting = get_post_meta( $this->post_id, $this->option, true );
			$customizer_setting = isset( $customizer_setting[ $this->option ] ) ? $customizer_setting[ $this->option ] : array();
		}

		return $customizer_setting;
	}

	/**
	 * @param string $key
	 * @param array  $default
	 * @param array  $grouping
	 *
	 * @return array|string
	 */
	public function get_repeater_field( $key = '', $default = array(), $grouping = array() ) {
		$data = get_theme_mod( $key, $default );

		/**
		 * In case we are in the customizer, we need to make sure that when we don`t have any values we stop here
		 */
		if ( is_customize_preview() ) {
			global $wp_customize;
			$post = $wp_customize->post_value( $wp_customize->get_setting( $key ) );
			if ( null !== $post ) {
				return $data;
			}
		}

		if ( empty( $data ) ) {
			$data = get_post_meta( Epsilon_Content_Backup::get_instance()->setting_page, $key, true );
			$data = isset( $data[ $key ] ) ? $data[ $key ] : $default;
		}

		if ( ! empty( $grouping ) ) {
			if ( 'all' !== $grouping['values'][0] ) {
				foreach ( $data as $k => $v ) {
					if ( is_array( $grouping['values'] ) && ! in_array( $v[ $grouping['group_by'] ], $grouping['values'] ) ) {
						unset( $data[ $k ] );
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Generate output
	 *
	 * Add actions before each section, maybe users would find them useful
	 */
	public function generate_output() {
		if ( empty( $this->sections ) && ! $this->sections ) {
			get_template_part( 'template-parts/frontpage/content-section-base' );
			return;
		}

		foreach ( $this->sections as $index => $section ) {
			$arg = '';
			do_action( 'before_' . $section['type'] . '_' . $index, $arg );

			$this->section_template( $section['type'], $section, $index );

			do_action( 'after_' . $section['type'] . '_' . $index, $arg );
		}
	}

	/**
	 * Proxy function to render sections in the frontend
	 *
	 * @param  string $template   Identifier for the template section.
	 * @param  array  $args       Template settings.
	 * @param string  $section_id Id of the section we need to render in the frontend.
	 */
	public function section_template( $template = '', $args = array(), $section_id = '' ) {
		$template_part = $args['type'] . '-section';
		set_query_var( 'section_id', $section_id );
		get_template_part( 'template-parts/frontpage/' . $template_part );
	}

	/**
	 * Group settings for easier rendering in the frontend
	 *
	 * @param array  $group  Array.
	 * @param string $prefix Prefix.
	 * @param bool   $single Key Val Assoc.
	 *
	 * @return array
	 */
	public function group_settings( $group = array(), $prefix = '', $single = false ) {
		$arr = array();

		foreach ( $group as $k => $v ) {
			if ( 0 === strpos( $k, $prefix ) ) {
				$parts = explode( '_', $k );
				$key   = end( $parts );
				array_pop( $parts );
				if ( $single ) {
					$arr[ $key ] = $v;
				} else {
					$arr[ $key ][ implode( '_', $parts ) ] = $v;
				}
				unset( $group[ $k ] );
			}
		}

		return array_filter( $arr );
	}

	/**
	 * @param $args
	 *
	 * @return array
	 */
	public static function generate_partial_section( $args ) {
		$self = self::get_instance( $args['control'], $args['postId'] );
		$self->set_manual_value( $args['id'], $args['value'] );
		ob_start();
		$self->section_template( $args['value']['type'], $args['value'], $args['id'] );

		return array(
			'message' => 'ok',
			'section' => ob_get_clean(),
		);
	}
}
