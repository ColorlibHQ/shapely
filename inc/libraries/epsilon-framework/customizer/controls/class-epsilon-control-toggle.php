<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Epsilon_Control_Toggle extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-toggle';

	/**
	 * Epsilon_Control_Toggle constructor.
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Toggle' );
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

		return $json;
	}

	/**
	 * Empty, as it should.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_content() {}

	/**
	 * @since  1.2.0
	 * @access public
	 */
	public function content_template() {
		//@formatter:off
		?>
		<div class="checkbox_switch">
			<span class="customize-control-title onoffswitch_label">
				{{{ data.label }}}
				<# if( data.description ){ #>
					<i class="dashicons dashicons-editor-help" style="vertical-align: text-bottom; position: relative;">
						<span class="mte-tooltip">
							{{{ data.description }}}
						</span>
					</i>
				<# } #>
			</span>
			<div class="onoffswitch">
				<input type="checkbox" id="{{{ data.id }}}" name="{{{ data.id }}}" class="onoffswitch-checkbox" value="{{{ data.value }}}" {{{ data.link }}} <# if( data.value ) { #> checked="checked" <# } #> >
				<label class="onoffswitch-label" for="{{{ data.id }}}"></label>
			</div>
		</div>
		<?php
		//@formatter:on
	}

}

