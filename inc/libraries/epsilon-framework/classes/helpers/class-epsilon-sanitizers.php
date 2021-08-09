<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Sanitizers
 */
class Epsilon_Sanitizers {
	/**
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return string
	 *
	 * https://wordpress.stackexchange.com/questions/257581/escape-hexadecimals-rgba-values
	 */
	public static function rgba( $value ) {
		// If empty or an array return transparent
		if ( empty( $value ) || is_array( $value ) ) {
			return 'rgba(0,0,0,0)';
		}

		// If string does not start with 'rgba', then treat as hex
		// sanitize the hex color and finally convert hex to rgba
		if ( false === strpos( $value, 'rgba' ) ) {
			return sanitize_hex_color( $value );
		}
		// By now we know the string is formatted as an rgba color so we need to further sanitize it.
		$value = str_replace( ' ', '', $value );
		sscanf( $value, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return int
	 */
	public static function checkbox( $value ) {
		return (bool) $value;
	}

	/**
	 * @since 1.0.0
	 *
	 * Simple function to validate choices from radio buttons
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public static function radio_buttons( $input, $setting ) {
		global $wp_customize;

		$control = $wp_customize->get_control( $setting->id );

		if ( is_array( $control->choices ) && array_key_exists( $input, $control->choices ) ) {
			return $input;
		}

		return $setting->default;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public static function textarea_nl2br( $input ) {
		return nl2br( $input );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public static function selectize( $input ) {
		$input = array_map( 'sanitize_text_field', $input );

		return $input;
	}

	/**
	 * Recursive array map functionality ( for field sanitize)
	 *
	 * @param $callback
	 * @param $array
	 *
	 * @return array
	 */
	public static function array_map_recursive( $callback, $array ) {
		$func = function ( $item ) use ( &$func, &$callback ) {
			return is_array( $item ) ? array_map( $func, $item ) : call_user_func( $callback, $item );
		};

		return array_map( $func, $array );
	}
}
