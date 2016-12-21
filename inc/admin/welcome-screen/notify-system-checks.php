<?php

if ( ! class_exists( 'Shapely_Notify_System' ) ) {
	/**
	 * Class Shapely_Notify_System
	 */
	class Shapely_Notify_System {
		/**
		 * @param $ver
		 *
		 * @return mixed
		 */
		public static function shapely_version_check( $ver ) {
			$shapely = wp_get_theme();

			return version_compare( $shapely['Version'], $ver, '>=' );
		}

		/**
		 * @return bool
		 */
		public static function shapely_is_not_static_page() {
			return 'page' == get_option( 'show_on_front' ) ? true : false;
		}


		/**
		 * @return bool
		 */
		public static function shapely_has_content() {
			$option = get_option( "shapely_imported_demo", false );
			if ( $option ) {
				return true;
			};

			return false;
		}

		/**
		 * @return bool|mixed
		 */
		public static function shapely_check_import_req() {
			$needs = array(
				'has_content' => self::shapely_has_content(),
				'has_plugin'  => self::shapely_has_plugin( 'shapely-companion' )
			);

			if ( $needs['has_content'] ) {
				return true;
			}

			if ( $needs['has_plugin'] ) {
				return false;
			}

			return true;
		}

		/**
		 * @return bool
		 */
		public static function shapely_check_plugin_is_installed( $slug ) {
			$slug2 = $slug;
			if ( $slug === 'wordpress-seo' ) {
				$slug2 = 'wp-seo';
			}

			$path = WPMU_PLUGIN_DIR . '/' . $slug . '/' . $slug2 . '.php';
			if ( ! file_exists( $path ) ) {
				$path = WP_PLUGIN_DIR . '/' . $slug . '/' . $slug2 . '.php';

				if ( ! file_exists( $path ) ) {
					$path = false;
				}
			}

			if ( file_exists( $path ) ) {
				return true;
			}

			return false;
		}

		/**
		 * @return bool
		 */
		public static function shapely_check_plugin_is_active( $slug ) {
			$slug2 = $slug;
			if ( $slug === 'wordpress-seo' ) {
				$slug2 = 'wp-seo';
			}

			$path = WPMU_PLUGIN_DIR . '/' . $slug . '/' . $slug2 . '.php';
			if ( ! file_exists( $path ) ) {
				$path = WP_PLUGIN_DIR . '/' . $slug . '/' . $slug2 . '.php';
				if ( ! file_exists( $path ) ) {
					$path = false;
				}
			}

			if ( file_exists( $path ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				return is_plugin_active( $slug . '/' . $slug2 . '.php' );
			}
		}

		public static function shapely_has_plugin( $slug = NULL ) {

			$check = array(
				'installed' => self::shapely_check_plugin_is_installed( $slug ),
				'active'    => self::shapely_check_plugin_is_active( $slug )
			);

			if ( ! $check['installed'] || ! $check['active'] ) {
				return false;
			}

			return true;
		}

		public static function shapely_companion_title() {
			$installed = self::shapely_check_plugin_is_installed( 'shapely-companion' );
			if ( ! $installed ) {
				return __( 'Install: Shapely Companion Plugin', 'shapely' );
			}

			$active = self::shapely_check_plugin_is_active( 'shapely-companion' );
			if ( $installed && ! $active ) {
				return __( 'Activate: Shapely Companion Plugin', 'shapely' );
			}

			return __( 'Install: Shapely Companion Plugin', 'shapely' );
		}

		public static function shapely_yoast_title() {
			$installed = self::shapely_check_plugin_is_installed( 'wordpress-seo' );
			if ( ! $installed ) {
				return __( 'Install: Yoast SEO Plugin', 'shapely' );
			}

			$active = self::shapely_check_plugin_is_active( 'wordpress-seo' );
			if ( $installed && ! $active ) {
				return __( 'Activate: Yoast SEO Plugin', 'shapely' );
			}

			return __( 'Install: Yoast SEO Plugin', 'shapely' );
		}

		public static function shapely_jetpack_title() {
			$installed = self::shapely_check_plugin_is_installed( 'jetpack' );
			if ( ! $installed ) {
				return __( 'Install: Jetpack by WordPress', 'shapely' );
			}

			$active = self::shapely_check_plugin_is_active( 'jetpack' );
			if ( $installed && ! $active ) {
				return __( 'Activate: Jetpack by WordPress', 'shapely' );
			}

			return __( 'Install: Jetpack by WordPress', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_companion_description() {
			$installed = self::shapely_check_plugin_is_installed( 'shapely-companion' );

			if ( ! $installed ) {
				return __( 'Please install Shapely Companion plugin.', 'shapely' );
			}

			$active = self::shapely_check_plugin_is_active( 'shapely-companion' );
			if ( $installed && ! $active ) {
				return __( 'Please activate Shapely Companion plugin.', 'shapely' );
			}

			return __( 'Please install Shapely Companion plugin.', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_jetpack_description() {
			$installed = self::shapely_check_plugin_is_installed( 'jetpack' );

			if ( ! $installed ) {
				return __( 'Please install Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
			}

			$active = self::shapely_check_plugin_is_active( 'jetpack' );
			if ( $installed && ! $active ) {
				return __( 'Please activate Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
			}

			return __( 'Please install Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
		}

		public static function shapely_yoast_description() {
			$installed = self::shapely_check_plugin_is_installed( 'wordpress-seo' );
			if ( ! $installed ) {
				return __( 'Please install Yoast SEO plugin.', 'shapely' );
			}

			$active = self::shapely_check_plugin_is_active( 'wordpress-seo' );
			if ( $installed && ! $active ) {
				return __( 'Please activate Yoast SEO plugin.', 'shapely' );
			}

			return __( 'Please install Yoast SEO plugin.', 'shapely' );

		}

		/**
		 * @return bool
		 */
		public static function shapely_is_not_template_front_page() {
			$page_id = get_option( 'page_on_front' );

			return get_page_template_slug( $page_id ) == 'page-templates/frontpage-template.php' ? true : false;
		}
	}
}