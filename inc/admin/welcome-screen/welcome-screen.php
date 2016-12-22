<?php

/**
 * Welcome Screen Class
 */
class shapely_Welcome {

	/**
	 * Constructor for the welcome screen
	 */
	public function __construct() {
		/* create dashbord page */
		add_action( 'admin_menu', array( $this, 'shapely_welcome_register_menu' ) );

		/* activation notice */
		add_action( 'load-themes.php', array( $this, 'shapely_activation_admin_notice' ) );

		/* enqueue script and style for welcome screen */
		add_action( 'admin_enqueue_scripts', array( $this, 'shapely_welcome_style_and_scripts' ) );

		/* enqueue script for customizer */
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'shapely_welcome_scripts_for_customizer' ) );

		/* ajax callback for dismissable required actions */
		add_action( 'wp_ajax_shapely_dismiss_required_action', array(
			$this,
			'shapely_dismiss_required_action_callback'
		) );
		add_action( 'wp_ajax_nopriv_shapely_dismiss_required_action', array(
			$this,
			'shapely_dismiss_required_action_callback'
		) );
	}

	/**
	 * Creates the dashboard page
	 *
	 * @see   add_theme_page()
	 * @since 1.8.2.4
	 */
	public function shapely_welcome_register_menu() {
		$action_count = $this->count_actions();
		$title        = $action_count > 0 ? __( 'About Shapely', 'shapely' ) . '<span class="badge-action-count">' . esc_html( $action_count ) . '</span>' : __( 'About Shapely', 'shapely' );

		add_theme_page( 'About shapely', $title, 'edit_theme_options', 'shapely-welcome', array(
			$this,
			'shapely_welcome_screen'
		) );
	}

	/**
	 * Adds an admin notice upon successful activation.
	 *
	 * @since 1.8.2.4
	 */
	public function shapely_activation_admin_notice() {
		global $pagenow;

		if ( is_admin() && ( 'themes.php' == $pagenow ) && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'shapely_welcome_admin_notice' ), 99 );
		}
	}

	/**
	 * Display an admin notice linking to the welcome screen
	 *
	 * @since 1.8.2.4
	 */
	public function shapely_welcome_admin_notice() {
		?>
		<div class="updated notice is-dismissible">
			<p><?php echo sprintf( esc_html__( 'Welcome! Thank you for choosing shapely! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'shapely' ), '<a href="' . esc_url( admin_url( 'themes.php?page=shapely-welcome' ) ) . '">', '</a>' ); ?></p>
			<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=shapely-welcome' ) ); ?>" class="button"
			      style="text-decoration: none;"><?php _e( 'Get started with shapely', 'shapely' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Load welcome screen css and javascript
	 *
	 * @since  1.8.2.4
	 */
	public function shapely_welcome_style_and_scripts( $hook_suffix ) {

		wp_enqueue_style( 'shapely-welcome-screen-css', get_template_directory_uri() . '/inc/admin/welcome-screen/css/welcome.css' );
		wp_enqueue_script( 'shapely-welcome-screen-js', get_template_directory_uri() . '/inc/admin/welcome-screen/js/welcome.js', array( 'jquery' ) );

		wp_localize_script( 'shapely-welcome-screen-js', 'shapelyWelcomeScreenObject', array(
			'nr_actions_required'      => absint( $this->count_actions() ),
			'ajaxurl'                  => esc_url( admin_url( 'admin-ajax.php' ) ),
			'template_directory'       => esc_url( get_template_directory_uri() ),
			'no_required_actions_text' => __( 'Hooray! There are no required actions for you right now.', 'shapely' )
		) );

	}

	/**
	 * Load scripts for customizer page
	 *
	 * @since  1.8.2.4
	 */
	public function shapely_welcome_scripts_for_customizer() {

		wp_enqueue_style( 'shapely-welcome-screen-customizer-css', get_template_directory_uri() . '/inc/admin/welcome-screen/css/welcome_customizer.css' );
		wp_enqueue_script( 'shapely-welcome-screen-customizer-js', get_template_directory_uri() . '/inc/admin/welcome-screen/js/welcome_customizer.js', array( 'jquery' ), '20120206', true );

		wp_localize_script( 'shapely-welcome-screen-customizer-js', 'shapelyWelcomeScreenCustomizerObject', array(
			'nr_actions_required' => absint( $this->count_actions() ),
			'aboutpage'           => esc_url( admin_url( 'themes.php?page=shapely-welcome&tab=recommended_actions' ) ),
			'customizerpage'      => esc_url( admin_url( 'customize.php#recommended_actions' ) ),
			'themeinfo'           => __( 'View Theme Info', 'shapely' ),
		) );
	}

	/**
	 * Dismiss required actions
	 *
	 * @since 1.8.2.4
	 */
	public function shapely_dismiss_required_action_callback() {

		global $shapely_required_actions;

		$action_id = ( isset( $_GET['id'] ) ) ? $_GET['id'] : 0;

		echo esc_html( $action_id ); /* this is needed and it's the id of the dismissable required action */

		if ( ! empty( $action_id ) ):

			/* if the option exists, update the record for the specified id */
			if ( get_option( 'shapely_show_required_actions' ) ):

				$shapely_show_required_actions = get_option( 'shapely_show_required_actions' );

				switch ( $_GET['todo'] ) {
					case 'add';
						$shapely_show_required_actions[ $action_id ] = true;
						break;
					case 'dismiss';
						$shapely_show_required_actions[ $action_id ] = false;
						break;
				}

				update_option( 'shapely_show_required_actions', $shapely_show_required_actions );

			/* create the new option,with false for the specified id */
			else:

				$shapely_show_required_actions_new = array();

				if ( ! empty( $shapely_required_actions ) ):

					foreach ( $shapely_required_actions as $shapely_required_action ):

						if ( $shapely_required_action['id'] == $action_id ):
							$shapely_show_required_actions_new[ $shapely_required_action['id'] ] = false;
						else:
							$shapely_show_required_actions_new[ $shapely_required_action['id'] ] = true;
						endif;

					endforeach;

					update_option( 'shapely_show_required_actions', $shapely_show_required_actions_new );

				endif;

			endif;

		endif;

		die(); // this is required to return a proper result
	}

	/**
	 *
	 */
	public function count_actions() {
		global $shapely_required_actions;

		$shapely_show_required_actions = get_option( 'shapely_show_required_actions' );
		if ( ! $shapely_show_required_actions ) {
			$shapely_show_required_actions = array();
		}

		$i = 0;
		foreach ( $shapely_required_actions as $action ) {
			$true      = false;
			$dismissed = false;

			if ( ! $action['check'] ) {
				$true = true;
			}

			if ( ! empty( $shapely_show_required_actions ) && isset( $shapely_show_required_actions[ $action['id'] ] ) && ! $shapely_show_required_actions[ $action['id'] ] ) {
				$true = false;
			}

			if ( $true ) {
				$i ++;
			}
		}


		return $i;
	}

	public function call_plugin_api( $slug ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		if ( false === ( $call_api = get_transient( 'shapely_plugin_information_transient_' . $slug ) ) ) {
			$call_api = plugins_api( 'plugin_information', array(
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
					'icons'             => true
				)
			) );
			set_transient( 'shapely_plugin_information_transient_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
		}

		return $call_api;
	}

	public function check_active( $slug ) {
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

			$needs = is_plugin_active( $slug . '/' . $slug2 . '.php' ) ? 'deactivate' : 'activate';

			return array( 'status' => is_plugin_active( $slug . '/' . $slug2 . '.php' ), 'needs' => $needs );
		}

		return array( 'status' => false, 'needs' => 'install' );
	}

	public function check_for_icon( $arr ) {
		if ( ! empty( $arr['svg'] ) ) {
			$plugin_icon_url = $arr['svg'];
		} elseif ( ! empty( $arr['2x'] ) ) {
			$plugin_icon_url = $arr['2x'];
		} elseif ( ! empty( $arr['1x'] ) ) {
			$plugin_icon_url = $arr['1x'];
		} else {
			$plugin_icon_url = $arr['default'];
		}

		return $plugin_icon_url;
	}

	public function create_action_link( $state, $slug ) {
		$slug2 = $slug;
		if ( $slug === 'wordpress-seo' ) {
			$slug2 = 'wp-seo';
		}
		switch ( $state ) {
			case 'install':
				return wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => $slug
						),
						network_admin_url( 'update.php' )
					),
					'install-plugin_' . $slug
				);
				break;
			case 'deactivate':
				return add_query_arg( array(
					                      'action'        => 'deactivate',
					                      'plugin'        => rawurlencode( $slug . '/' . $slug2 . '.php' ),
					                      'plugin_status' => 'all',
					                      'paged'         => '1',
					                      '_wpnonce'      => wp_create_nonce( 'deactivate-plugin_' . $slug . '/' . $slug2 . '.php' ),
				                      ), network_admin_url( 'plugins.php' ) );
				break;
			case 'activate':
				return add_query_arg( array(
					                      'action'        => 'activate',
					                      'plugin'        => rawurlencode( $slug . '/' . $slug2 . '.php' ),
					                      'plugin_status' => 'all',
					                      'paged'         => '1',
					                      '_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $slug . '/' . $slug2 . '.php' ),
				                      ), network_admin_url( 'plugins.php' ) );
				break;
		}
	}

	/**
	 * Welcome screen content
	 *
	 * @since 1.8.2.4
	 */
	public function shapely_welcome_screen() {
		require_once( ABSPATH . 'wp-load.php' );
		require_once( ABSPATH . 'wp-admin/admin.php' );
		require_once( ABSPATH . 'wp-admin/admin-header.php' );

		$shapely      = wp_get_theme();
		$active_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : 'getting_started';
		$action_count = $this->count_actions();

		?>

		<div class="wrap about-wrap epsilon-wrap">

			<h1><?php echo esc_html__( 'Welcome to Shapely! - Version ', 'shapely' ) . $shapely['Version']; ?></h1>

			<div
				class="about-text"><?php echo esc_html__( 'Shapely is now installed and ready to use! Get ready to build something beautiful. We hope you enjoy it! We want to make sure you have the best experience using shapely and that is why we gathered here all the necessary information for you. We hope you will enjoy using shapely, as much as we enjoy creating great products.', 'shapely' ); ?></div>

			<div class="wp-badge epsilon-welcome-logo"></div>


			<h2 class="nav-tab-wrapper wp-clearfix">
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=shapely-welcome&tab=getting_started' ) ); ?>"
				   class="nav-tab <?php echo $active_tab == 'getting_started' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Getting Started', 'shapely' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=shapely-welcome&tab=recommended_actions' ) ); ?>"
				   class="nav-tab <?php echo $active_tab == 'recommended_actions' ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Recommended Actions', 'shapely' ); ?>
					<?php echo $action_count > 0 ? '<span class="badge-action-count">' . esc_html( $action_count ) . '</span>' : '' ?></a>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=shapely-welcome&tab=recommended_plugins' ) ); ?>"
				   class="nav-tab <?php echo $active_tab == 'recommended_plugins' ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Recommended Plugins', 'shapely' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=shapely-welcome&tab=support' ) ); ?>"
				   class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Support', 'shapely' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=shapely-welcome&tab=changelog' ) ); ?>"
				   class="nav-tab <?php echo $active_tab == 'changelog' ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Changelog', 'shapely' ); ?></a>
			</h2>

			<?php
			switch ( $active_tab ) {
				case 'getting_started':
					require_once get_template_directory() . '/inc/admin/welcome-screen/sections/getting-started.php';
					break;
				case 'recommended_actions':
					require_once get_template_directory() . '/inc/admin/welcome-screen/sections/actions-required.php';
					break;
				case 'recommended_plugins':
					require_once get_template_directory() . '/inc/admin/welcome-screen/sections/recommended-plugins.php';
					break;
				case 'support':
					require_once get_template_directory() . '/inc/admin/welcome-screen/sections/support.php';
					break;
				case 'changelog':
					require_once get_template_directory() . '/inc/admin/welcome-screen/sections/changelog.php';
					break;
				default:
					require_once get_template_directory() . '/inc/admin/welcome-screen/sections/getting-started.php';
					break;
			}
			?>


		</div><!--/.wrap.about-wrap-->

		<?php
	}
}

new shapely_Welcome();
