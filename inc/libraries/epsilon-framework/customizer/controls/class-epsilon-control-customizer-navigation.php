<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Color_Picker
 */
class Epsilon_Control_Customizer_Navigation extends WP_Customize_Control {
	/**
	 * Control type
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'epsilon-customizer-navigation';
	/**
	 * Id of the section we`re navigating to
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $navigate_to_id;
	/**
	 * Anchor label
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $navigate_to_label;

	/**
	 * Opens a doubled section
	 *
	 * @since 1.4.0
	 * @var bool
	 */
	public $opens_doubled = false;

	/**
	 * Epsilon_Control_Customizer_Navigation constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Customizer_Navigation' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		$json['id']              = $this->id;
		$json['navigateToId']    = $this->navigate_to_id;
		$json['navigateToLabel'] = $this->navigate_to_label;
		$json['opensDoubled']    = $this->opens_doubled;

		return $json;
	}

	/**
	 * Empty as it should be
	 *
	 * @since 1.0.0
	 */
	public function render_content() {

	}

	/**
	 * Render the content template
	 *
	 * @since 1.0.0
	 */
	public function content_template() {
		//@formatter:off ?>
		{{{ data.label }}}
		<a href="#" class="epsilon-customizer-navigation" data-customizer-section="{{{ data.navigateToId }}}">
			{{{ data.navigateToLabel }}}
		</a>
		<?php //@formatter:on
	}
}
