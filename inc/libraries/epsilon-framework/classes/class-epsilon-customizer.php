<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Easier way to add panels,sections,controls
 *
 * @since 1.0.0
 *
 * Class Epsilon_Customizer
 */
class Epsilon_Customizer {
	/**
	 * Holds the WP Customizer Object
	 *
	 * @since 1.0.0
	 * @var WP Customize Object
	 */
	public static $manager;
	/**
	 * URL Being edited
	 *
	 * @since 1.2.0
	 * @var
	 */
	public static $url = null;
	/**
	 * The single instance of the backup class
	 *
	 * @var     object
	 * @access   private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * Epsilon_Customizer constructor.
	 *
	 * @param $manager WP_Customize_Manager.
	 */
	public function __construct( $manager ) {
		self::$manager = $manager;

		/**
		 * Fallback, if somehow the argument is not the manager we use the global
		 */
		if ( ! is_a( $manager, 'WP_Customize_Manager' ) ) {
			global $wp_customize;
			self::$manager = $wp_customize;
		}
	}

	/**
	 * @param array|object $manager WP Customizer object.
	 *
	 * @return Epsilon_Customizer|object
	 */
	public static function get_instance( $manager = array() ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $manager );
		}

		return self::$_instance;
	}

	/**
	 * This function is called by self::add_field()
	 *
	 * @since 1.0.0
	 *
	 * @param $id
	 */
	private static function add_setting( $id, array $args = array() ) {
		/**
		 * We need to use setting_type to determine the sanitizer
		 */
		if ( empty( $args['setting_type'] ) ) {
			$args['setting_type'] = $args['type'];
		}

		/**
		 * Setting types can be theme_mod or options, control's type is not needed here
		 */
		unset( $args['type'] );

		/**
		 * Determine sanitizer
		 */
		if ( empty( $args['sanitize_callback'] ) ) {
			$args['sanitize_callback'] = self::_get_sanitizer( $args['setting_type'] );
			if ( 'epsilon-section-repeater' === $args['setting_type'] ) {
				unset( $args['sanitize_callback'] );
			}
		}

		/**
		 * Create the class name for the setting, repeater field has a different setting class
		 */
		$class = self::_get_type( $args['setting_type'], 'setting' );

		/**
		 * Register it
		 */
		self::$manager->add_setting(
			new $class['class'](
				self::$manager,
				$id,
				$args
			)
		);
	}

	/**
	 * Add control function ( this will automatically add the setting, based on the field type )
	 *
	 * @since 1.0.0
	 *
	 * @param       $id
	 * @param array $args
	 */
	public static function add_field( $id, array $args = array() ) {
		$args['type'] = isset( $args['type'] ) ? $args['type'] : 'control';
		if ( 'epsilon-section-repeater' === $args['type'] && ( isset( $args['page_builder'] ) && true === $args['page_builder'] ) ) {
			self::add_page_builder( $id, $args );

			return;
		}
		/**
		 * Add setting
		 */
		self::add_setting( $id, $args );
		$args['backup'] = isset( $args['backup'] ) ? $args['backup'] : false;
		/**
		 * Get class name, if it's an epsilon control, we need to build the class name accordingly
		 */
		$field_type = self::_get_type( $args['type'], 'control' );

		/**
		 * This array SHOULD always be backed up
		 */
		$must_backup = array(
			'epsilon-section-repeater',
			'epsilon-repeater',
		);

		if ( in_array( $args['type'], $must_backup ) || true === $args['backup'] ) {
			$instance = Epsilon_Content_Backup::get_instance();
			$instance->add_field( $id, $args );
		}

		/**
		 * Register the control
		 */
		self::$manager->add_control(
			new $field_type['class'](
				self::$manager,
				$id,
				$args
			)
		);
	}

	/**
	 * Add section
	 *
	 * @since 1.0.0
	 *
	 * @param       $id
	 * @param array $args
	 */
	public static function add_section( $id, array $args = array() ) {
		$args['type'] = isset( $args['type'] ) ? $args['type'] : 'section';

		$class = self::_get_section_type( $args['type'] );
		self::$manager->add_section(
			new $class['class'](
				self::$manager,
				$id,
				$args
			)
		);
	}

	/**
	 * Add panel
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public static function add_panel( $id, array $args = array() ) {
		$args['type'] = isset( $args['type'] ) ? $args['type'] : 'panel';

		$class = self::_get_panel_type( $args['type'] );
		self::$manager->add_panel(
			new $class['class'](
				self::$manager,
				$id,
				$args
			)
		);
	}

	/**
	 * Add multiple customizer elements at once
	 *
	 * @since 1.0.0
	 *
	 * @param array $collection
	 */
	public static function add_multiple( array $collection = array() ) {
		foreach ( $collection as $type => $items ) {
			foreach ( $items as $item ) {
				$func = 'self::add_' . $type;
				call_user_func( $func, $item['id'], $item['args'] );
			}
		}
	}

	/**
	 * Get the class name of the section type
	 *
	 * @since 1.2.6
	 *
	 * @param string $type
	 */
	public static function _get_section_type( $type = '' ) {
		$class = '';
		$type  = explode( '-', $type );
		/**
		 * Let's make sure it's an Epsilon Class
		 */
		if ( 1 < count( $type ) && 'epsilon' === $type[0] ) {
			$class = implode( '_', $type );
		}

		/**
		 * Provide a default
		 */
		if ( ! class_exists( $class ) ) {
			$class = 'WP_Customize_Section';
		}

		return array(
			'class' => $class,
		);
	}

	/**
	 * Get the class name of the panel type
	 *
	 * @since 1.3.4
	 *
	 * @param string $type
	 */
	public static function _get_panel_type( $type = '' ) {
		$class = '';
		$type  = explode( '-', $type );
		/**
		 * Let's make sure it's an Epsilon Class
		 */
		if ( 1 < count( $type ) && 'epsilon' === $type[0] ) {
			$class = implode( '_', $type );
		}

		/**
		 * Provide a default
		 */
		if ( ! class_exists( $class ) ) {
			$class = 'WP_Customize_Panel';
		}

		return array(
			'class' => $class,
		);
	}

	/**
	 * Get the class name and field type
	 *
	 * @since 1.0.0
	 *
	 * @param $type
	 */
	public static function _get_type( $type = '', $prefix = '' ) {
		$class = '';
		if ( 'setting' === $prefix && ( 'epsilon-section-repeater' === $type || 'epsilon-repeater' === $type ) ) {
			$type = 'epsilon-repeater';
		}

		$type = explode( '-', $type );

		if ( 1 < count( $type ) && 'epsilon' === $type[0] ) {
			$class = implode( '_', $type );
			$class = str_replace( 'epsilon_', 'epsilon_' . $prefix . '_', $class );
		}

		/**
		 * Provide a default
		 */
		if ( ! class_exists( $class ) ) {
			$class = 'WP_Customize_' . $prefix;
		}

		return array(
			'class' => $class,
		);
	}

	/**
	 * Dynamic sanitization
	 *
	 * @param $type
	 *
	 * @return array|string
	 */
	public static function _get_sanitizer( $type = '' ) {
		/**
		 * Dynamic sanitizer, based on field type
		 */
		switch ( $type ) {
			case 'text':
				$sanitizer = 'sanitize_text_field';
			case 'url':
				$sanitizer = 'esc_url_raw';
				break;
			case 'email':
				$sanitizer = 'sanitize_email';
				break;
			case 'textarea':
				$sanitizer = 'sanitize_textarea_field';
				break;
			case 'epsilon-toggle':
			case 'checkbox':
				$sanitizer = array( 'Epsilon_Sanitizers', 'checkbox' );
				break;
			case 'radio':
				$sanitizer = array( 'Epsilon_Sanitizers', 'radio_buttons' );
				break;
			case 'epsilon-image':
				$sanitizer = 'esc_url_raw';
				break;
			case 'epsilon-text-editor':
				$sanitizer = array( 'Epsilon_Sanitizers', 'textarea_nl2br' );
				break;
			case 'epsilon-repeater':
			case 'epsilon-section-repeater':
				/**
				 * Already sanitized by the setting
				 */
				$sanitizer = '';
				break;
			case 'epsilon-slider':
				$sanitizer = 'absint';
				break;
			case 'epsilon-typography':
				$sanitizer = 'sanitize_text_field';
				break;
			case 'epsilon-layouts':
				$sanitizer = 'sanitize_text_field';
				break;
			case 'epsilon-color-scheme':
				$sanitizer = 'sanitize_text_field';
				break;
			case 'epsilon-color-picker':
				$sanitizer    = 'sanitize_hex_color';
				$args['mode'] = isset( $args['mode'] ) ? $args['mode'] : 'hex';
				if ( 'rgba' === $args['mode'] ) {
					$sanitizer = array( 'Epsilon_Sanitizers', 'rgba' );
				}
				break;
			case 'epsilon-button-group':
				$sanitizer = array( 'Epsilon_Sanitizers', 'radio_buttons' );
				break;
			case 'epsilon-selectize':
				$sanitize = array( 'Epsilon_Sanitizers', 'selectize' );
				break;
			default:
				$sanitizer = 'sanitize_text_field';
				break;
		}// End switch().

		return $sanitizer;
	}

	/**
	 * Page builder functionality
	 *
	 * @since 1.2.0
	 */
	public static function add_page_builder( $id, $args ) {
		$pages = new WP_Query(
			array(
				'post_type'        => 'page',
				'nopaging'         => true,
				'suppress_filters' => true,
				'post__not_in'     => array(
					Epsilon_Content_Backup::get_instance()->setting_page,
				),
			)
		);

		$ids = array();

		if ( $pages->have_posts() ) {
			foreach ( $pages->posts as $page ) {
				$ids[] = $page->ID;
			}
		}

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();

				$args['backup']       = isset( $args['backup'] ) ? $args['backup'] : false;
				$args['save_as_meta'] = get_the_ID();
				$args['label']        = esc_html( get_the_title() );

				/**
				 * Add setting
				 */
				self::add_setting( $id . '_' . get_the_ID(), $args );

				/**
				 * Get class name, if it's an epsilon control, we need to build the class name accordingly
				 */
				$field_type = self::_get_type( $args['type'], 'control' );

				/**
				 * This array SHOULD always be backed up
				 */
				$must_backup = array(
					'epsilon-section-repeater',
				);

				if ( in_array( $args['type'], $must_backup ) || true === $args['backup'] ) {
					$instance = Epsilon_Content_Backup::get_instance();
					$instance->add_pages( get_the_ID(), $id . '_' . get_the_ID(), $args );
				}

				/**
				 * Register the control
				 */
				self::$manager->add_control(
					new Epsilon_Control_Section_Repeater(
						self::$manager,
						$id . '_' . get_the_ID(),
						$args
					)
				);
			}// End while().
		}// End if().

		wp_reset_postdata();
	}

	/**
	 * Add quick action links to posts
	 */
	public static function add_action_links( $actions, $post ) {
		if ( absint( Epsilon_Content_Backup::get_instance()->setting_page ) === $post->ID ) {
			return $actions;
		}

		if ( 'draft' === $post->post_status ) {
			return $actions;
		}

		if ( defined( 'POLYLANG_VERSION' ) ) {
			$language = pll_get_post_language( $post->ID, 'slug' );
			$default  = pll_default_language( 'slug' );
			if ( $language !== $default ) {
				return $actions;
			}
		}


		$actions['customize'] = '<a href="' . esc_url( get_admin_url() . 'customize.php?url=' . get_permalink( $post->ID ) ) . '" />' . esc_html__( 'Customize', 'epsilon-framework' ) . '</a>';

		return $actions;
	}
}
