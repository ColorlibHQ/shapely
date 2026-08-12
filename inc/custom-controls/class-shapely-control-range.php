<?php
/**
 * Range Control for the Customizer
 *
 * A range slider that shows its current value. Core's plain 'range' type
 * renders the input with no numeric feedback, which is unhelpful for a setting
 * like header opacity where the number is the whole point.
 *
 * Replaces Epsilon_Control_Slider.
 *
 * @package Shapely
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Shapely_Control_Range' ) ) {
	/**
	 * Range slider with a live value readout.
	 */
	class Shapely_Control_Range extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'shapely-range';

		/**
		 * Render the control.
		 */
		public function render_content() {
			$input_id   = '_customize-input-' . $this->id;
			$output_id  = $input_id . '-value';
			$attrs      = wp_parse_args(
				$this->input_attrs,
				array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				)
			);
			$describedby = '';

			if ( ! empty( $this->description ) ) {
				$describedby = '_customize-description-' . $this->id;
			}
			?>
			<label for="<?php echo esc_attr( $input_id ); ?>">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
			</label>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span id="<?php echo esc_attr( $describedby ); ?>" class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<span class="shapely-range">
				<input
					type="range"
					id="<?php echo esc_attr( $input_id ); ?>"
					<?php if ( $describedby ) : ?>aria-describedby="<?php echo esc_attr( $describedby ); ?>"<?php endif; ?>
					min="<?php echo esc_attr( $attrs['min'] ); ?>"
					max="<?php echo esc_attr( $attrs['max'] ); ?>"
					step="<?php echo esc_attr( $attrs['step'] ); ?>"
					value="<?php echo esc_attr( $this->value() ); ?>"
					oninput="document.getElementById('<?php echo esc_js( $output_id ); ?>').value = this.value"
					<?php $this->link(); ?>
				/>
				<output
					id="<?php echo esc_attr( $output_id ); ?>"
					for="<?php echo esc_attr( $input_id ); ?>"
					class="shapely-range__value"><?php echo esc_html( $this->value() ); ?></output>
			</span>
			<?php
		}
	}
}
