<?php
/**
 * Welcome screen.
 *
 * Native replacement for Epsilon_Welcome_Screen. Same page, same tabs, same
 * flows -- 850 lines of vendored framework reduced to what Shapely actually
 * uses, built on core admin APIs.
 *
 * Two contracts are preserved exactly, because other code depends on them:
 *
 *  - The demo import posts to `shapely_companion_import_content` with a
 *    `welcome_nonce`. That handler lives in the Shapely Companion plugin, so
 *    the payload shape cannot change without breaking the plugin.
 *  - The page lives at themes.php?page=shapely-welcome, which is linked from
 *    the customizer and from the admin notice.
 *
 * One thing deliberately not carried over: the old AJAX endpoint took a class
 * name and a method name from POST and called `$class::$method()`. It was
 * whitelisted to a single class, but dispatching arbitrary static calls from
 * request data is not a pattern worth reimplementing. Dismissals now run
 * through one handler that does one thing.
 *
 * @package Shapely
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Shapely_Welcome' ) ) :
	/**
	 * Theme welcome / onboarding screen.
	 */
	class Shapely_Welcome {

		/**
		 * Option storing dismissed action ids.
		 */
		const DISMISSED_OPTION = 'shapely_dismissed_actions';

		/**
		 * Menu slug.
		 */
		const PAGE = 'shapely-welcome';

		/**
		 * Singleton instance.
		 *
		 * @var Shapely_Welcome|null
		 */
		private static $instance = null;

		/**
		 * Recommended actions, supplied by Shapely.
		 *
		 * @var array
		 */
		private $actions = array();

		/**
		 * Recommended plugins, supplied by Shapely.
		 *
		 * @var array
		 */
		private $plugins = array();

		/**
		 * Get the instance.
		 *
		 * @param array $config Actions and plugins.
		 * @return Shapely_Welcome
		 */
		public static function get_instance( $config = array() ) {
			if ( null === self::$instance ) {
				self::$instance = new self( $config );
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param array $config Actions and plugins.
		 */
		private function __construct( $config = array() ) {
			$this->actions = isset( $config['actions'] ) ? (array) $config['actions'] : array();
			$this->plugins = isset( $config['plugins'] ) ? (array) $config['plugins'] : array();

			add_action( 'admin_menu', array( $this, 'register_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'wp_ajax_shapely_dismiss_action', array( $this, 'ajax_dismiss_action' ) );
			add_action( 'wp_ajax_shapely_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
		}

		/**
		 * Tabs on the welcome screen.
		 *
		 * @return array
		 */
		private function tabs() {
			return array(
				'getting-started'      => esc_html__( 'Getting Started', 'shapely' ),
				'recommended-actions'  => esc_html__( 'Recommended Actions', 'shapely' ),
				'recommended-plugins'  => esc_html__( 'Recommended Plugins', 'shapely' ),
				'support'              => esc_html__( 'Support', 'shapely' ),
			);
		}

		/**
		 * Register the page under Appearance.
		 */
		public function register_page() {
			add_theme_page(
				esc_html__( 'Shapely', 'shapely' ),
				esc_html__( 'About Shapely', 'shapely' ),
				'edit_theme_options',
				self::PAGE,
				array( $this, 'render' )
			);
		}

		/**
		 * Is the current screen our welcome page?
		 *
		 * @return bool
		 */
		private function is_welcome_screen() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

			return $screen && false !== strpos( (string) $screen->id, self::PAGE );
		}

		/**
		 * Assets.
		 *
		 * @param string $hook Current admin page.
		 */
		public function enqueue( $hook ) {
			$on_screen = $this->is_welcome_screen();

			// The dismissible notice appears on every admin page, so its script
			// has to load there too -- but nothing else does.
			if ( ! $on_screen && ! $this->should_show_notice() ) {
				return;
			}

			$uri = get_template_directory_uri();

			if ( $on_screen ) {
				wp_enqueue_style( 'shapely-welcome', $uri . '/assets/css/welcome.css', array(), SHAPELY_VERSION );
				// Core's plugin installer, for the one-click install buttons.
				wp_enqueue_script( 'plugin-install' );
				wp_enqueue_script( 'updates' );
				add_thickbox();
			}

			wp_enqueue_script( 'shapely-welcome', $uri . '/assets/js/welcome.js', array( 'jquery' ), SHAPELY_VERSION, true );

			wp_localize_script(
				'shapely-welcome',
				'shapelyWelcome',
				array(
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					/*
					 * welcome_nonce is the nonce the Shapely Companion plugin
					 * verifies for the import. Renaming it would break the
					 * import against every released version of the plugin.
					 */
					'importNonce'  => wp_create_nonce( 'welcome_nonce' ),
					'dismissNonce' => wp_create_nonce( 'shapely_dismiss' ),
					'strings'      => array(
						'imported'  => esc_html__( 'Demo content was imported successfully.', 'shapely' ),
						'importing' => esc_html__( 'Importing&hellip;', 'shapely' ),
						'failed'    => esc_html__( 'There was an error importing the demo content.', 'shapely' ),
					),
				)
			);
		}

		/**
		 * Actions the user has not yet dismissed or completed.
		 *
		 * @return array
		 */
		private function outstanding_actions() {
			$dismissed = (array) get_option( self::DISMISSED_OPTION, array() );
			$out       = array();

			foreach ( $this->actions as $action ) {
				if ( empty( $action['id'] ) ) {
					continue;
				}
				if ( in_array( $action['id'], $dismissed, true ) ) {
					continue;
				}
				if ( ! empty( $action['check'] ) ) {
					continue;
				}
				$out[] = $action;
			}

			return $out;
		}

		/**
		 * Should the "get started" notice show?
		 *
		 * @return bool
		 */
		private function should_show_notice() {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return false;
			}
			if ( get_option( 'shapely_welcome_notice_dismissed' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * One-time admin notice pointing at the welcome screen.
		 */
		public function admin_notice() {
			if ( ! $this->should_show_notice() || $this->is_welcome_screen() ) {
				return;
			}
			?>
			<div class="notice notice-info is-dismissible shapely-welcome-notice">
				<p>
					<?php
					printf(
						/* translators: %s: link to the welcome screen. */
						esc_html__( 'Thanks for installing Shapely. %s to import the demo content and finish setting up.', 'shapely' ),
						'<a href="' . esc_url( admin_url( 'themes.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Open the setup page', 'shapely' ) . '</a>'
					);
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Dismiss a single recommended action.
		 */
		public function ajax_dismiss_action() {
			check_ajax_referer( 'shapely_dismiss', 'nonce' );

			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Not allowed', 'shapely' ) ), 403 );
			}

			$id = isset( $_POST['id'] ) ? sanitize_key( wp_unslash( $_POST['id'] ) ) : '';

			if ( '' === $id ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Missing action id', 'shapely' ) ), 400 );
			}

			$dismissed = (array) get_option( self::DISMISSED_OPTION, array() );

			if ( ! in_array( $id, $dismissed, true ) ) {
				$dismissed[] = $id;
				update_option( self::DISMISSED_OPTION, $dismissed, false );
			}

			wp_send_json_success( array( 'id' => $id ) );
		}

		/**
		 * Dismiss the admin notice.
		 */
		public function ajax_dismiss_notice() {
			check_ajax_referer( 'shapely_dismiss', 'nonce' );

			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Not allowed', 'shapely' ) ), 403 );
			}

			update_option( 'shapely_welcome_notice_dismissed', 1, false );

			wp_send_json_success();
		}

		/**
		 * URL for a tab.
		 *
		 * @param string $tab Tab slug.
		 * @return string
		 */
		private function tab_url( $tab ) {
			return admin_url( 'themes.php?page=' . self::PAGE . '&tab=' . rawurlencode( $tab ) );
		}

		/**
		 * Render the page.
		 */
		public function render() {
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( esc_html__( 'You do not have permission to view this page.', 'shapely' ) );
			}

			$tabs = $this->tabs();
			// Read-only tab selection; nonce would be meaningless here.
			$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'getting-started'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! isset( $tabs[ $tab ] ) ) {
				$tab = 'getting-started';
			}

			$theme = wp_get_theme( get_template() );
			?>
			<div class="wrap shapely-welcome">

				<h1 class="shapely-welcome__title">
					<?php
					printf(
						/* translators: %s: theme version. */
						esc_html__( 'Shapely %s', 'shapely' ),
						esc_html( (string) $theme->get( 'Version' ) )
					);
					?>
				</h1>
				<p class="shapely-welcome__intro"><?php esc_html_e( 'A one-page WordPress theme by Colorlib. Everything below is optional — the theme works without any of it.', 'shapely' ); ?></p>

				<nav class="nav-tab-wrapper shapely-welcome__tabs">
					<?php foreach ( $tabs as $slug => $label ) : ?>
						<a href="<?php echo esc_url( $this->tab_url( $slug ) ); ?>"
							class="nav-tab <?php echo $slug === $tab ? 'nav-tab-active' : ''; ?>">
							<?php echo esc_html( $label ); ?>
							<?php
							if ( 'recommended-actions' === $slug ) {
								$count = count( $this->outstanding_actions() );
								if ( $count ) {
									echo ' <span class="shapely-welcome__count">' . esc_html( (string) $count ) . '</span>';
								}
							}
							?>
						</a>
					<?php endforeach; ?>
				</nav>

				<div class="shapely-welcome__body">
					<?php
					switch ( $tab ) {
						case 'recommended-actions':
							$this->render_actions();
							break;
						case 'recommended-plugins':
							$this->render_plugins();
							break;
						case 'support':
							$this->render_support();
							break;
						default:
							$this->render_getting_started();
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Getting started tab.
		 */
		private function render_getting_started() {
			$outstanding = count( $this->outstanding_actions() );
			?>
			<div class="shapely-cards">
				<div class="shapely-card">
					<h3><?php esc_html_e( 'Recommended actions', 'shapely' ); ?></h3>
					<?php if ( 0 === $outstanding ) : ?>
						<p><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Nothing left to do here.', 'shapely' ); ?></p>
					<?php else : ?>
						<p>
							<span class="dashicons dashicons-no-alt"></span>
							<a href="<?php echo esc_url( $this->tab_url( 'recommended-actions' ) ); ?>">
								<?php
								printf(
									/* translators: %d: number of outstanding actions. */
									esc_html( _n( '%d action left', '%d actions left', $outstanding, 'shapely' ) ),
									(int) $outstanding
								);
								?>
							</a>
						</p>
					<?php endif; ?>
				</div>

				<div class="shapely-card">
					<h3><?php esc_html_e( 'Documentation', 'shapely' ); ?></h3>
					<p><?php esc_html_e( 'How the homepage sections, layouts and customizer options fit together.', 'shapely' ); ?></p>
					<p><a href="https://colorlib.com/wp/support/shapely/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read the documentation', 'shapely' ); ?></a></p>
				</div>

				<div class="shapely-card">
					<h3><?php esc_html_e( 'Customize', 'shapely' ); ?></h3>
					<p><?php esc_html_e( 'Colours, layout, header and footer all live in the customizer.', 'shapely' ); ?></p>
					<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open the customizer', 'shapely' ); ?></a></p>
				</div>
			</div>
			<?php
		}

		/**
		 * Recommended actions tab.
		 */
		private function render_actions() {
			$actions = $this->outstanding_actions();

			if ( empty( $actions ) ) {
				echo '<p class="shapely-empty">' . esc_html__( 'No recommended actions left to perform.', 'shapely' ) . '</p>';

				return;
			}

			echo '<ul class="shapely-actions">';

			foreach ( $actions as $action ) {
				$id = isset( $action['id'] ) ? $action['id'] : '';
				?>
				<li class="shapely-action" data-action-id="<?php echo esc_attr( $id ); ?>">
					<button type="button" class="shapely-action__dismiss" aria-label="<?php esc_attr_e( 'Dismiss this action', 'shapely' ); ?>">
						<span class="dashicons dashicons-dismiss" aria-hidden="true"></span>
					</button>

					<h3><?php echo esc_html( isset( $action['title'] ) ? $action['title'] : '' ); ?></h3>

					<?php if ( ! empty( $action['description'] ) ) : ?>
						<p><?php echo esc_html( $action['description'] ); ?></p>
					<?php endif; ?>

					<?php
					if ( ! empty( $action['plugin_slug'] ) ) {
						$this->render_plugin_button( $action['plugin_slug'] );
					}

					if ( ! empty( $action['help'] ) ) {
						/*
						 * Supplied by the theme itself (Shapely::generate_action_html),
						 * not by user input, and contains the import controls.
						 */
						echo wp_kses( $action['help'], $this->allowed_help_html() );
					}
					?>
				</li>
				<?php
			}

			echo '</ul>';
		}

		/**
		 * HTML permitted inside an action's help markup.
		 *
		 * @return array
		 */
		private function allowed_help_html() {
			return array(
				'p'      => array( 'class' => array() ),
				'div'    => array(
					'class' => array(),
					'id'    => array(),
				),
				'h4'     => array(),
				'label'  => array(),
				'a'      => array(
					'class'       => array(),
					'href'        => array(),
					'id'          => array(),
					'data-action' => array(),
					'target'      => array(),
					'rel'         => array(),
				),
				'input'  => array(
					'type'    => array(),
					'name'    => array(),
					'class'   => array(),
					'value'   => array(),
					'checked' => array(),
				),
				'span'   => array( 'class' => array() ),
				'strong' => array(),
			);
		}

		/**
		 * Recommended plugins tab.
		 */
		private function render_plugins() {
			if ( empty( $this->plugins ) ) {
				echo '<p class="shapely-empty">' . esc_html__( 'No recommended plugins.', 'shapely' ) . '</p>';

				return;
			}

			echo '<ul class="shapely-actions">';

			foreach ( $this->plugins as $slug => $plugin ) {
				$slug = is_string( $slug ) ? $slug : ( isset( $plugin['slug'] ) ? $plugin['slug'] : '' );
				$name = isset( $plugin['name'] ) ? $plugin['name'] : $slug;
				?>
				<li class="shapely-action">
					<h3><?php echo esc_html( $name ); ?></h3>
					<?php if ( ! empty( $plugin['description'] ) ) : ?>
						<p><?php echo esc_html( $plugin['description'] ); ?></p>
					<?php endif; ?>
					<?php $this->render_plugin_button( $slug ); ?>
				</li>
				<?php
			}

			echo '</ul>';
		}

		/**
		 * Install / activate button for a wordpress.org plugin.
		 *
		 * Uses core's own installer markup so wp.updates handles the request,
		 * rather than reimplementing plugin installation.
		 *
		 * @param string $slug Plugin directory slug.
		 */
		private function render_plugin_button( $slug ) {
			$slug = sanitize_key( $slug );

			if ( '' === $slug ) {
				return;
			}

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$installed = false;
			$file      = '';

			foreach ( (array) get_plugins() as $plugin_file => $data ) {
				if ( 0 === strpos( $plugin_file, $slug . '/' ) ) {
					$installed = true;
					$file      = $plugin_file;
					break;
				}
			}

			if ( ! $installed ) {
				printf(
					'<a class="button button-primary install-now" data-slug="%1$s" href="%2$s" aria-label="%3$s">%4$s</a>',
					esc_attr( $slug ),
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug ) ),
					esc_attr( sprintf( /* translators: %s: plugin slug. */ __( 'Install %s now', 'shapely' ), $slug ) ),
					esc_html__( 'Install', 'shapely' )
				);

				return;
			}

			if ( is_plugin_active( $file ) ) {
				printf( '<span class="shapely-action__done"><span class="dashicons dashicons-yes"></span> %s</span>', esc_html__( 'Active', 'shapely' ) );

				return;
			}

			printf(
				'<a class="button button-secondary activate-now" data-slug="%1$s" href="%2$s">%3$s</a>',
				esc_attr( $slug ),
				esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ), 'activate-plugin_' . $file ) ),
				esc_html__( 'Activate', 'shapely' )
			);
		}

		/**
		 * Support tab.
		 */
		private function render_support() {
			?>
			<div class="shapely-cards">
				<div class="shapely-card">
					<h3><?php esc_html_e( 'Documentation', 'shapely' ); ?></h3>
					<p><a href="https://colorlib.com/wp/support/shapely/" target="_blank" rel="noopener noreferrer">colorlib.com/wp/support/shapely</a></p>
				</div>
				<div class="shapely-card">
					<h3><?php esc_html_e( 'Support forum', 'shapely' ); ?></h3>
					<p><a href="https://wordpress.org/support/theme/shapely/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Ask a question on WordPress.org', 'shapely' ); ?></a></p>
				</div>
				<div class="shapely-card">
					<h3><?php esc_html_e( 'Report a bug', 'shapely' ); ?></h3>
					<p><a href="https://github.com/ColorlibHQ/shapely/issues" target="_blank" rel="noopener noreferrer">github.com/ColorlibHQ/shapely</a></p>
				</div>
			</div>
			<?php
		}
	}
endif;
