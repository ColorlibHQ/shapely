<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Epsilon_Control_Separator extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-separator';

	/**
	 * Epsilon_Control_Separator constructor.
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Separator' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		return $json;
	}

	/**
	 * Empty, as it should.
	 *
	 * @access public
	 * @return void
	 */
	public function render_content() {
	}

	/**
	 * Content templte
	 *
	 * @access public
	 */
	public function content_template() {
		//@formatter:off
		?>
		<div class="epsilon-control-container">
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
			<hr />
		</div>
		<?php
		//@formatter:on
	}

}
