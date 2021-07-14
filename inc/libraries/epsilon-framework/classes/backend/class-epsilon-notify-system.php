<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Notify_System
 */
class Epsilon_Notify_System {


	public static $plugins;

	/**
	 * @param $ver
	 *
	 * @return mixed
	 */
	public static function version_check( $ver ) {
		$theme = wp_get_theme();

		return version_compare( $theme['Version'], $ver, '>=' );
	}

	/**
	 * @return bool
	 */
	public static function is_not_static_page() {
		return 'page' == get_option( 'show_on_front' ) ? true : false;
	}

	/**
	 * @return array
	 */
	public static function _get_plugins() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins();

	}

	/**
	 * @param $slug
	 *
	 * @return mixed
	 */
	public static function _get_plugin_basename_from_slug( $slug ) {

		if ( empty( self::$plugins ) ) {
			self::$plugins = array_keys( self::_get_plugins() );
		}

		$keys = self::$plugins;
		foreach ( $keys as $key ) {
			if ( preg_match( '|^' . $slug . '/|', $key ) ) {
				return $key;
			}
		}

		return $slug;
	}

	/**
	 * @param $slug
	 *
	 * @return bool
	 */
	public static function check_plugin_is_installed( $slug ) {
		$plugin_path = self::_get_plugin_basename_from_slug( $slug );
		if ( file_exists( ABSPATH . 'wp-content/plugins/' . $plugin_path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $slug
	 *
	 * @return bool
	 */
	public static function check_plugin_is_active( $slug ) {
		$plugin_path = self::_get_plugin_basename_from_slug( $slug );
		if ( file_exists( ABSPATH . 'wp-content/plugins/' . $plugin_path ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			return is_plugin_active( $plugin_path );
		}
	}

	/**
	 * Verify the status of a plugin
	 *
	 * @param string      $get         Return title/description/etc.
	 * @param string      $slug        Plugin slug.
	 * @param string      $plugin_name Plugin name.
	 * @param bool|string $special     Callback to verify a certain plugin
	 *
	 * @return mixed
	 */
	public static function plugin_verifier( $slug = '', $get = '', $plugin_name = '', $special = false ) {
		if ( false !== $special ) {
			$arr = self::$special();
		} else {
			$arr = array(
				'installed' => Epsilon_Notify_System::check_plugin_is_installed( $slug ),
				'active'    => Epsilon_Notify_System::check_plugin_is_active( $slug ),
			);

			if ( empty( $get ) ) {
				$arr = array_filter( $arr );

				return 2 === count( $arr );
			}
		}

		// Translators: %s is the plugin name.
		$arr['title'] = sprintf( __( 'Install: %s', 'epsilon-framework' ), $plugin_name );
		// Translators: %s is the plugin name.
		$arr['description'] = sprintf( __( 'Please install %s in order to create the demo content.', 'epsilon-framework' ), $plugin_name );

		if ( $arr['installed'] ) {
			// Translators: %s is the plugin name
			$arr['title'] = sprintf( __( 'Activate: %s', 'epsilon-framework' ), $plugin_name );
			// Translators: %s is the plugin name
			$arr['description'] = sprintf( __( 'Please activate %s in order to create the demo content.', 'epsilon-framework' ), $plugin_name );
		}

		return $arr[ $get ];
	}

	/**
	 * @param $args
	 *
	 * @return string
	 */
	public static function dismiss_required_action( $args ) {
		$option = get_option( $args['option'] );

		if ( $option ) :
			$option[ $args['id'] ] = false;
			update_option( $args['option'], $option );
		else :
			$option = array(
				$args['id'] => false,
			);
			update_option( $args['option'], $option );
		endif;

		return 'ok';
	}

}
