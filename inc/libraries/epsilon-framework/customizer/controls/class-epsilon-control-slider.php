<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Slider control
 *
 * @since  1.0.0
 * @access public
 *
 */
class Epsilon_Control_Slider extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-slider';

	/**
	 * Min/Max/Step options
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $choices = array();

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-slider' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		$json['id']             = $this->id;
		$json['link']           = $this->get_link();
		$json['value']          = $this->value();
		$json['sliderControls'] = $this->choices;

		$slider_defaults = array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1,
		);

		$json['sliderControls'] = wp_parse_args( $this->choices, $slider_defaults );

		return $json;
	}

	/**
	 * Epsilon_Control_Slider constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Slider' );
	}

	/**
	 * Displays the control content.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function content_template() {
		//@formatter:off ?>
		<label>
			<span class="customize-control-title">
				{{{ data.label }}}
				<# if( data.description ){ #>
					<i class="dashicons dashicons-editor-help" style="vertical-align: text-bottom; position: relative;">
						<span class="mte-tooltip">
							{{{ data.description }}}
						</span>
					</i>
				<# } #>
			</span>
		</label>
		<div class="slider-container">
			<input disabled type="text" class="rl-slider" id="input_{{ data.id }}" value="{{ data.value }}" {{{ data.link }}}/>
			<div id="slider_{{{ data.id }}}" class="ss-slider"></div>
		</div>
		<?php
		//@formatter:on
	}

	/**
	 * Displays the control content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_content() {

	}
}
