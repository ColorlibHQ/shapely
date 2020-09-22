<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Upsell
 */
class Epsilon_Control_Upsell extends WP_Customize_Control {

	/**
	 * @var string
	 */
	public $type = 'epsilon-upsell';
	/**
	 * @var string
	 */
	public $button_text = '';
	/**
	 * @var string
	 */
	public $button_url = '#';
	/**
	 * @var string
	 */
	public $second_button_text = '';
	/**
	 * @var string
	 */
	public $second_button_url = '#';
	/**
	 * @var string
	 */
	public $separator = '';
	/**
	 * @var array
	 */
	public $options = array();
	/**
	 * @var array
	 */
	public $requirements = array();
	/**
	 * @var bool|mixed|void
	 */
	public $allowed = true;

	/**
	 * Epsilon_Control_Upsell constructor.
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args ) {
		$this->allowed = apply_filters( 'epsilon_upsell_control_display', true );
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Upsell' );

	}

	/**
	 *
	 */
	public function json() {
		$json = parent::json();
		/**
		 * Provide a fallback for the label
		 */
		$json['label'] = ! empty( $this->label ) ? $this->label : __( 'See what\'s in the PRO version', 'epsilon-framework' );
		/**
		 * Buttons
		 */
		$json['button_text']        = $this->button_text;
		$json['button_url']         = $this->button_url;
		$json['second_button_text'] = $this->second_button_text;
		$json['second_button_url']  = $this->second_button_url;

		/**
		 * Misc
		 */
		$json['separator'] = $this->separator;
		$json['allowed']   = $this->allowed;

		$arr = array();
		$i   = 0;
		foreach ( $this->options as $option ) {
			$arr[ $i ]['option'] = $option;
			$i ++;
		}

		$i = 0;
		foreach ( $this->requirements as $help ) {
			$arr[ $i ]['help'] = $help;
			$i ++;
		}

		$json['options'] = $arr;

		$json['id']    = $this->id;
		$json['link']  = $this->get_link();
		$json['value'] = $this->value();

		return $json;
	}

	/**
	 *
	 */
	public function content_template() {
		//@formatter:off ?>
		<# if ( data.allowed ) { #>
		<div class="epsilon-upsell-label">
			{{{ data.label }}} <i class="dashicons dashicons-arrow-down-alt2"></i>
		</div>
		<div class="epsilon-upsell-container">
			<# if ( data.options ) { #>
				<ul class="epsilon-upsell-options">
					<# _.each(data.options, function( option, index) { #>
						<li><i class="dashicons dashicons-editor-help">
								<span class="mte-tooltip">{{{ option.help }}}</span>
							</i>
							{{ option.option }}
						</li>
						<# }) #>
				</ul>
			<# } #>

			<div class="epsilon-button-group">
				<# if ( data.button_text && data.button_url ) { #>
					<a href="{{ data.button_url }}" class="button" target="_blank">{{
						data.button_text }}</a>
				<# } #>

				<# if ( data.separator ) { #>
					<span class="button-separator">{{ data.separator }}</span>
				<# } #>

				<# if ( data.second_button_text && data.second_button_url ) { #>
					<a href="{{ data.second_button_url }}" class="button button-primary" target="_blank"> {{data.second_button_text }}</a>
				<# } #>
			</div>
		</div>
		<# } #>
	<?php //@formatter:on
	}

	/**
	 * Empty
	 *
	 * @since 1.0.0
	 */
	public function render_content() {

	}
}
