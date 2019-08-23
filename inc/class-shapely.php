<?php

class Shapely {

	public $recommended_plugins = array(
		'kali-forms'    => array(
			'recommended' => true,
		),
		'colorlib-login-customizer' => array(
			'recommended' => true,
		),
        'colorlib-404-customizer' => array(
            'recommended' => true,
        ),
        'colorlib-coming-soon-maintenance' => array(
            'recommended' => true,
        ),
		'simple-custom-post-order'  => array(
			'recommended' => true,
		),
		'fancybox-for-wordpress'    => array(
			'recommended' => true,
		),
		'modula-best-grid-gallery' => array(
			'recommended' => true,
		),
	);

	public $recommended_actions;

	public $theme_slug = 'shapely';

	function __construct() {

		if ( ! is_admin() && ! is_customize_preview() ) {
			return;
		}

		$this->load_class();

		$this->recommended_actions = apply_filters(
			'shapely_required_actions', array(
				array(
					'id'          => 'shapely-req-import-content',
					'title'       => esc_html__( 'Import Demo Content', 'shapely' ),
					'description' => esc_html__( 'Clicking the button below will install and activate plugins, add widgets and set static front page to your WordPress installation. Click advanced to customize the import process.', 'shapely' ),
					'help'        => $this->generate_action_html(),
					'check'       => Shapely_Notify_System::shapely_has_content(),
				),
				array(
					'id'          => 'shapely-req-ac-install-companion-plugin',
					'title'       => Shapely_Notify_System::shapely_companion_title(),
					'description' => Shapely_Notify_System::shapely_companion_description(),
					'check'       => Shapely_Notify_System::shapely_has_plugin( 'shapely-companion' ),
					'plugin_slug' => 'shapely-companion',
				),
				array(
					'id'          => 'shapely-req-ac-install-wp-jetpack-plugin',
					'title'       => Shapely_Notify_System::shapely_jetpack_title(),
					'description' => Shapely_Notify_System::shapely_jetpack_description(),
					'check'       => Shapely_Notify_System::shapely_has_plugin( 'jetpack' ),
					'plugin_slug' => 'jetpack',
				),
				array(
					'id'          => 'shapely-req-ac-install-kali-forms',
					'title'       => Shapely_Notify_System::shapely_kaliforms_title(),
					'description' => Shapely_Notify_System::shapely_kaliforms_description(),
					'check'       => Shapely_Notify_System::shapely_has_plugin( 'kali-forms' ),
					'plugin_slug' => 'kali-forms',
				),
			)
		);

		$this->init_epsilon();
		$this->init_welcome_screen();

		// Hooks
		add_action( 'customize_register', array( $this, 'init_customizer' ) );

	}

	public function load_class() {

		if ( ! is_admin() && ! is_customize_preview() ) {
			return;
		}

		require_once get_template_directory() . '/inc/libraries/epsilon-framework/class-epsilon-autoloader.php';
		require_once get_template_directory() . '/inc/class-shapely-notify-system.php';
		require_once get_template_directory() . '/inc/libraries/welcome-screen/class-epsilon-welcome-screen.php';

	}

	public function init_epsilon() {

		$args = array(
			'controls' => array( 'slider', 'toggle' ), // array of controls to load
			'sections' => array( 'recommended-actions', 'pro' ), // array of sections to load
			'backup'   => false,
		);

		new Epsilon_Framework( $args );

	}

	public function init_welcome_screen() {

		Epsilon_Welcome_Screen::get_instance(
			$config = array(
				'theme-name' => 'Shapely',
				'theme-slug' => 'shapely',
				'actions'    => $this->recommended_actions,
				'plugins'    => $this->recommended_plugins,
			)
		);

	}

	public function init_customizer( $wp_customize ) {
		$current_theme = wp_get_theme();
		$wp_customize->add_section(
			new Epsilon_Section_Recommended_Actions(
				$wp_customize, 'epsilon_recomended_section', array(
					'title'                        => esc_html__( 'Recomended Actions', 'shapely' ),
					'social_text'                  => esc_html( $current_theme->get( 'Author' ) ) . esc_html__( ' is social :', 'shapely' ),
					'plugin_text'                  => esc_html__( 'Recomended Plugins :', 'shapely' ),
					'actions'                      => $this->recommended_actions,
					'plugins'                      => $this->recommended_plugins,
					'theme_specific_option'        => $this->theme_slug . '_show_required_actions',
					'theme_specific_plugin_option' => $this->theme_slug . '_show_required_plugins',
					'facebook'                     => 'https://www.facebook.com/colorlib',
					'twitter'                      => 'https://twitter.com/colorlib',
					'wp_review'                    => true,
					'priority'                     => 0,
				)
			)
		);

	}

	private function generate_action_html() {

		$import_actions = array(
			'set-frontpage'  => esc_html__( 'Set Static FrontPage', 'shapely' ),
			'import-widgets' => esc_html__( 'Import HomePage Widgets', 'shapely' ),
		);

		$import_plugins = array(
			'shapely-companion' => esc_html__( 'Shapely Companion', 'shapely' ),
			'jetpack'           => esc_html__( 'Jetpack', 'shapely' ),
			'kali-forms'        => esc_html__( 'Kali Forms', 'shapely' ),
		);

		$plugins_html = '';

		if ( is_customize_preview() ) {
			$url  = 'themes.php?page=%1$s-welcome&tab=%2$s';
			$html = '<a class="button button-primary" id="" href="' . esc_url( admin_url( sprintf( $url, 'shapely', 'recommended-actions' ) ) ) . '">' . __( 'Import Demo Content', 'shapely' ) . '</a>';
		} else {
			$html  = '<p><a class="button button-primary cpo-import-button epsilon-ajax-button" data-action="import_demo" id="add_default_sections" href="#">' . __( 'Import Demo Content', 'shapely' ) . '</a>';
			$html .= '<a class="button epsilon-hidden-content-toggler" href="#welcome-hidden-content">' . __( 'Advanced', 'shapely' ) . '</a></p>';
			$html .= '<div class="import-content-container" id="welcome-hidden-content">';

			foreach ( $import_plugins as $id => $label ) {
				if ( ! Shapely_Notify_System::shapely_has_plugin( $id ) ) {
					$plugins_html .= $this->generate_checkbox( $id, $label, 'plugins' );
				}
			}

			if ( '' != $plugins_html ) {
				$html .= '<div class="plugins-container">';
				$html .= '<h4>' . __( 'Plugins', 'shapely' ) . '</h4>';
				$html .= '<div class="checkbox-group">';
				$html .= $plugins_html;
				$html .= '</div>';
				$html .= '</div>';
			}

			$html .= '<div class="demo-content-container">';
			$html .= '<h4>' . __( 'Demo Content', 'shapely' ) . '</h4>';
			$html .= '<div class="checkbox-group">';
			foreach ( $import_actions as $id => $label ) {
				$html .= $this->generate_checkbox( $id, $label );
			}
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;

	}

	private function generate_checkbox( $id, $label, $name = 'options', $block = false ) {
		$string = '<label><input checked type="checkbox" name="%1$s" class="demo-checkboxes"' . ( $block ? ' disabled ' : ' ' ) . 'value="%2$s">%3$s</label>';

		return sprintf( $string, $name, $id, $label );
	}

}

new Shapely();
