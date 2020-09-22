<?php
/**
 * Epsilon Welcome Screen
 *
 * @package Epsilon Framework
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Welcome_Screen
 */
class Epsilon_Welcome_Screen {
	/**
	 * Theme name
	 *
	 * @var string
	 */
	public $theme_name = '';

	/**
	 * Theme slug
	 *
	 * @var string
	 */
	public $theme_slug = '';

	/**
	 * Author Logo
	 *
	 * @var string
	 */
	public $author_logo = '';

	/**
	 * Required actions
	 *
	 * @var array|mixed
	 */
	public $actions = array();

	/**
	 * Actions count
	 *
	 * @var int
	 */
	public $actions_count = 0;

	/**
	 * Required Plugins
	 *
	 * @var array|mixed
	 */
	public $plugins = array();

	/**
	 * Notice message
	 *
	 * @var mixed|string
	 */
	public $notice = '';

	/**
	 * Tab sections
	 *
	 * @var array
	 */
	public $sections = array();

	/**
	 * EDD Strings
	 *
	 * @var array
	 */
	public $strings = array();

	/**
	 * EDD load
	 *
	 * @var bool
	 */
	public $edd = false;

	/**
	 * If we have an EDD product, we need to add an ID
	 *
	 * @var string
	 */
	public $download_id = '';

	/**
	 * Epsilon_Welcome_Screen constructor.
	 *
	 * @param array $config Configuration array.
	 */
	public function __construct( $config = array() ) {
		$theme    = wp_get_theme();
		$defaults = array(
			'theme-name'  => $theme->get( 'Name' ),
			'theme-slug'  => $theme->get( 'TextDomain' ),
			'author-logo' => get_template_directory_uri() . '/inc/libraries/welcome-screen/img/colorlib.png',
			'actions'     => array(),
			'plugins'     => array(),
			'notice'      => '',
			'sections'    => array(),
			'edd'         => false,
			'download_id' => '',
		);

		$config = wp_parse_args( $config, $defaults );

		/**
		 * Configure our welcome screen
		 */
		$this->theme_name    = $config['theme-name'];
		$this->theme_slug    = $config['theme-slug'];
		$this->author_logo   = $config['author-logo'];
		$this->actions       = $config['actions'];
		$this->actions_count = $this->count_actions();
		$this->plugins       = $config['plugins'];
		$this->notice        = $config['notice'];
		$this->sections      = $config['sections'];
		$this->edd           = $config['edd'];
		$this->download_id   = $config['download_id'];

		if ( $this->edd ) {
			$this->strings = EDD_Theme_Helper::get_strings();
		}

		if ( empty( $config['sections'] ) ) {
			$this->sections = $this->set_default_sections( $config );
		}

		/**
		 * Create the dashboard page
		 */
		add_action( 'admin_menu', array( $this, 'welcome_screen_menu' ) );

		/**
		 * Load the welcome screen styles and scripts
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		/**
		 * Add the notice in the admin backend
		 */
		$this->add_admin_notice();

		/**
		 * Ajax callbacks
		 */
		add_action(
			'wp_ajax_welcome_screen_ajax_callback', array(
				$this,
				'welcome_screen_ajax_callback',
			)
		);

		if ( $this->edd ) {
			/**
			 * Initiate EDD Stuff
			 */
			add_action( 'admin_init', array( 'EDD_Theme_Helper', 'init' ) );
			add_filter( 'http_request_args', array( 'EDD_Theme_Helper', 'disable_wporg_request' ), 5, 2 );

			add_action(
				'update_option_' . $this->theme_slug . '_license_key',
				array(
					'EDD_Theme_Helper',
					'license_activator_deactivator',
				),
				10,
				2
			);
		}
	}

	/**
	 * AJAX Handler
	 */
	public function welcome_screen_ajax_callback() {
		if ( !isset( $_POST['args'], $_POST['args']['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['args']['nonce'] ), 'welcome_nonce' ) ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Not allowed', 'epsilon-framework' ),
					)
				)
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
		    wp_die(
				json_encode(
					array(
						'status' => false,
						'error'  => 'Not allowed',
					)
				)
			);
		}

		$args_action = array_map( 'sanitize_text_field', wp_unslash( $_POST['args']['action'] ) );

