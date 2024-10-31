<?php
/**
 * Header Footer Elementor Compatibility File.
 *
 * @since  1.1.1
 *
 * @package NocturneDarkMode
 */

use \Elementor\Controls_Manager;
use \Elementor\Plugin;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;

/**
 * Header Footer Elementor Compatibility
 */
if ( ! class_exists( 'Nocturne_HFE' ) ) :

	/**
	 * Header Footer Elementor Compatibility
	 *
	 * @since 1.1.1
	 */
	class Nocturne_HFE {

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
			if ( 'navigation-menu' === $element->get_name() && 'style_toggle' === $section_id ) {
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
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .menu-item a.hfe-menu-item, .dark {{WRAPPER}} .sub-menu a.hfe-sub-menu-item' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_control(
					'nocturne_main_menu_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .menu-item a.hfe-menu-item, .dark {{WRAPPER}} .sub-menu, .dark {{WRAPPER}} nav.hfe-dropdown, .dark {{WRAPPER}} .hfe-dropdown-expandible' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'layout!' => 'flyout',
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
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .menu-item a.hfe-menu-item:hover,
								.dark {{WRAPPER}} .sub-menu a.hfe-sub-menu-item:hover,
								.dark {{WRAPPER}} .menu-item.current-menu-item a.hfe-menu-item,
								.dark {{WRAPPER}} .menu-item a.hfe-menu-item.highlighted,
								.dark {{WRAPPER}} .menu-item a.hfe-menu-item:focus' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_control(
					'nocturne_main_menu_hover_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .menu-item a.hfe-menu-item:hover,
							.dark {{WRAPPER}} .sub-menu a.hfe-sub-menu-item:hover,
							.dark {{WRAPPER}} .menu-item.current-menu-item a.hfe-menu-item,
							.dark {{WRAPPER}} .menu-item a.hfe-menu-item.highlighted,
							.dark {{WRAPPER}} .menu-item a.hfe-menu-item:focus' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'layout!' => 'flyout',
						],
					]
				);

				$element->add_control(
					'nocturne_main_menu_hover_pointer_color_dark',
					[
						'label' => esc_html__( 'Link Hover Effect Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
								'.dark {{WRAPPER}} .hfe-nav-menu-layout:not(.hfe-pointer__framed) .menu-item.parent a.hfe-menu-item:before,
								.dark {{WRAPPER}} .hfe-nav-menu-layout:not(.hfe-pointer__framed) .menu-item.parent a.hfe-menu-item:after' => 'background-color: {{VALUE}}',
								'.dark {{WRAPPER}} .hfe-nav-menu-layout:not(.hfe-pointer__framed) .menu-item.parent .sub-menu .hfe-has-submenu-container a:after' => 'background-color: unset',
								'.dark {{WRAPPER}} .hfe-pointer__framed .menu-item.parent a.hfe-menu-item:before,
								.dark {{WRAPPER}} .hfe-pointer__framed .menu-item.parent a.hfe-menu-item:after' => 'border-color: {{VALUE}}',
						],
						'condition' => [
							'pointer!' => [ 'none', 'text' ],
							'layout!'  => 'flyout',
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
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .menu-item.current-menu-item a.hfe-menu-item,
								.dark {{WRAPPER}} .menu-item.current-menu-ancestor a.hfe-menu-item' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_control(
					'nocturne_main_menu_active_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .menu-item.current-menu-item a.hfe-menu-item,
							.dark {{WRAPPER}} .menu-item.current-menu-ancestor a.hfe-menu-item' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'layout!' => 'flyout',
						],
					]
				);

				$element->add_control(
					'nocturne_main_menu_active_pointer_color_dark',
					[
						'label' => esc_html__( 'Link Hover Effect Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .hfe-nav-menu-layout:not(.hfe-pointer__framed) .menu-item.parent.current-menu-item a.hfe-menu-item:before,
								.dark {{WRAPPER}} .hfe-nav-menu-layout:not(.hfe-pointer__framed) .menu-item.parent.current-menu-item a.hfe-menu-item:after' => 'background-color: {{VALUE}}',
								'.dark {{WRAPPER}} .hfe-nav-menu:not(.hfe-pointer__framed) .menu-item.parent .sub-menu .hfe-has-submenu-container a.current-menu-item:after' => 'background-color: unset',
								'.dark {{WRAPPER}} .hfe-pointer__framed .menu-item.parent.current-menu-item a.hfe-menu-item:before,
								.dark {{WRAPPER}} .hfe-pointer__framed .menu-item.parent.current-menu-item a.hfe-menu-item:after' => 'border-color: {{VALUE}}',
						],
						'condition' => [
							'pointer!' => [ 'none', 'text' ],
							'layout!'  => 'flyout',
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

				$element->add_control(
					'nocturne_dropdown_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .sub-menu a.hfe-sub-menu-item,
							.dark {{WRAPPER}} .elementor-menu-toggle,
							.dark {{WRAPPER}} nav.hfe-dropdown li a.hfe-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown li a.hfe-sub-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown-expandible li a.hfe-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown-expandible li a.hfe-sub-menu-item' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_control(
					'nocturne_dropdown_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .sub-menu,
								.dark {{WRAPPER}} nav.hfe-dropdown,
								.dark {{WRAPPER}} nav.hfe-dropdown-expandible,
								.dark {{WRAPPER}} nav.hfe-dropdown .menu-item a.hfe-menu-item,
								.dark {{WRAPPER}} nav.hfe-dropdown .menu-item a.hfe-sub-menu-item' => 'background-color: {{VALUE}}',
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
							'.dark {{WRAPPER}} .sub-menu a.hfe-sub-menu-item:hover,
								.dark {{WRAPPER}} .elementor-menu-toggle:hover,
								.dark {{WRAPPER}} nav.hfe-dropdown li a.hfe-menu-item:hover,
								.dark {{WRAPPER}} nav.hfe-dropdown li a.hfe-sub-menu-item:hover,
								.dark {{WRAPPER}} nav.hfe-dropdown-expandible li a.hfe-menu-item:hover,
								.dark {{WRAPPER}} nav.hfe-dropdown-expandible li a.hfe-sub-menu-item:hover' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_control(
					'nocturne_dropdown_hover_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .sub-menu a.hfe-sub-menu-item:hover,
							.dark {{WRAPPER}} nav.hfe-dropdown li a.hfe-menu-item:hover,
							.dark {{WRAPPER}} nav.hfe-dropdown li a.hfe-sub-menu-item:hover,
							.dark {{WRAPPER}} nav.hfe-dropdown-expandible li a.hfe-menu-item:hover,
							.dark {{WRAPPER}} nav.hfe-dropdown-expandible li a.hfe-sub-menu-item:hover' => 'background-color: {{VALUE}}',
						],
					]
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_dropdown_active_tab',
					[
						'label' => esc_html__( 'Active', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_dropdown_active_color_dark',
					[
						'label' => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .sub-menu .menu-item.current-menu-item a.hfe-sub-menu-item.hfe-sub-menu-item-active,
							.dark {{WRAPPER}} nav.hfe-dropdown .menu-item.current-menu-item a.hfe-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown .menu-item.current-menu-ancestor a.hfe-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown .sub-menu .menu-item.current-menu-item a.hfe-sub-menu-item.hfe-sub-menu-item-active
							' => 'color: {{VALUE}}',
						],
					]
				);

				$element->add_control(
					'nocturne_dropdown_active_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .sub-menu .menu-item.current-menu-item a.hfe-sub-menu-item.hfe-sub-menu-item-active,
							.dark {{WRAPPER}} nav.hfe-dropdown .menu-item.current-menu-item a.hfe-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown .menu-item.current-menu-ancestor a.hfe-menu-item,
							.dark {{WRAPPER}} nav.hfe-dropdown .sub-menu .menu-item.current-menu-item a.hfe-sub-menu-item.hfe-sub-menu-item-active' => 'background-color: {{VALUE}}',
						],
					]
				);

				$element->end_controls_tab();
				
				$element->end_controls_tabs();

				$element->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => 'nocturne_nav_dropdown_border_dark',
						'selector' => '.dark {{WRAPPER}} nav.hfe-nav-menu__layout-horizontal .sub-menu,
							.dark {{WRAPPER}} nav:not(.hfe-nav-menu__layout-horizontal) .sub-menu.sub-menu-open,
							.dark {{WRAPPER}} nav.hfe-dropdown .hfe-nav-menu,
						 	.dark {{WRAPPER}} nav.hfe-dropdown-expandible .hfe-nav-menu',
					]
				);

				$element->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'nocturne_nav_dropdown_box_shadow_dark',
						'label'    => esc_html__( 'Box Shadow', 'nocturne-dark-mode' ),
						'selector'  => '.dark {{WRAPPER}} .hfe-nav-menu .sub-menu,
								.dark {{WRAPPER}} nav.hfe-dropdown,
						 		.dark {{WRAPPER}} nav.hfe-dropdown-expandible',
					]
				);				

				$element->add_control(
					'nocturne_dropdown_divider_color_dark',
					[
						'label' => esc_html__( 'Divider Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .sub-menu li.menu-item:not(:last-child),
							.dark {{WRAPPER}} nav.hfe-dropdown li.menu-item:not(:last-child),
							.dark {{WRAPPER}} nav.hfe-dropdown-expandible li.menu-item:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
						],
						'condition' => [
							'dropdown_divider_border!' => 'none',
						],
					]
				);

				$element->add_control(
					'nocturne_toggle_heading',
					[
						'label' => esc_html__( 'Menu Trigger & Close Icon', 'nocturne-dark-mode' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before'
					]
				);

				$element->start_controls_tabs(
					'nocturne_style_toggle_tabs'
				);

				$element->start_controls_tab(
					'nocturne_toggle_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_toggle_color_dark',
					[
						'label' => esc_html__( 'Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} div.hfe-nav-menu-icon' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} div.hfe-nav-menu-icon svg' => 'fill: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_toggle_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .hfe-nav-menu-icon' => 'background-color: {{VALUE}};',
						]
					]
				);

				$element->end_controls_tab();

				$element->start_controls_tab(
					'nocturne_toggle_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
					]
				);

				$element->add_control(
					'nocturne_toggle_hover_color_dark',
					[
						'label' => esc_html__( 'Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} div.hfe-nav-menu-icon:hover' => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} div.hfe-nav-menu-icon:hover svg' => 'fill: {{VALUE}}',
						]
					]
				);

				$element->add_control(
					'nocturne_toggle_hover_bg_color_dark',
					[
						'label' => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .hfe-nav-menu-icon:hover' => 'background-color: {{VALUE}};',
						]
					]
				);

				$element->end_controls_tab();

				$element->end_controls_tabs();

				$element->add_control(
					'nocturne_toggle_close_color_dark',
					[
						'label' => esc_html__( 'Close Icon Color', 'nocturne-dark-mode' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'.dark {{WRAPPER}} .hfe-flyout-close'     => 'color: {{VALUE}}',
							'.dark {{WRAPPER}} .hfe-flyout-close svg' => 'fill: {{VALUE}}',
						],
						'condition' => [
							'layout' => 'flyout',
						],
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
Nocturne_HFE::get_instance();