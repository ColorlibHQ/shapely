<?php
/**
 * Link Section for the Customizer
 *
 * A customizer section that is not a section at all: it renders as a titled
 * button that opens an external page, used for the theme documentation entry.
 *
 * Replaces Epsilon_Section_Pro from the vendored framework, which did the same
 * thing behind 135 files of dependencies.
 *
 * @package Shapely
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Section' ) && ! class_exists( 'Shapely_Section_Link' ) ) {
	/**
	 * A customizer section rendered as a single outbound link.
	 */
	class Shapely_Section_Link extends WP_Customize_Section {

		/**
		 * Section type, matched by the JS template below.
		 *
		 * @var string
		 */
		public $type = 'shapely-link';

		/**
		 * Text for the button.
		 *
		 * @var string
		 */
		public $button_text = '';

		/**
		 * Where the button goes.
		 *
		 * @var string
		 */
		public $button_url = '';

		/**
		 * Pass the extra properties through to the JS template.
		 *
		 * @return array
		 */
		public function json() {
			$json = parent::json();

			$json['button_text'] = $this->button_text;
			$json['button_url']  = $this->button_url;

			return $json;
		}

		/**
		 * Underscore template for the section.
		 *
		 * Rendered in place of the usual accordion heading, so the whole row is
		 * the link rather than something that expands to reveal controls.
		 */
		protected function render_template() {
			?>
			<li id="accordion-section-{{ data.id }}" class="shapely-link-section accordion-section control-section control-section-{{ data.type }} cannot-expand">
				<h3 class="shapely-link-section__title">{{ data.title }}</h3>
				<# if ( data.button_text && data.button_url ) { #>
					<a class="button button-secondary shapely-link-section__button"
						href="{{ data.button_url }}"
						target="_blank"
						rel="noopener noreferrer">
						{{ data.button_text }}
					</a>
				<# } #>
			</li>
			<?php
		}
	}
}