		if ( count( $args_action ) !== 2 ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Not allowed', 'epsilon-framework' ),
					)
				)
			);
		}

		if ( ! in_array( $args_action[0], array( 'Epsilon_Welcome_Screen' ) ) ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Class Not allowed', 'epsilon-framework' ),
					)
				)
			);
		}

		if ( ! class_exists( $args_action[0] ) ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Class does not exist', 'epsilon-framework' ),
					)
				)
			);
		}

		$class  = $args_action[0];
		$method = $args_action[1];
		$args   = array();

		if ( is_array( $_POST['args']['args'] ) ) {
			$args = Epsilon_Sanitizers::array_map_recursive( 'sanitize_text_field', wp_unslash( $_POST['args']['args'] ) );
		}

		$response = $class::$method( $args );

		if ( is_array( $response ) ) {
			wp_die( wp_json_encode( $response ) );
		}

		if ( 'ok' === $response ) {
			wp_die(
				wp_json_encode(
					array(
						'status'  => true,
						'message' => 'ok',
					)
				)
			);
		}

		wp_die(
			wp_json_encode(
				array(
					'status'  => false,
					'message' => 'nok',
				)
			)
		);
	}

	/**
	 * Instance constructor
	 *
	 * @param array $config Configuration array.
	 *
	 * @returns object
	 */
	public static function get_instance( $config = array() ) {
		static $inst;

		if ( ! $inst ) {
			$inst = new Epsilon_Welcome_Screen( $config );
		}

		return $inst;
	}

	/**
	 * Load welcome screen css and javascript
	 */
	public function enqueue() {
		if ( is_admin() ) {
			wp_enqueue_style(
				'welcome-screen',
				get_template_directory_uri() . '/inc/libraries/welcome-screen/css/welcome.css'
			);

			wp_enqueue_script(
				'welcome-screen',
				get_template_directory_uri() . '/inc/libraries/welcome-screen/js/welcome.js',
				array(
					'jquery-ui-slider',
				),
				'12123'
			);

			wp_localize_script(
				'welcome-screen',
				'welcomeScreen',
				array(
					'nr_actions_required'      => absint( $this->count_actions() ),
					'template_directory'       => esc_url( get_template_directory_uri() ),
					'no_required_actions_text' => esc_html__( 'Hooray! There are no required actions for you right now.', 'epsilon-framework' ),
					'ajax_nonce'               => wp_create_nonce( 'welcome_nonce' ),
					'activating_string'        => esc_html__( 'Activating', 'epsilon-framework' ),
					'body_class'               => 'appearance_page_' . $this->theme_slug . '-welcome',
					'no_actions'               => esc_html__( 'Hooray! There are no required actions for you right now.', 'epsilon-framework' ),
				)
			);
		}
	}

	/**
	 * Return the actions left
	 *
	 * @return array|mixed
	 */
	private function get_actions_left() {
		if ( ! empty( $this->actions ) ) {
			$actions_left = get_option( $this->theme_slug . '_actions_left', array() );
			return $actions_left;
		}

		return array();
	}

	/**
	 * Returns the plugins left to be installed
	 *
	 * @return array|mixed
	 */
	private function get_plugins_left() {
		if ( ! empty( $this->plugins ) ) {
			$plugins_left = get_option( $this->theme_slug . '_plugins_left', array() );
			if ( empty( $plugins_left ) ) {
				foreach ( $this->plugins as $plugin => $prop ) {
					$plugins_left[ $plugin ] = true;
				}

				return $plugins_left;
			}

			return $plugins_left;
		}

		return array();
	}

	/**
	 * Adds an admin notice in the backend
	 *
	 * If the Epsilon Notification class does not exist, we stop
	 */
	private function add_admin_notice() {
		if ( ! class_exists( 'Epsilon_Notifications' ) ) {
			return;
		}

		if ( empty( $this->notice ) ) {
			/* Translators: Notice Title */
			$this->notice .= '<h3>' . sprintf( esc_html__( 'Welcome to %1$s', 'epsilon-framework' ), $this->theme_name ) . '</h3>';
			$this->notice .= '<p>';
			$this->notice .=
				sprintf( /* Translators: Notice */
					esc_html__( 'Welcome! Thank you for choosing %3$s! To fully take advantage of the best our theme can offer please make sure you visit our %1$swelcome page%2$s.', 'epsilon-framework' ),
					'<a href="' . esc_url( admin_url( 'themes.php?page=' . $this->theme_slug . '-welcome' ) ) . '">',
					'</a>',
					$this->theme_name
				);
			$this->notice .= '</p>';
			/* Translators: Notice URL */
			$this->notice .= '<p><a href="' . esc_url( admin_url( 'themes.php?page=' . $this->theme_slug . '-welcome&tab=recommended-actions' ) ) . '" class="button button-primary" style="text-decoration: none;"> ' . sprintf( esc_html__( 'Get started with %1$s', 'epsilon-framework' ), $this->theme_name ) . '</a></p>';

		}

		$notifications = Epsilon_Notifications::get_instance();
		$notifications->add_notice(
			array(
				'id'      => 'shapely_welcome_notification',
				'type'    => 'notice notice-success',
				'message' => $this->notice,
			)
		);
	}

	/**
	 * Registers the welcome screen menu
	 */
	public function welcome_screen_menu() {
		/* Translators: Menu Title */
		$title = sprintf( esc_html__( 'About %1$s', 'epsilon-framework' ), esc_html( $this->theme_name ) );

		if ( 0 < $this->actions_count ) {
			$title .= '<span class="badge-action-count">' . esc_html( $this->actions_count ) . '</span>';
		}

		add_theme_page(
			$this->theme_name,
			$title,
			'edit_theme_options',
			$this->theme_slug . '-welcome',
			array(
				$this,
				'render_welcome_screen',
			)
		);
	}

	/**
	 * Render the welcome screen
	 */
	public function render_welcome_screen() {
		require_once( ABSPATH . 'wp-load.php' );
		require_once( ABSPATH . 'wp-admin/admin.php' );
		require_once( ABSPATH . 'wp-admin/admin-header.php' );

		$theme = wp_get_theme();
		$tab   = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'getting-started';

		?>
		<div class="wrap about-wrap epsilon-wrap">
			<h1>
				<?php
				/* Translators: Welcome Screen Title. */
				echo sprintf( esc_html__( 'Welcome to %1$s - v', 'epsilon-framework' ), esc_html( $this->theme_name ) ) . esc_html( $theme['Version'] );
				?>
			</h1>
			<div class="about-text">
				<?php
				/* Translators: Welcome Screen Description. */
				echo sprintf( esc_html__( '%1$s is now installed and ready to use! Get ready to build something beautiful. We hope you enjoy it! We want to make sure you have the best experience using %1$s and that is why we gathered here all the necessary information for you. We hope you will enjoy using %1$s, as much as we enjoy creating great products.', 'epsilon-framework' ), esc_html( $this->theme_name ) );
				?>
			</div>
			<div class="wp-badge epsilon-welcome-logo"></div>

			<h2 class="nav-tab-wrapper wp-clearfix">
				<?php foreach ( $this->sections as $id => $section ) { ?>
					<a class="nav-tab <?php echo $id === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $section['url'] ); ?>"><?php echo wp_kses_post( $section['label'] ); ?></a>
				<?php } ?>
			</h2>

			<?php require_once $this->sections[ $tab ]['path']; ?>
		</div>
		<?php
	}

	/**
	 * Count the number of actions left
	 *
	 * @return int
	 */
	private function count_actions() {
		$actions_left = get_option( $this->theme_slug . '_actions_left', array() );

		$i = 0;
		foreach ( $this->actions as $action ) {
			$true = false;

			if ( ! $action['check'] ) {
				$true = true;
			}

			if ( ! empty( $actions_left ) && isset( $actions_left[ $action['id'] ] ) && ! $actions_left[ $action['id'] ] ) {
				$true = false;
			}

			if ( $true ) {
				$i ++;
			}
		}

		return $i;
	}

	/**
	 * Generate url for the backend section tabs
	 *
	 * @param string $id Id of the backend tab.
	 *
	 * @return string
	 */
	private function generate_admin_url( $id = '' ) {
		$url = 'themes.php?page=%1$s-welcome&tab=%2$s';

		return admin_url( sprintf( $url, $this->theme_slug, $id ) );
	}

	/**
	 * Generate default sections, with exclusion
	 *
	 * @param array $config Configuration array.
	 *
	 * @return array
	 */
	private function set_default_sections( $config = array() ) {
		$arr = array(
			'getting-started'     => array(
				'id'    => 'getting-started',
				'url'   => $this->generate_admin_url( 'getting-started' ),
				'label' => __( 'Getting Started', 'epsilon-framework' ),
				'path'  => get_template_directory() . '/inc/libraries/welcome-screen/sections/getting-started.php',
			),
			'recommended-actions' => array(
				'id'    => 'recommended-actions',
				'url'   => $this->generate_admin_url( 'recommended-actions' ),
				'label' => __( 'Recommended Actions', 'epsilon-framework' ),
				'path'  => get_template_directory() . '/inc/libraries/welcome-screen/sections/recommended-actions.php',
			),
			'recommended-plugins' => array(
				'id'    => 'recommended-plugins',
				'url'   => $this->generate_admin_url( 'recommended-plugins' ),
				'label' => __( 'Recommended Plugins', 'epsilon-framework' ),
				'path'  => get_template_directory() . '/inc/libraries/welcome-screen/sections/recommended-plugins.php',
			),
			'support'             => array(
				'id'    => 'support',
				'url'   => $this->generate_admin_url( 'support' ),
				'label' => __( 'Support', 'epsilon-framework' ),
				'path'  => get_template_directory() . '/inc/libraries/welcome-screen/sections/support.php',
			),
			'registration'        => array(
				'id'    => 'registration',
				'url'   => $this->generate_admin_url( 'registration' ),
				'label' => __( 'Registration', 'epsilon-framework' ),
				'path'  => get_template_directory() . '/inc/libraries/welcome-screen/sections/registration.php',
			),
		);

		if ( 0 === count( $this->plugins ) ) {
			unset( $arr['recommended-plugins'] );
		}

		if ( 0 < $this->actions_count ) {
			$arr['recommended-actions']['label'] .= '<span class="badge-action-count">' . $this->actions_count . '</span>';
		}

		if ( ! $this->edd ) {
			unset( $arr['registration'] );
		}

		if ( isset( $config['sections_exclude'] ) && ! empty( $config['sections_exclude'] ) ) {
			foreach ( $config['sections_exclude'] as $id ) {
				unset( $arr[ $id ] );
			}
		}

		if ( isset( $config['sections_include'] ) && ! empty( $config['sections_include'] ) ) {
			foreach ( $config['sections_include'] as $id => $props ) {
				$arr[ $id ] = $props;
			}
		}

		return $arr;
	}

	/**
	 * Will return an array with everything that we need to render the action info
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @returns array
	 */
	private function check_plugin( $slug = '' ) {
		$arr = array(
			'installed' => Epsilon_Notify_System::check_plugin_is_installed( $slug ),
			'active'    => Epsilon_Notify_System::check_plugin_is_active( $slug ),
			'needs'     => 'install',
			'class'     => 'install-now button',
			'label'     => __( 'Install and Activate', 'epsilon-framework' ),
		);

		if ( in_array( $slug, array( 'contact-form-7' ) ) ) {
			switch ( $slug ) {
				case 'contact-form-7':
					if ( file_exists( ABSPATH . 'wp-content/plugins/contact-form-7' ) ) {
						$arr['installed'] = true;
						$arr['active']    = defined( 'WPCF7_VERSION' );
					}
					break;
				default:
					$arr['installed'] = false;
					$arr['active']    = false;
					break;
			}
		}

		if ( $arr['installed'] ) {
			$arr['needs'] = 'activate';
			$arr['class'] = 'activate-now button button-primary';
			$arr['label'] = __( 'Activate now', 'epsilon-framework' );
		}

		if ( $arr['active'] ) {
			$arr['needs'] = 'deactivate';
			$arr['class'] = 'deactivate-now button button-primary';
			$arr['label'] = __( 'Deactivate now', 'epsilon-framework' );
		}

		$arr['url'] = $this->create_plugin_link( $arr['needs'], $slug );

		return $arr;
	}

	/**
	 * Creates a link to install/activate/deactivate certain plugins
	 *
	 * @param string $state Plugin state (active,not installed).
	 * @param string $slug  Plugin slug.
	 *
	 * @return string
	 */
	private function create_plugin_link( $state, $slug ) {
		$string = '';

		switch ( $state ) {
			case 'install':
				$string = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => Epsilon_Notify_System::_get_plugin_basename_from_slug( $slug ),
						),
						network_admin_url( 'update.php' )
					),
					'install-plugin_' . $slug
				);
				break;
			case 'deactivate':
				$string = add_query_arg(
					array(
						'action'        => 'deactivate',
						'plugin'        => rawurlencode( Epsilon_Notify_System::_get_plugin_basename_from_slug( $slug ) ),
						'plugin_status' => 'all',
						'paged'         => '1',
						'_wpnonce'      => wp_create_nonce( 'deactivate-plugin_' . Epsilon_Notify_System::_get_plugin_basename_from_slug( $slug ) ),
					),
					admin_url( 'plugins.php' )
				);
				break;
			case 'activate':
				$string = add_query_arg(
					array(
						'action'        => 'activate',
						'plugin'        => rawurlencode( Epsilon_Notify_System::_get_plugin_basename_from_slug( $slug ) ),
						'plugin_status' => 'all',
						'paged'         => '1',
						'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . Epsilon_Notify_System::_get_plugin_basename_from_slug( $slug ) ),
					),
					admin_url( 'plugins.php' )
				);
				break;
			default:
				$string = '';
				break;
		}// End switch().

		return $string;
	}

	/**
	 * Return information of a plugin
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return array
	 */
	private function get_plugin_information( $slug = '' ) {
		$arr = array(
			'info' => $this->call_plugin_api( $slug ),
		);

		$arr['icon'] = $this->check_for_icon( $arr['info']->icons );
		$merge       = $this->check_plugin( $slug );

		$arr = array_merge( $arr, $merge );

		return $arr;
	}

	/**
	 * Get information about a plugin
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return array|mixed|object|WP_Error
	 */
	private function call_plugin_api( $slug = '' ) {
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		$call_api = get_transient( $this->theme_slug . '_plugin_information_transient_' . $slug );

		if ( false === $call_api ) {
			$call_api = plugins_api(
				'plugin_information', array(
					'slug'   => $slug,
					'fields' => array(
						'downloaded'        => false,
						'rating'            => false,
						'description'       => false,
						'short_description' => true,
						'donate_link'       => false,
						'tags'              => false,
						'sections'          => true,
						'homepage'          => true,
						'added'             => false,
						'last_updated'      => false,
						'compatibility'     => false,
						'tested'            => false,
						'requires'          => false,
						'downloadlink'      => false,
						'icons'             => true,
					),
				)
			);
			set_transient( $this->theme_slug . '_plugin_information_transient_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
		}

		return $call_api;
	}

	/**
	 * Searches icons for the plugin
	 *
	 * @param object $object Icon object.
	 *
	 * @return string;
	 */
	private function check_for_icon( $object ) {
		if ( ! empty( $object['svg'] ) ) {
			$plugin_icon_url = $object['svg'];
		} elseif ( ! empty( $object['2x'] ) ) {
			$plugin_icon_url = $object['2x'];
		} elseif ( ! empty( $object['1x'] ) ) {
			$plugin_icon_url = $object['1x'];
		} else {
			$plugin_icon_url = $object['default'];
		}

		return $plugin_icon_url;
	}

	/**
	 * Handle a required action
	 *
	 * @param array $args Argument array.
	 *
	 * @return string;
	 */
	public static function handle_required_action( $args = array() ) {
		$instance     = self::get_instance();
		$actions_left = get_option( $instance->theme_slug . '_actions_left', array() );

		$actions_left[ $args['id'] ] = 'hidden' === $args['do'];

		update_option( $instance->theme_slug . '_actions_left', $actions_left );

		return 'ok';
	}

	/**
	 * Set a frontpage to static
	 *
	 * @param array $args Argument array.
	 *
	 * @return string;
	 */
	public static function set_frontpage_to_static( $args = array() ) {
		$home = get_page_by_title( 'Homepage' );
		$blog = get_page_by_title( 'Blog' );

		if ( null === $home ) {
			$id = wp_insert_post(
				array(
					'post_title'  => __( 'Homepage', 'epsilon-framework' ),
					'post_type'   => 'page',
					'post_status' => 'publish',
				)
			);

			update_option( 'page_on_front', $id );
			update_option( 'show_on_front', 'page' );
		}

		if ( null === $blog ) {
			$id = wp_insert_post(
				array(
					'post_title'  => __( 'Blog', 'epsilon-framework' ),
					'post_type'   => 'page',
					'post_status' => 'publish',
				)
			);

			update_option( 'page_for_posts', $id );
		}

		return 'ok';
	}
}
