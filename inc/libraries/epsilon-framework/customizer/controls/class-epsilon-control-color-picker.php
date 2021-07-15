<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Color_Picker
 */
class Epsilon_Control_Color_Picker extends WP_Customize_Control {
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'epsilon-color-picker';
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $default = '';
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $mode = '';
	/**
	 * @since 1.3.4
	 * @var bool
	 */
	public $lite = false;

	/**
	 * Epsilon_Control_Color_Picker constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Color_Picker' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		$json['id']      = $this->id;
		$json['link']    = $this->get_link();
		$json['value']   = $this->value();
		$json['default'] = $this->setting->default;
		$json['mode']    = '' !== $this->mode ? $this->mode : 'hex';
		$json['lite']    = $this->lite;

		return $json;
	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'minicolors', EPSILON_URI . '/assets/vendors/minicolors/jquery.minicolors.css' );
		wp_enqueue_script( 'minicolors', EPSILON_URI . '/assets/vendors/minicolors/jquery.minicolors.min.js', array( 'jquery' ), '1.2.0', true );
	}

	/**
	 * Display the control's content
	 */
	public function content_template() {
		//@formatter:off ?>
		<label <# if( data.lite ) { #>class="lite"<# } #>>
			<input class="epsilon-color-picker" data-attr-mode={{ data.mode }} type="text" maxlength="7" <# if( data.default ){ #>placeholder="{{ data.default }}"<# } #> <# if(data.value){ #> value="{{ data.value }}" <# } #> {{{ data.link }}} />
			<span class="customize-control-title epsilon-color-picker-title">
				{{{ data.label }}}
				<# if( data.default ){ #>
				<a href="#" data-default="{{ data.default }}" class="epsilon-color-picker-default"><?php echo esc_html__( '(clear)', 'epsilon-framework' ); ?></a>
				<# } #>

				<# if( data.description ){ #>
					<span class="epsilon-color-picker-description">{{{ data.description }}}</span>
				<# } #>
			</span>
		</label>
	<?php //@formatter:on
	}

	/**
	 * Empty, as it should be
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
	}
}
