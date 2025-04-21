<?php
/**
 * Custom Label Control for the Customizer
 *
 * @package Shapely
 */

// Only define this class if WP_Customize_Control exists
if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Shapely_Custom_Label' ) ) {
    /**
     * Custom control to display a styled heading in the Customizer
     */
    class Shapely_Custom_Label extends WP_Customize_Control {
        /**
         * Control type
         *
         * @var string
         */
        public $type = 'shapely-custom-label';

        /**
         * Render the control's content
         */
        public function render_content() {
            if ( ! empty( $this->label ) ) {
                echo '<h3 class="shapely-customizer-heading">' . esc_html( $this->label ) . '</h3>';
            }
            if ( ! empty( $this->description ) ) {
                echo '<p class="shapely-customizer-description">' . esc_html( $this->description ) . '</p>';
            }
        }
    }
} 