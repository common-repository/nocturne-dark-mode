<?php
/**
 * Happy Addons Compatibility File.
 *
 * @since  1.1
 *
 * @package NocturneDarkMode
 */

use \Elementor\Controls_Manager;
use \Elementor\Plugin;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;

/**
 * Happy Addons Compatibility
 */
if ( ! class_exists( 'Nocturne_Happy_Addons' ) ) :

	/**
	 * Happy Addons Compatibility
	 *
	 * @since 1.0.0
	 */
	class Nocturne_Happy_Addons {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'elementor/element/after_section_end', [ $this, 'add_dark_mode_section_controls' ], 10, 3 );
		}

		/**
		* Add dark mode controls
		*
		* @since 1.0.0
		*
		* @access public
		*/
		public function add_dark_mode_section_controls( $element, $section_id, $args ) {

			// Nav Menu
			if ( 'ha-navigation-menu' === $element->get_name() && '_nav_menu_responsive_style_control' === $section_id ) {
				$element->start_controls_section(
					'nocturne_dark_mode',
					array(
						'tab'   => Controls_Manager::TAB_STYLE,
						'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
					)
				);

				$element->add_control(
					'nocturne_nav_menu_heading',
					[
						'label' => esc_html__( 'Main Menu', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
					]
				);

				$element->start_controls_tabs(
					'nocturne_nav_menu_tabs'
				);

				$element->start_controls_tab(
					'nocturne_nav_menu_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_main_menu_color_dark',
					[
						'label' => esc_html__( 'Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li > a' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.menu li .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
						],
					]
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_nav_menu_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_main_menu_hover_color_dark',
					[
						'label' => esc_html__( 'Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li:hover > a' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.menu > li:hover > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
						],
					]
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_nav_menu_active_tab',
					[
						'label' => esc_html__( 'Active', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_main_menu_active_color_dark',
					[
						'label' => esc_html__( 'Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-nav-menu ul.menu > li.active > a' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-nav-menu ul.menu > li.active > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-nav-menu ul.menu > li.current-menu-ancestor > a' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-nav-menu ul.menu > li.current-menu-ancestor > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
						],
					]
				);

				$element->end_controls_tab();
				$element->end_controls_tabs();

				$element->add_control(
					'nocturne_nav_menu_dropdown_heading',
					[
						'label' => esc_html__( 'Dropdown', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$element->start_controls_tabs(
					'nocturne_dropdown_tabs'
				);

				$element->start_controls_tab(
					'nocturne_dropdown_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
					]
				);

				$element->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'nocturne_nav_submenu_wrap_background_dark',
						'label'    => esc_html__( 'Background', 'nocturne-dark-mode' ),
						'types'    => ['classic', 'gradient'],
						'exclude'  => ['image'],
						'selector' => '.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu',
					]
				);

				$element->add_control(
					'nocturne_dropdown_color_dark',
					[
						'label' => esc_html__( 'Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li > a' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
						],
					]
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_dropdown_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_dropdown_hover_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:hover > a' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:hover > .ha-submenu-indicator-wrap' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'nocturne_nav_submenu_wrap_hover_background_dark',
						'label'    => esc_html__( 'Background', 'nocturne-dark-mode' ),
						'types'    => ['classic', 'gradient'],
						'exclude'  => ['image'],
						'selector' => '.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:hover',
					]
				);

				$element->end_controls_tab();
				$element->end_controls_tabs();

				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'nocturne_nav_submenu_box_shadow_dark',
						'label'    => esc_html__( 'Box Shadow', 'nocturne-dark-mode' ),
						'selector' => '.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu',
					]
				);

				$element->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => 'nocturne_nav_submenu_border_dark',
						'selector' => '.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu',
					]
				);

				$element->add_control(
					'nocturne_dropdown_divider_color_dark',
					[
						'label' => esc_html__( 'Divider Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-menu-wrapper ul.sub-menu > li:not(:last-child)' => 'border-color: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_responsive_nav_heading',
					[
						'label' => esc_html__( 'Responsive Navigation', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$element->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'nocturne_responsive_nav_background_dark',
						'label'    => esc_html__( 'Background', 'nocturne-dark-mode' ),
						'types'    => ['classic', 'gradient'],
						'exclude'  => ['image'],
						'selector' => '.dark {{WRAPPER}} .ha-navigation-burger-menu .ha-menu-toggler',
					]
				);

				$element->add_control(
					'nocturne_responsive_nav_icon_color_dark',
					[
						'label' => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'color: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_responsive_nav_border_color_dark',
					[
						'label' => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-burger-menu .ha-nav-humberger-wrapper .ha-menu-toggler' => 'border-color: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_responsive_nav_separator_color_dark',
					[
						'label' => esc_html__( 'Seperator Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .ha-navigation-burger-menu ul.menu li.menu-item:not(:last-child)' => 'border-bottom-color: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_responsive_nav_item_heading',
					[
						'label' => esc_html__( 'Responsive Navigation Menu Item', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
						// 'separator' => 'before'
					]
				);

				$element->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'nocturne_responsive_nav_item_background_dark',
						'label'    => esc_html__( 'Background', 'nocturne-dark-mode' ),
						'types'    => ['classic', 'gradient'],
						'exclude'  => ['image'],
						'selector' => '.dark {{WRAPPER}} .ha-navigation-burger-menu ul.menu li.menu-item',
					]
				);

				$element->end_controls_section();

			}

			// Contact Form 7
			if ( 'ha-cf7' === $element->get_name() && 'submit' === $section_id ) {

				$element->start_controls_section(
					'nocturne_dark_mode',
					array(
						'tab'   => Controls_Manager::TAB_STYLE,
						'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
					)
				);

				$element->add_control(
					'nocturne_cf7_field_heading',
					[
						'label' => esc_html__( 'Fields', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
					]
				);

				$element->add_control(
					'nocturne_cf7_field_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
              '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit)' => 'color: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_cf7_field_placeholder_color_dark',
					[
						'label' => esc_html__( 'Placeholder Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
               '.dark {{WRAPPER}} ::-webkit-input-placeholder' => 'color: {{VALUE}};',
								'.dark {{WRAPPER}} ::-moz-placeholder' => 'color: {{VALUE}};',
								'.dark {{WRAPPER}} ::-ms-input-placeholder' => 'color: {{VALUE}};',
						]
					]
				);

				$element->start_controls_tabs( 'nocturne_cf7_tabs_field_state' );

        $element->start_controls_tab(
					'nocturne_cf7_tab_field_normal',
					[
						'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
					]
				);

				$element->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'nocturne_cf7_field_border',
						'selector' => '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit)',
					]
        );

				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'nocturne_cf7_field_box_shadow',
						'selector' => '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit)',
					]
        );

				$element->add_control(
					'nocturne_cf7_field_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
               '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit)' => 'background-color: {{VALUE}}',
						]
					]
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_cf7_tab_field_focus',
					[
						'label' => esc_html__( 'Focus', 'nocturne-dark-mode' ),
					]
				);

				$element->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'nocturne_cf7_field_focus_border',
            'selector' => '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):focus',
					]
        );

				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'nocturne_cf7_field_focus_box_shadow',
						'exclude' => [
              'box_shadow_position',
            ],
            'selector' => '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):focus',
					]
        );

				$element->add_control(
					'nocturne_cf7_field_focus_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
               '.dark {{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit):focus' => 'background-color: {{VALUE}}',
						]
					]
				);

				$element->end_controls_tab();
				$element->end_controls_tabs();

				$element->add_control(
					'nocturne_cf7_label_heading',
					[
						'label' => esc_html__( 'Form Labels', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$element->add_control(
					'nocturne_cf7_label_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
               '.dark {{WRAPPER}} label' => 'color: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_cf7_submit_heading',
					[
						'label' => esc_html__( 'Submit Button', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$element->start_controls_tabs( 'nocturne_cf7_tabs_submit' );

        $element->start_controls_tab(
					'nocturne_cf7_tab_submit_normal',
					[
						'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_cf7_submit_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
               '.dark {{WRAPPER}} .wpcf7-submit' => 'color: {{VALUE}};',
						]
					]
				);

				$element->add_control(
					'nocturne_cf7_submit_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
               '.dark {{WRAPPER}} .wpcf7-submit' => 'background-color: {{VALUE}};',
						]
					]
				);

        $element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_cf7_tab_submit_hover',
					[
						'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_cf7_submit_hover_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
              '.dark {{WRAPPER}} .wpcf7-submit:hover, .dark {{WRAPPER}} .wpcf7-submit:focus' => 'color: {{VALUE}};',
						]
					]
				);

				$element->add_control(
					'nocturne_cf7_submit_hover_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
              '.dark {{WRAPPER}} .wpcf7-submit:hover, .dark {{WRAPPER}} .wpcf7-submit:focus' => 'background-color: {{VALUE}};',
						]
					]
				);

				$element->add_control(
					'nocturne_cf7_submit_hover_border_color_dark',
					[
						'label' => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
              '.dark {{WRAPPER}} .wpcf7-submit:hover, .dark {{WRAPPER}} .wpcf7-submit:focus' => 'border-color: {{VALUE}};',
						]
					]
				);

				$element->end_controls_tab();
        $element->end_controls_tabs();

				$element->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'nocturne_cf7_submit_border',
            'selector' => '.dark {{WRAPPER}} .wpcf7-submit',
					]
        );

				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'nocturne_cf7_submit_box_shadow',
            'selector' => '.dark {{WRAPPER}} .wpcf7-submit',
					]
        );

				$element->end_controls_section();

			}
			
		}
	}
	
endif;

/**
 * Kicking this off by calling 'get_instance()' method
 */
Nocturne_Happy_Addons::get_instance();