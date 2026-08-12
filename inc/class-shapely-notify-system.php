<?php

if ( ! class_exists( 'Shapely_Notify_System' ) ) {
	/**
	 * Class Shapely_Notify_System
	 */
	/**
	 * Plugin / content state checks for the welcome screen.
	 *
	 * Previously extended Epsilon_Notify_System. Nothing was actually inherited:
	 * every self:: call in this class resolves to a method defined here, the two
	 * methods that looked inherited (check_plugin_is_installed, check_plugin_is_active)
	 * were already overridden because the parent hardcoded
	 * ABSPATH . 'wp-content/plugins/' and broke on relocated content directories,
	 * and no parent-only method is referenced anywhere in the theme.
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
			$option = get_option( 'shapely_imported_demo', false );
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
				'has_plugin'  => self::shapely_has_plugin( 'shapely-companion' ),
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
		 * Resolve a plugin slug to its "dir/file.php" basename.
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return string Plugin basename, or an empty string when not installed.
		 */
		protected static function shapely_locate_plugin( $slug ) {
			$slug = trim( (string) $slug, '/' );
			if ( '' === $slug || false !== strpos( $slug, '.' ) ) {
				return '';
			}

			$candidates = array( $slug . '/' . $slug . '.php' );
			if ( 'wordpress-seo' === $slug ) {
				$candidates[] = $slug . '/wp-seo.php';
			}

			foreach ( $candidates as $candidate ) {
				if ( file_exists( WP_PLUGIN_DIR . '/' . $candidate ) ) {
					return $candidate;
				}
			}

			// Plugins whose main file does not match the directory name.
			$dir = WP_PLUGIN_DIR . '/' . $slug;
			if ( is_dir( $dir ) ) {
				foreach ( (array) glob( $dir . '/*.php' ) as $file ) {
					$data = get_file_data( $file, array( 'Name' => 'Plugin Name' ) );
					if ( ! empty( $data['Name'] ) ) {
						return $slug . '/' . basename( $file );
					}
				}
			}

			return '';
		}

		/**
		 * Whether the plugin is present on disk.
		 *
		 * Overrides the parent, which hardcodes ABSPATH . 'wp-content/plugins/' and
		 * therefore never finds anything on installs with a relocated content
		 * directory. WP_PLUGIN_DIR respects those installs.
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return bool
		 */
		public static function check_plugin_is_installed( $slug ) {
			if ( '' !== self::shapely_locate_plugin( $slug ) ) {
				return true;
			}

			// Must-use plugins are installed and permanently active.
			return defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR . '/' . trim( (string) $slug, '/' ) );
		}

		/**
		 * Whether the plugin is active for this site or across the network.
		 *
		 * @param string $slug Plugin directory slug.
		 *
		 * @return bool Always a bool; the parent fell through to null when the
		 *              plugin file was missing.
		 */
		public static function check_plugin_is_active( $slug ) {
			$basename = self::shapely_locate_plugin( $slug );

			if ( '' === $basename ) {
				// mu-plugins cannot be deactivated, so presence implies active.
				return defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR . '/' . trim( (string) $slug, '/' ) );
			}

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return is_plugin_active( $basename ) || is_plugin_active_for_network( $basename );
		}

		public static function shapely_has_plugin( $slug = null ) {

			$check = array(
				'installed' => self::check_plugin_is_installed( $slug ),
				'active'    => self::check_plugin_is_active( $slug ),
			);

			if ( ! $check['installed'] || ! $check['active'] ) {
				return false;
			}

			return true;
		}

		public static function shapely_companion_title() {
			$installed = self::check_plugin_is_installed( 'shapely-companion' );
			if ( ! $installed ) {
				return esc_html__( 'Install: Shapely Companion Plugin', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'shapely-companion' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Activate: Shapely Companion Plugin', 'shapely' );
			}

			return esc_html__( 'Install: Shapely Companion Plugin', 'shapely' );
		}

		public static function shapely_yoast_title() {
			$installed = self::check_plugin_is_installed( 'wordpress-seo' );
			if ( ! $installed ) {
				return esc_html__( 'Install: Yoast SEO Plugin', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'wordpress-seo' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Activate: Yoast SEO Plugin', 'shapely' );
			}

			return esc_html__( 'Install: Yoast SEO Plugin', 'shapely' );
		}

		public static function shapely_jetpack_title() {
			$installed = self::check_plugin_is_installed( 'jetpack' );
			if ( ! $installed ) {
				return esc_html__( 'Install: Jetpack by WordPress', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'jetpack' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Activate: Jetpack by WordPress', 'shapely' );
			}

			return esc_html__( 'Install: Jetpack by WordPress', 'shapely' );
		}

		public static function shapely_kaliforms_title() {
			$installed = self::check_plugin_is_installed( 'kali-forms' );
			if ( ! $installed ) {
				return esc_html__( 'Install: Kali Forms', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'kali-forms' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Activate: Kali Forms', 'shapely' );
			}

			return esc_html__( 'Install: Kali Forms', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_companion_description() {
			$installed = self::check_plugin_is_installed( 'shapely-companion' );

			if ( ! $installed ) {
				return esc_html__( 'Please install Shapely Companion plugin.', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'shapely-companion' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Please activate Shapely Companion plugin.', 'shapely' );
			}

			return esc_html__( 'Please install Shapely Companion plugin.', 'shapely' );
		}

		/**
		 * @return string
		 */
		public static function shapely_jetpack_description() {
			$installed = self::check_plugin_is_installed( 'jetpack' );

			if ( ! $installed ) {
				return esc_html__( 'Please install Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'jetpack' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Please activate Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
			}

			return esc_html__( 'Please install Jetpack by WordPress. Note that you won\'t be able to use the Testimonials and Portfolio widgets without it.', 'shapely' );
		}

		public static function shapely_kaliforms_description() {
			$installed = self::check_plugin_is_installed( 'kali-forms' );

			if ( ! $installed ) {
				return esc_html__( 'Please install Kali Forms. Note that you won\'t be able to use Contact widget without it.', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'kali-forms' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Please activate Kali Forms. Note that you won\'t be able to use Contact widget without it.', 'shapely' );
			}

			return esc_html__( 'Please install Kali Forms. Note that you won\'t be able to use Contact widget without it.', 'shapely' );
		}

		public static function shapely_yoast_description() {
			$installed = self::check_plugin_is_installed( 'wordpress-seo' );
			if ( ! $installed ) {
				return esc_html__( 'Please install Yoast SEO plugin.', 'shapely' );
			}

			$active = self::check_plugin_is_active( 'wordpress-seo' );
			if ( $installed && ! $active ) {
				return esc_html__( 'Please activate Yoast SEO plugin.', 'shapely' );
			}

			return esc_html__( 'Please install Yoast SEO plugin.', 'shapely' );

		}

		/**
		 * @return bool
		 */
		public static function shapely_is_not_template_front_page() {
			$page_id = get_option( 'page_on_front' );

			return get_page_template_slug( $page_id ) == 'page-templates/frontpage-template.php' ? true : false;
		}
	}
}// End if().
