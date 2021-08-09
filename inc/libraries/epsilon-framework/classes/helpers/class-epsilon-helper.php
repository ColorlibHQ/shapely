<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @since 1.1.0
 * Class Epsilon_Helper
 */
class Epsilon_Helper {
	/**
	 * Function that retrieves image sizes defined in theme
	 *
	 * @return array
	 */
	public static function get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		return $sizes;
	}

	/**
	 * Format a CSS string used in the section repeater template
	 */
	public static function get_css_string( $fields ) {
		$css        = '';
		$translator = array(
			'topleft'     => 'top left',
			'top'         => 'top',
			'topright'    => 'top right',
			'left'        => 'left',
			'center'      => 'center',
			'right'       => 'right',
			'bottomleft'  => 'bottom left',
			'bottom'      => 'bottom',
			'bottomright' => 'bottom right',
		);

		foreach ( $fields as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			switch ( $key ) {
				case 'background-image':
					$css .= $key . ': url(' . esc_url( $value ) . ');';
					break;
				case 'background-position':
					$css .= $key . ': ' . esc_attr( isset( $translator[ $value ] ) ? $translator[ $value ] : 'center' ) . ';';
					break;
				case 'background-size':
					$css .= $key . ': ' . esc_attr( $value ) . ';';
					break;
				case 'background-color':
					$css .= $key . ':' . esc_attr( $value ) . ';';
					break;
				default:
					$css .= '';
					break;
			}
		}

		return $css;
	}
}
