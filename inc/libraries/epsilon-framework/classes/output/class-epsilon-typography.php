<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Typography
 */
class Epsilon_Typography {
	/**
	 * Prefix for our custom control
	 *
	 * @var string
	 */
	protected $prefix;
	/**
	 * If there isn't any inline style, we don't need to generate the CSS
	 *
	 * @var bool
	 */
	protected $terminate = false;
	/**
	 * Options with defaults
	 *
	 * @var array
	 */
	protected $options = array();
	/**
	 * Stores the import url
	 *
	 * @var array
	 */
	protected $font_imports = array();
	/**
	 * Array that defines the controls/settings to be added in the customizer
	 *
	 * @var array
	 */
	protected $customizer_controls = array();
	/**
	 * String, containing the handler of the stylesheet for the inline style
	 *
	 * @var null
	 */
	protected $handler = null;

	/**
	 * Epsilon_Typography constructor.
	 *
	 * @param array $args
	 * @param null  $handler
	 * Description
	 *
	 * Normal usage: Epsilon_Typography::get_instance( array $options )
	 *
	 * During construct, $this->options is being populated with the options
	 * defined as typography. After this, the inline scripts are enqueued.
	 */
	public function __construct( $args = array(), $handler = null ) {
		$this->handler = $handler;
		$this->options = $this->get_option( $args );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		/**
		 * Add the actions for the customizer previewer
		 */
		add_action( 'wp_ajax_epsilon_retrieve_font_weights', array(
			$this,
			'epsilon_retrieve_font_weights',
		) );
		add_action( 'wp_ajax_nopriv_epsilon_retrieve_font_weights', array(
			$this,
			'epsilon_retrieve_font_weights',
		) );
	}

	/**
	 * @param $args
	 *
	 * @return array
	 */
	public function get_option( $args ) {
		$options = array();

		if ( ! empty( $args ) ) {
			foreach ( $args as $option ) {
				$typo = get_theme_mod( $option, '' );
				if ( '' === $typo ) {
					continue;
				}

				$typo         = str_replace( '&quot;', '"', $typo );
				$typo         = (array) json_decode( $typo );
				$typo['json'] = (array) $typo['json'];

				$this->set_font( $typo['json']['font-family'] );

				$options[ $option ] = array_filter( $typo );
			}
		}

		return array_filter( $options );
	}

	/**
	 * Grabs the instance of the epsilon typography class
	 *
	 * @param null $args
	 * @param null $handler
	 *
	 * @return Epsilon_Typography
	 */
	public static function get_instance( $args = null, $handler = null ) {
		static $inst;

		if ( ! $inst ) {
			$inst = new Epsilon_Typography( $args, $handler );
		}

		return $inst;
	}


	/**
	 * Access the GFonts Json and parse its content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array|mixed|object
	 */
	public function google_fonts( $font = null ) {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$path   = EPSILON_PATH . '/assets/data/gfonts.json';
		$gfonts = $wp_filesystem->get_contents( $path );
		$gfonts = json_decode( $gfonts );

		/**
		 * If it's not a valid json ( json_decode returns null if invalid ), we terminate here.
		 */
		if ( null === $gfonts ) {
			return new stdClass();
		}

		if ( empty( $font ) ) {
			return $gfonts;
		}

		return $gfonts->$font;
	}

	/**
	 * @param $args
	 *
	 * @return bool
	 */
	public function set_font( $args ) {
		if ( is_array( $args ) ) {
			$args = $args['font-family'];
		}

		$defaults = array( 'Select font', 'Theme default', 'default_font' );
		if ( in_array( $args, $defaults ) ) {
			return false;
		}

		$font = $this->google_fonts( $args );

		if ( in_array( $args, $defaults ) ) {
			$this->font_imports = false;
		}

		$this->font_imports[] = $font->import;

		return true;
	}

	/**
	 * Return the css string for the live website
	 *
	 * @return string
	 */
	public function generate_css( $options ) {
		$css        = '';
		$properties = '';
		$defaults   = array( 'Select font', 'Theme default', 'initial', 'default_font' );
		foreach ( $options['json'] as $property => $value ) {
			$extra = '';

			if ( in_array( $value, $defaults ) || empty( $value ) ) {
				continue;
			}

			if ( 'font-size' === $property || 'line-height' === $property || 'letter-spacing' === $property ) {
				$extra = 'px';
			}

			switch ( $property ) {
				case 'font-size':
				case 'line-height':
				case 'letter-spacing':
					$properties .= $property . ':' . $value . $extra . ';' . "\n";
					break;
				case 'font-weight':
					if ( 'on' === $value ) {
						$properties .= $property . ': bold;' . "\n";
					}
					break;
				case 'font-style':
					if ( 'on' === $value ) {
						$properties .= $property . ': italic;' . "\n";
					}
					break;
				default :
					$properties .= $property . ':' . $value . ';' . "\n";
					break;
			}
		}

		if ( ! empty( $properties ) ) {
			$css .= $options['selectors'] . '{' . "\n";
			$css .= $properties;
			$css .= '}' . "\n";
		}

		return $css;
	}

	/**
	 * Enqueue the inline style CSS string
	 */
	public function enqueue() {
		$css   = '';
		$fonts = '';

		foreach ( $this->options as $k => $v ) {
			$css .= $this->generate_css( $v );
		}

		if ( '' !== $css ) {
			$this->font_imports = array_unique( $this->font_imports );

			$i = 0;
			foreach ( $this->font_imports as $font ) {
				if ( null !== $font ) {
					wp_enqueue_style( 'epsilon-google-fonts-' . $i, '//fonts.googleapis.com/css?family=' . $font, array(), false, 'all' );
				}
				$i ++;
			}
		}

		$css = $fonts . "\n" . $css;

		wp_add_inline_style( $this->handler, $css );
	}

	/**
	 * Generate typography CSS
	 */
	public static function epsilon_generate_typography_css( $params ) {
		$response = array(
			'status'     => true,
			'message'    => 'ok',
			'stylesheet' => '',
		);

		$typography = Epsilon_Typography::get_instance();
		$typography->set_font( $params['json'] );

		/**
		 * Echo the css inline sheet
		 */
		$response['css']        = $typography->generate_css( $params );
		$response['fonts']      = array_filter( $typography->font_imports );
		$response['stylesheet'] = $params['id'];

		return $response;
	}
}
