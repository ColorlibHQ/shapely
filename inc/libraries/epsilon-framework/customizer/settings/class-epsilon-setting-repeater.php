<?php

/**
 * Repeater Settings.
 */
class Epsilon_Setting_Repeater extends WP_Customize_Setting {
	/**
	 * ID of the post where we`ll save the meta
	 *
	 * @var
	 */
	public $save_as_meta;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @access public
	 * @since  1.2.0
	 *
	 * @param WP_Customize_Manager $manager The WordPress WP_Customize_Manager object.
	 * @param string               $id      A specific ID of the setting. Can be a theme mod or option name.
	 * @param array                $args    Setting arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		if ( isset( $args['save_as_meta'] ) ) {
			$this->save_as_meta = $args['save_as_meta'];
		}

		// Will onvert the setting from JSON to array. Must be triggered very soon.
		add_filter( "customize_sanitize_{$this->id}", array( $this, 'sanitize_repeater_setting' ), 10, 1 );
	}

	/**
	 * Fetch the value of the setting.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return mixed The value.
	 */
	public function value() {
		$value = parent::value();
		if ( ! is_array( $value ) ) {
			$value = array();
		}

		return $value;
	}

	/**
	 * Convert the JSON encoded setting coming from Customizer to an Array.
	 *
	 * @since  1.2.0
	 *
	 * @access public
	 *
	 * @param string $value URL Encoded JSON Value.
	 *
	 * @return array
	 */
	public function sanitize_repeater_setting( $value ) {
		if ( ! is_array( $value ) ) {
			$value = json_decode( urldecode( $value ) );
		}
		$sanitized = ( empty( $value ) || ! is_array( $value ) ) ? array() : $value;
		// Make sure that every row is an array, not an object.
		foreach ( $sanitized as $key => $_value ) {
			if ( empty( $_value ) ) {
				unset( $sanitized[ $key ] );
			} else {
				$sanitized[ $key ] = (array) $_value;
			}
		}
		// Reindex array.
		if ( is_array( $sanitized ) ) {
			$sanitized = array_values( $sanitized );
		}

		return $sanitized;
	}


	/**
	 * Overwrite saving
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function set_root_value( $value ) {
		if ( ! empty( $this->save_as_meta ) ) {
			update_post_meta(
				$this->save_as_meta,
				$this->id, array(
					$this->id => $value,
				)
			);

			return true;
		} else {
			return parent::set_root_value( $value );
		}
	}

	/**
	 * Get the root value for a setting, especially for multidimensional ones.
	 *
	 * @param mixed $default Value to return if root does not exist.
	 *
	 * @return mixed
	 */
	protected function get_root_value( $default = null ) {
		if ( ! empty( $this->save_as_meta ) ) {
			$draft = $this->manager->changeset_post_id();
			$arr   = null === $draft ? get_post_meta( $this->save_as_meta, $this->id, true ) : get_post( $draft );

			if ( null !== $draft && is_a( $arr, 'WP_Post' ) ) {
				$theme_slug = get_stylesheet();
				$string     = $theme_slug . '::' . $this->id;
				$known      = $arr->post_content;
				$arr        = array();
				$known      = json_decode( $known, true );
				if ( isset( $known[ $string ] ) && ! empty( $known[ $string ]['value'] ) ) {
					$arr = array(
						$this->id => $known[ $string ]['value'],
					);
				}
			}

			if ( empty( $arr ) ) {
				$arr = array(
					$this->id => array(),
				);
			}

			return $arr[ $this->id ];
		} else {
			return parent::get_root_value( $default );
		}
	}
}
