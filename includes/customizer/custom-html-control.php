<?php
/**
 * Custom HTML control for customizer.
 * @author  	 DeoThemes
 * @copyright  (c) Copyright by DeoThemes
 * @link       https://deothemes.com
 * @package 	 NocturneDarkMode
 * @since 		 1.0.0
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
	class Nocturne_Custom_HTML_Control extends WP_Customize_Control {
		public $type = 'custom_html';

		public function render_content() {
			if ( isset( $this->label ) ) {
				echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
			}
			if ( isset( $this->description ) ) {
				echo '<span class="customize-control-description">' . esc_html( $this->description ) . '</span>';
			}
			echo '<div>' . wp_kses_post( $this->input_attrs['html'] ) . '</div>';
		}
	}
}