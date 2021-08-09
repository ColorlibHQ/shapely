<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once dirname( __FILE__ ) . '/class-epsilon-autoloader.php';

/**
 * Class Epsilon_Framework
 */
class Epsilon_Framework {
	/**
	 * By default, it loads all controls
	 *
	 * @var array|mixed
	 */
	private $controls = array(
		'toggle',
		'typography',
		'slider',
		'repeater',
		'section-repeater',
		'image',
		'text-editor',
		'icon-picker',
		'customizer-navigation',
		'color-scheme',
		'button-group',
		'separator',
	);
	/**
	 * By default, it loads all sections
	 *
	 * @var array|mixed
	 */
	private $sections = array(
		'recommended-actions',
		'pro',
		'doubled',
	);
	/**
	 * By default, load all panels
	 *
	 * @var array
	 */
	private $panels = array(
		'regular',
	);
	/**
	 * @var bool
	 */
	private $plugin = false;
	/**
	 * If we're in a plugin, set up uri manually
	 *
	 * @var string
	 */
	private $plugin_uri = '';
	/**
	 * If we're in a plugin, set up paths manually
	 *
	 * @var string
	 */
	private $plugin_path = '';
	/**
	 * Default path is in /inc/libraries
	 *
	 * @var mixed|string
	 */
	private $path = '/inc/libraries';
	/**
	 * At the current moment, backup is a must
	 *
	 * @var bool
	 */
	private $backup = true;

	/**
	 * Epsilon_Framework constructor.
	 *
	 * @param $args array
	 */
	public function __construct( $args = array() ) {
		foreach ( $args as $k => $v ) {

			if ( ! in_array(
				$k,
				array(
					'controls',
					'sections',
					'panels',
					'plugin',
					'path',
					'backup',
					'plugin_uri',
					'plugin_dir',
				)
			)
			) {
				continue;
			}

			$this->$k = $v;
		}

		if ( $this->backup ) {
			/**
			 * Let's initiate a backup instance
			 */
			$backup = Epsilon_Content_Backup::get_instance();
		}

		/**
		 * Define Framework uri and paths
		 */
		$this->define_paths();

		/**
		 * Enqueue scripts and styles
		 */
		$this->start_enqueues();

		/**
		 * Add quick links
		 */
		$this->add_action_links();

		/**
		 * AJAX handling moved to a different class
		 */
		new Epsilon_Ajax_Controller();
	}

	/**
	 * Init custom controls
	 *
	 * @param object $wp_customize
	 */
	public function init_controls( $wp_customize ) {
		foreach ( $this->controls as $control ) {
			if ( file_exists( EPSILON_PATH . '/customizer/controls/class-epsilon-control-' . $control . '.php' ) ) {
				require_once EPSILON_PATH . '/customizer/controls/class-epsilon-control-' . $control . '.php';
			}
			if ( file_exists( EPSILON_PATH . '/customizer/settings/class-epsilon-setting-' . $control . '.php' ) ) {
				require_once EPSILON_PATH . '/customizer/settings/class-epsilon-setting-' . $control . '.php';
			}
		}

		foreach ( $this->sections as $section ) {
			if ( file_exists( EPSILON_PATH . '/customizer/sections/class-epsilon-section-' . $section . '.php' ) ) {
				require_once EPSILON_PATH . '/customizer/sections/class-epsilon-section-' . $section . '.php';
			}
		}

		foreach ( $this->panels as $panel ) {
			if ( file_exists( EPSILON_PATH . '/customizer/panels/class-epsilon-panel-' . $panel . '.php' ) ) {
				require_once EPSILON_PATH . '/customizer/panels/class-epsilon-panel-' . $panel . '.php';
			}
		}

		/**
		 * Expose Manager to the Epsilon Customizer class.
		 */
		Epsilon_Customizer::get_instance( $wp_customize );
	}

	/**
	 * Add quick links to point in customizer
	 */
	public function add_action_links() {
		add_filter( 'page_row_actions', array( 'Epsilon_Customizer', 'add_action_links' ), 99, 2 );
	}

	/**
	 * Centralize scripts and styles in a function for easier maintenance
	 *
	 * @since 1.1.0
	 */
	public function start_enqueues() {
		/**
		 * Admin enqueues
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		/**
		 * Customizer enqueues & controls
		 */
		add_action( 'customize_register', array( $this, 'init_controls' ), 10 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_enqueue_scripts' ), 25 );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_styles' ), 25 );

		/**
		 * Repeater fields templates
		 */
		add_action( 'customize_controls_print_footer_scripts', array(
			'Epsilon_Repeater_Templates',
			'field_repeater_js_template',
		), 0 );
		add_action( 'customize_controls_print_footer_scripts', array(
			'Epsilon_Repeater_Templates',
			'section_repeater_js_template',
		), 0 );
	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_script( 'epsilon-admin', EPSILON_URI . '/assets/js/epsilon-framework-admin.js', array( 'jquery' ) );
		wp_localize_script( 'epsilon-admin', 'EpsilonWPUrls', array(
			'siteurl'    => get_option( 'siteurl' ),
			'theme'      => get_template_directory_uri(),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'epsilon_nonce' ),
		) );
		wp_enqueue_style( 'epsilon-admin', EPSILON_URI . '/assets/css/style-admin.css' );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function customize_preview_styles() {
		wp_enqueue_style( 'epsilon-styles', EPSILON_URI . '/assets/css/style.css' );
		wp_enqueue_script( 'epsilon-previewer', EPSILON_URI . '/assets/js/epsilon-framework-previewer.js', array(
			'jquery',
			'customize-preview',
		), 2, true );

		wp_localize_script( 'epsilon-previewer', 'EpsilonWPUrls', array(
			'siteurl'    => get_option( 'siteurl' ),
			'theme'      => get_template_directory_uri(),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'epsilon_nonce' ),
		) );
	}

	/*
	 * Our Customizer script
	 *
	 * Dependencies: Customizer Controls script (core)
	 */
	public function customizer_enqueue_scripts() {
		wp_enqueue_script( 'epsilon-object', EPSILON_URI . '/assets/js/epsilon-framework-customizer.js', array(
			'jquery',
			'customize-controls',
		) );

		wp_localize_script( 'epsilon-object', 'EpsilonWPUrls', array(
			'siteurl'    => get_option( 'siteurl' ),
			'theme'      => get_template_directory_uri(),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'epsilon_nonce' ),
		) );

		wp_localize_script( 'epsilon-object', 'EpsilonTranslations', array(
			'remove'     => esc_html__( 'Remove', 'epsilon-framework' ),
			'add'        => esc_html__( 'Add', 'epsilon-framework' ),
			'selectFile' => esc_html__( 'Upload image', 'epsilon-framework' ),
			'row'        => esc_html__( 'Row', 'epsilon-framework' ),
		) );

		wp_enqueue_style( 'font-awesome', EPSILON_URI . '../../assets/css/fontawesome/all.css' );
		wp_enqueue_style( 'epsilon-styles', EPSILON_URI . '/assets/css/style.css' );
	}

	/**
	 * Define epsilon loading paths
	 */
	public function define_paths() {
		$dir_uri = get_template_directory_uri();
		$dir     = get_template_directory();

		if ( $this->plugin ) {
			$dir_uri = $this->plugin_uri;
			$dir     = $this->plugin_path;
		}
		/**
		 * Define URI and PATH for the framework
		 */
		define( 'EPSILON_URI', $dir_uri . $this->path . '/epsilon-framework' );
		define( 'EPSILON_PATH', $dir . $this->path . '/epsilon-framework' );
		define( 'EPSILON_BACKUP', $this->backup );
	}
}
