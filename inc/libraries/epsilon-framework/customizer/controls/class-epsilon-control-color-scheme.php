<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Color_Scheme
 */
class Epsilon_Control_Color_Scheme extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-color-scheme';

	/**
	 * Choice array
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $choices = array();

	/**
	 * Epsilon_Control_Color_Scheme constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Color_Scheme' );
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
		$json['choices'] = $this->get_choices();

		if ( 'default' === $json['value'] || null === $json['value'] ) {
			$temp          = reset( $json['choices'] );
			$json['value'] = $temp['id'];
		}

		$json['selectedColors'] = $this->get_selected_colors( $json );

		return $json;
	}

	/**
	 * Arrange a new array of options using the values from database
	 *
	 * @since 1.0.0
	 */
	public function get_selected_colors( $json ) {
		$arr = $json['choices'][ $json['value'] ];

		foreach ( $arr['colors'] as $input => $value ) {
			if ( ! get_theme_mod( $input, false ) ) {
				continue;
			}

			$arr['colors'][ $input ] = get_theme_mod( $input );
		}

		return $arr;
	}

	/**
	 * Arrange array so we can handle it easier
	 *
	 * @since 1.0.0
	 */
	public function get_choices() {
		$arr = array();
		foreach ( $this->choices as $index => $choice ) {
			$arr[ $choice['id'] ]                  = $choice;
			$arr[ $choice['id'] ]['encodedColors'] = json_encode( $choice['colors'] );
		}

		return $arr;
	}

	/**
	 * Display the control content
	 *
	 * @since 1.0.0
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
			<div class="customize-control-content">
				<input {{{ data.link }}} class="epsilon-color-scheme-input" id="input_{{ data.id }}" type="hidden" <# if( data.value ) { #> value='{{{ data.value }}}' <# } #> />
			</div>
			<div class="epsilon-color-scheme-selected epsilon-control-set-dropdown">
				<div class="epsilon-control-dropdown">
					<i class="dashicons dashicons-arrow-down"/>
				</div>
				<div class="epsilon-control-styles">
					<# var i = 0; #>
					<# _.each(data.selectedColors.colors, function(v, k) { #>
						<# if ( i < 5 ){ #>
						<a href="#" data-field-id="{{ k }}" style="background-color: {{ v }}"></a>
						<# } #>
					<# i++ #>
					<# }); #>
				</div>
			</div>

			<div id="color_scheme_{{ data.id }}" class="epsilon-color-scheme">
				<# _.each(data.choices, function(el) { #>
				<div class="epsilon-color-scheme-option <# if ( data.value === el.id ) { #> selected <# } #>" data-color-id="{{{ el.id }}}">
					<input type="hidden" value='{{{ el.encodedColors }}}'/>
					<span class="epsilon-color-scheme-name"> {{{ el.name }}} </span>
					<div class="epsilon-control-styles">
						<# var i = 0 #>
						<# _.each(el.colors, function(v, k) { #>
							<# if ( i < 5 ){ #>
							<a href="#" data-field-id="{{ k }}" style="background-color: {{ v }}"></a>
							<# } #>
						<# i++ #>
						<# }); #>
					</div>
				</div>
				<# }); #>
			</div>
		</label>
		<?php //@formatter:on
	}

	/**
	 * Empty, as it should be
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_content() {

	}
}
