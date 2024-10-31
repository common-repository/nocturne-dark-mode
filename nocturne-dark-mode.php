<?php

/**
 * Plugin Name: Nocturne Dark Mode
 * Description: Powerful plugin that allows you to easily enable dark mode on your website built with Elementor.
 * Plugin URI:  https://nocturne.deothemes.com/
 * Version:     1.2.4
 * Elementor tested up to: 5.0
 * Elementor Pro tested up to: 5.0
 * Author:      DeoThemes
 * Author URI:  https://deothemes.com/
 * Text Domain: nocturne-dark-mode
 * 
 */
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly.
define( 'NOCTURNE_PATH', plugin_dir_path( __FILE__ ) );
define( 'NOCTURNE_URL', plugin_dir_url( __FILE__ ) );
define( 'NOCTURNE_VERSION', '1.2.4' );
if ( !function_exists( 'nocturne_fs' ) ) {
    // Create a helper function for easy SDK access.
    function nocturne_fs() {
        global $nocturne_fs;
        if ( !isset( $nocturne_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $nocturne_fs = fs_dynamic_init( array(
                'id'             => '16391',
                'slug'           => 'nocturne',
                'type'           => 'plugin',
                'public_key'     => 'pk_142d6e6921275c061ac88d8024c32',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                    'slug'    => 'nocturne-dark-mode',
                    'contact' => false,
                    'support' => false,
                ),
                'is_live'        => true,
            ) );
        }
        return $nocturne_fs;
    }

    // Init Freemius.
    nocturne_fs();
    // Signal that SDK was initiated.
    do_action( 'nocturne_fs_loaded' );
}
/**
 * Main Nocturne Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Nocturne {
    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    const VERSION = NOCTURNE_VERSION;

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '5.6';

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var Nocturne The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return Nocturne An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct() {
        add_action( 'init', [$this, 'i18n'] );
        add_action( 'plugins_loaded', [$this, 'init'] );
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n() {
        load_plugin_textdomain( 'nocturne-dark-mode' );
    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init() {
        // Check if Elementor installed and activated
        if ( !did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_missing_main_plugin'] );
        }
        // Check for required Elementor version
        if ( !version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_minimum_elementor_version'] );
        }
        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_minimum_php_version'] );
        }
        // Enqueue admin styles
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
        // Add menu pages
        add_action( 'admin_menu', [$this, 'add_menu_pages'] );
        // Add the widget category
        add_action( 'elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories'] );
        // Register and enqueue assets
        add_action( 'elementor/frontend/after_register_scripts', [$this, 'register_scripts'] );
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'] );
        // Register editor assets
        add_action( 'elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles'] );
        // Register widgets
        add_action( 'elementor/widgets/register', [$this, 'register_widgets'] );
        // Add custom controls for a dark mode
        add_action(
            'elementor/element/after_section_end',
            [$this, 'add_dark_mode_section_controls'],
            10,
            3
        );
        // Include Files
        $this->includes();
        // Dark mode default
        add_filter(
            'language_attributes',
            [$this, 'default_dark_mode'],
            10,
            2
        );
    }

    /**
     * Apply dark mode as a default scheme
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function default_dark_mode( $output, $doctype ) {
        if ( 'html' !== $doctype ) {
            return $output;
        }
        if ( get_theme_mod( 'dark_mode_default_scheme_setting', false ) ) {
            $output .= ' data-scheme="dark"';
        }
        return $output;
    }

    /**
     * Add dark mode controls to the Elementor sections
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function add_dark_mode_section_controls( $element, $section_id, $args ) {
        // Container / Section
        if ( 'section' === $element->get_name() && 'section_typo' === $section_id || 'container' === $element->get_name() && 'section_shape_divider' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_shape_divider_top_color', [
                'label'     => esc_html__( 'Shape Divider Top Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    "shape_divider_top!" => '',
                ],
                'selectors' => [
                    ".dark {{WRAPPER}} .elementor-shape-top .elementor-shape-fill" => 'fill: {{UNIT}};',
                ],
            ] );
            $element->add_control( 'nocturne_shape_divider_bottom_color', [
                'label'     => esc_html__( 'Shape Divider Bottom Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    "shape_divider_bottom!" => '',
                ],
                'selectors' => [
                    ".dark {{WRAPPER}} .elementor-shape-bottom .elementor-shape-fill" => 'fill: {{UNIT}};',
                ],
            ] );
            $element->add_group_control( Group_Control_Background::get_type(), [
                'name'           => 'nocturne_background_dark',
                'types'          => ['classic', 'gradient'],
                'fields_options' => [
                    'nocturne_background_dark' => [
                        'frontend_available' => true,
                    ],
                    'image'                    => [
                        'background_lazyload' => [
                            'active' => true,
                            'keys'   => ['background_image', 'url'],
                        ],
                    ],
                ],
            ] );
            $element->add_control( 'nocturne_border_color_dark', [
                'label'     => esc_html__( 'Border', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_background_dark_class', [
                'label'        => esc_html__( 'Class', 'nocturne-dark-mode' ),
                'type'         => Controls_Manager::HIDDEN,
                'default'      => 'mode',
                'prefix_class' => 'nocturne-dark-',
                'condition'    => [
                    'nocturne_background_dark_background[url]!' => '',
                ],
            ] );
            $element->end_controls_section();
        }
        // Column
        if ( 'column' === $element->get_name() && 'section_typo' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_group_control( Group_Control_Background::get_type(), [
                'name'           => 'nocturne_background_dark',
                'types'          => ['classic', 'gradient'],
                'selector'       => '.dark {{WRAPPER}}:not(.elementor-motion-effects-element-type-background) > .elementor-widget-wrap, {{WRAPPER}} > .elementor-widget-wrap > .elementor-motion-effects-container > .elementor-motion-effects-layer',
                'fields_options' => [
                    'nocturne_background_dark' => [
                        'frontend_available' => true,
                    ],
                    'image'                    => [
                        'background_lazyload' => [
                            'active'   => true,
                            'keys'     => ['background_image', 'url'],
                            'selector' => '.elementor-element-populated',
                        ],
                    ],
                ],
            ] );
            $element->add_control( 'nocturne_border_color_dark', [
                'label'     => esc_html__( 'Border', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} > .elementor-widget-wrap.elementor-element-populated' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Heading
        if ( 'heading' === $element->get_name() && 'section_title_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_heading_color_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Text Editor
        if ( 'text-editor' === $element->get_name() && 'section_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_text_color_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-widget-container' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Button
        if ( 'button' === $element->get_name() && 'section_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->start_controls_tabs( 'nocturne_button_dark_mode_colors' );
            $element->start_controls_tab( 'nocturne_button_dark_colors_normal', [
                'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
            ] );
            $element->add_group_control( Group_Control_Background::get_type(), [
                'name'     => 'nocturne_button_background_dark_mode',
                'types'    => ['classic', 'gradient'],
                'selector' => '.dark {{WRAPPER}} .elementor-button',
            ] );
            $element->add_control( 'nocturne_button_text_color_dark', [
                'label'     => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-button' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_button_border_color_dark', [
                'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-button' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_tab();
            $element->start_controls_tab( 'nocturne_button_dark_colors_hover', [
                'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
            ] );
            $element->add_group_control( Group_Control_Background::get_type(), [
                'name'     => 'nocturne_button_background_dark_mode_hover',
                'types'    => ['classic', 'gradient'],
                'selector' => '.dark {{WRAPPER}} .elementor-button:hover, .dark {{WRAPPER}} .elementor-button:focus',
            ] );
            $element->add_control( 'nocturne_button_text_color_dark_hover', [
                'label'     => esc_html__( 'Text Color', 'envision-blocks' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-button:hover, .dark {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_button_border_color_dark_hover', [
                'label'     => esc_html__( 'Border Color', 'envision-blocks' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-button:hover, .dark {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->end_controls_section();
        }
        // Divider
        if ( 'divider' === $element->get_name() && 'section_divider_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_divider_color_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}.elementor-widget-divider:not(.elementor-widget-divider--view-line_text):not(.elementor-widget-divider--view-line_icon) .elementor-divider-separator' => '--divider-color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Spacer
        if ( 'spacer' === $element->get_name() && 'section_spacer' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_spacer_background_color_dark', [
                'label'     => esc_html__( 'Background color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-widget-container' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->end_controls_section();
        }
        // Icon
        if ( 'icon' === $element->get_name() && 'section_style_icon' === $section_id ) {
            $element->start_controls_section( 'nocturne_icon_style', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Style', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_icon_background_color', [
                'label'     => esc_html__( 'Background color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_group_control( Group_Control_Border::get_type(), [
                'name'     => 'nocturne_icon_border',
                'selector' => '{{WRAPPER}}.elementor-view-default .elementor-icon',
            ] );
            $element->add_control( 'nocturne_icon_border_radius', [
                'label'     => esc_html__( 'Border Radius', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ] );
            $element->add_control( 'nocturne_icon_padding', [
                'label'     => esc_html__( 'Padding', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ] );
            $element->end_controls_section();
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_icon_color_dark', [
                'label'     => esc_html__( 'Icon color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon, .dark {{WRAPPER}} .elementor-icon svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_icon_background_color_dark', [
                'label'     => esc_html__( 'Background color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_icon_border_color_dark', [
                'label'     => esc_html__( 'Border color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon' => 'border-color: {{VALUE}};',
                ],
            ] );
            $element->end_controls_section();
        }
        // Image Box
        if ( 'image-box' === $element->get_name() && 'section_style_content' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_image_box_border_color_dark', [
                'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-image-box-img img' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_image_box_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-image-box-title' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_image_box_description_color_dark', [
                'label'     => esc_html__( 'Description Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-image-box-description' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Icon box
        if ( 'icon-box' === $element->get_name() && 'section_style_content' === $section_id ) {
            $element->start_controls_section( 'nocturne_icon_box_style', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Style', 'nocturne-dark-mode' ),
            ) );
            $element->add_group_control( Group_Control_Box_Shadow::get_type(), [
                'name'     => 'nocturne_icon_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-widget-container',
            ] );
            $element->add_group_control( Group_Control_Border::get_type(), [
                'name'     => 'nocturne_icon_box_border',
                'selector' => '{{WRAPPER}} .elementor-widget-container',
            ] );
            $element->end_controls_section();
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_icon_box_icon_color_dark', [
                'label'     => esc_html__( 'Icon color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}.elementor-view-stacked .elementor-icon, .dark {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; fill: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_icon_box_base_color_dark', [
                'label'          => esc_html__( 'Base color', 'nocturne-dark-mode' ),
                'type'           => Controls_Manager::COLOR,
                'default'        => '',
                'fields_options' => [
                    'nocturne_icon_box_base_color_dark' => [
                        'frontend_available' => true,
                    ],
                ],
                'selectors'      => [
                    '.dark {{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_icon_box_title_color_dark', [
                'label'     => esc_html__( 'Title color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon-box-title' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_icon_box_description_color_dark', [
                'label'     => esc_html__( 'Description color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon-box-description' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_group_control( Group_Control_Box_Shadow::get_type(), [
                'name'     => 'nocturne_icon_box_shadow_dark',
                'selector' => '.dark {{WRAPPER}} .elementor-widget-container',
            ] );
            $element->add_group_control( Group_Control_Border::get_type(), [
                'name'     => 'nocturne_icon_box_border_dark',
                'selector' => '.dark {{WRAPPER}} .elementor-widget-container',
            ] );
            $element->end_controls_section();
        }
        // Star Rating
        if ( 'star-rating' === $element->get_name() && 'section_stars_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_star_rating_color_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-star-rating i:before' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_star_rating_unmarked_color_dark', [
                'label'     => esc_html__( 'Unmarked Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-star-rating i' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Image Carousel
        if ( 'image-carousel' === $element->get_name() && 'section_style_image' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_image_carousel_arrow_color_dark', [
                'label'     => esc_html__( 'Arrow Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-prev svg, .dark {{WRAPPER}} .elementor-swiper-button.elementor-swiper-button-next svg' => 'fill: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_image_carousel_dot_color_dark', [
                'label'     => esc_html__( 'Dot Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'background: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_image_carousel_active_dot_color_dark', [
                'label'     => esc_html__( 'Dot Active Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_image_carousel_image_border_color_dark', [
                'label'     => esc_html__( 'Image Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-image-carousel-wrapper .elementor-image-carousel .swiper-slide-image' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Basic Gallery
        if ( 'image-gallery' === $element->get_name() && 'section_caption' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_basic_gallery_image_border_color_dark', [
                'label'     => esc_html__( 'Image Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .gallery-item img' => 'border-color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_basic_gallery_caption_color_dark', [
                'label'     => esc_html__( 'Caption Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .gallery-item .gallery-caption' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Icon List
        if ( 'icon-list' === $element->get_name() && 'section_text_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->start_controls_tabs( 'nocturne_icon_list_dark_colors' );
            $element->start_controls_tab( 'nocturne_icon_list_dark_colors_normal', [
                'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
            ] );
            $element->add_control( 'nocturne_icon_list_icon_color_dark', [
                'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon-list-icon i'   => 'color: {{VALUE}}',
                    '.dark {{WRAPPER}} .elementor-icon-list-icon svg' => 'fill: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_icon_list_text_color_dark', [
                'label'     => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon-list-text' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_tab();
            $element->start_controls_tab( 'nocturne_icon_list_dark_colors_hover', [
                'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
            ] );
            $element->add_control( 'nocturne_icon_list_icon_color_hover_dark', [
                'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon i'   => 'color: {{VALUE}}',
                    '.dark {{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon svg' => 'fill: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_icon_list_text_color_hover_dark', [
                'label'     => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-text' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->end_controls_section();
        }
        // Counter
        if ( 'counter' === $element->get_name() && 'section_title' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_counter_number_color_dark', [
                'label'     => esc_html__( 'Number Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-counter-number-wrapper' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_counter_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-counter-title' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Progress Bar
        if ( 'progress' === $element->get_name() && 'section_title' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_progress_color_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-progress-wrapper .elementor-progress-bar' => 'background-color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_progress_bg_color_dark', [
                'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-progress-wrapper' => 'background-color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_progress_inner_text_color_dark', [
                'label'     => esc_html__( 'Inner Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-progress-bar' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_progress_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-title' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Testimonial
        if ( 'testimonial' === $element->get_name() && 'section_style_testimonial_job' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_testimonial_text_color_dark', [
                'label'     => esc_html__( 'Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-testimonial-content' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_testimonial_name_text_color_dark', [
                'label'     => esc_html__( 'Name Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-testimonial-name' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_testimonial_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-testimonial-job' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Tabs
        if ( 'tabs' === $element->get_name() && 'section_tabs_style' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_tabs_border_color_dark', [
                'label'     => esc_html__( 'Tabs Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-mobile-title, .dark {{WRAPPER}} .elementor-tab-desktop-title.elementor-active, .dark {{WRAPPER}} .elementor-tab-title:before, .dark {{WRAPPER}} .elementor-tab-title:after, .dark {{WRAPPER}} .elementor-tab-content, .dark {{WRAPPER}} .elementor-tabs-content-wrapper' => 'border-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_tabs_background_color_dark', [
                'label'     => esc_html__( 'Tabs Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-desktop-title.elementor-active' => 'background-color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-tabs-content-wrapper'               => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_tabs_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title, .dark {{WRAPPER}} .elementor-tab-title a' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_tabs_title_active_color_dark', [
                'label'     => esc_html__( 'Title Active Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title.elementor-active,
					 	.dark {{WRAPPER}} .elementor-tab-title.elementor-active a' => 'color: {{VALUE}}',
                ],
            ] );
            $element->add_control( 'nocturne_tabs_content_color_dark', [
                'label'     => esc_html__( 'Content Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-content' => 'color: {{VALUE}}',
                ],
            ] );
            $element->end_controls_section();
        }
        // Accordion
        if ( 'accordion' === $element->get_name() && 'section_toggle_style_content' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_accordion_border_color_dark', [
                'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-accordion-item'                                       => 'border-color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-accordion-item .elementor-tab-content'                => 'border-top-color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-accordion-item .elementor-tab-title.elementor-active' => 'border-bottom-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_title_background_color_dark', [
                'label'     => esc_html__( 'Title Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-accordion-icon, .dark {{WRAPPER}} .elementor-accordion-title' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_title_active_color_dark', [
                'label'     => esc_html__( 'Title Active Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-active .elementor-accordion-icon, .dark {{WRAPPER}} .elementor-active .elementor-accordion-title' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_icon_color_dark', [
                'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title .elementor-accordion-icon i:before' => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-tab-title .elementor-accordion-icon svg'      => 'fill: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_icon_active_color_dark', [
                'label'     => esc_html__( 'Icon Active Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon i:before' => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-tab-title.elementor-active .elementor-accordion-icon svg'      => 'fill: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_content_background_color_dark', [
                'label'     => esc_html__( 'Content Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-content' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_accordion_content_color_dark', [
                'label'     => esc_html__( 'Content Text Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-content' => 'color: {{VALUE}};',
                ],
            ] );
            $element->end_controls_section();
        }
        // Toggle
        if ( 'toggle' === $element->get_name() && 'section_toggle_style_content' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_toggle_border_color_dark', [
                'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-content' => 'border-bottom-color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-tab-title'   => 'border-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_title_bg_color_dark', [
                'label'     => esc_html__( 'Title Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-toggle-title, .dark {{WRAPPER}} .elementor-toggle-icon' => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-toggle-icon svg'                                        => 'fill: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_title_active_color_dark', [
                'label'     => esc_html__( 'Title Active Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title.elementor-active a, .dark {{WRAPPER}} .elementor-tab-title.elementor-active .elementor-toggle-icon' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_icon_color_dark', [
                'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title .elementor-toggle-icon i:before' => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-tab-title .elementor-toggle-icon svg'      => 'fill: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_icon_active_color_dark', [
                'label'     => esc_html__( 'Icon Active Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-title.elementor-active .elementor-toggle-icon i:before' => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-tab-title.elementor-active .elementor-toggle-icon svg'      => 'fill: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_content_bg_color_dark', [
                'label'     => esc_html__( 'Content Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-content' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_toggle_content_color_dark', [
                'label'     => esc_html__( 'Content Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-tab-content' => 'color: {{VALUE}};',
                ],
            ] );
            $element->end_controls_section();
        }
        // Social Icons
        if ( 'social-icons' === $element->get_name() && 'section_social_hover' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->start_controls_tabs( 'nocturne_socials_dark_colors' );
            $element->start_controls_tab( 'nocturne_socials_dark_colors_normal', [
                'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
            ] );
            $element->add_control( 'nocturne_socials_bg_color_dark', [
                'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-social-icon' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_socials_icon_color_dark', [
                'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-social-icon i'   => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-social-icon svg' => 'fill: {{VALUE}};',
                ],
            ] );
            $element->end_controls_tab();
            $element->start_controls_tab( 'nocturne_socials_dark_colors_hover', [
                'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
            ] );
            $element->add_control( 'nocturne_socials_bg_color_hover_dark', [
                'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-social-icon:hover' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_socials_icon_color_hover_dark', [
                'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-social-icon:hover i'   => 'color: {{VALUE}};',
                    '.dark {{WRAPPER}} .elementor-social-icon:hover svg' => 'fill: {{VALUE}};',
                ],
            ] );
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->end_controls_section();
        }
        // Alert
        if ( 'alert' === $element->get_name() && 'section_dismiss_icon' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->add_control( 'nocturne_alert_bg_color_dark', [
                'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-alert' => 'background-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_alert_border_color_dark', [
                'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-alert' => 'border-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_alert_title_color_dark', [
                'label'     => esc_html__( 'Title Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-alert-title' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_alert_description_color_dark', [
                'label'     => esc_html__( 'Description Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}} .elementor-alert-description' => 'color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_alert_dismiss_icon_color_dark', [
                'label'     => esc_html__( 'Dismiss Icon Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}' => '--dismiss-icon-normal-color: {{VALUE}};',
                ],
            ] );
            $element->add_control( 'nocturne_alert_dismiss_icon_hover_color_dark', [
                'label'     => esc_html__( 'Dismiss Icon Hover Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}' => '--dismiss-icon-hover-color: {{VALUE}};',
                ],
            ] );
            $element->end_controls_section();
        }
        // Text Path
        if ( 'text-path' === $element->get_name() && 'section_style_text_path' === $section_id ) {
            $element->start_controls_section( 'nocturne_dark_mode', array(
                'tab'   => Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            ) );
            $element->start_controls_tabs( 'nocturne_text_path_dark_colors' );
            $element->start_controls_tab( 'nocturne_text_path_dark_colors_normal', [
                'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
            ] );
            $element->add_control( 'nocturne_text_path_color_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}' => '--text-color: {{VALUE}};',
                ],
            ] );
            $element->end_controls_tab();
            $element->start_controls_tab( 'nocturne_text_path_dark_colors_hover', [
                'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
            ] );
            $element->add_control( 'nocturne_text_path_color_hover_dark', [
                'label'     => esc_html__( 'Color', 'nocturne-dark-mode' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '.dark {{WRAPPER}}' => '--text-color-hover: {{VALUE}};',
                ],
            ] );
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->end_controls_section();
        }
    }

    /**
     * Load all the necessary files
     * 
     * @since 1.0.0
     * @access public
     */
    public function includes() {
        require_once __DIR__ . '/includes/customizer/customizer.php';
        if ( defined( 'HAPPY_ADDONS_VERSION' ) ) {
            require_once __DIR__ . '/includes/compatibility/class-nocturne-happy-addons.php';
        }
        if ( defined( 'HFE_VER' ) ) {
            require_once __DIR__ . '/includes/compatibility/class-nocturne-hfe.php';
        }
    }

    /**
     * Add menu pages.
     *
     * @since 1.0.0
     * 
     * @access public
     */
    public function add_menu_pages() {
        add_menu_page(
            esc_html__( 'Nocturne', 'nocturne-dark-mode' ),
            esc_html__( 'Nocturne', 'nocturne-dark-mode' ),
            'manage_options',
            'nocturne-dark-mode',
            array($this, 'nocturne_getting_started_options_page'),
            'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAxOUMxNC45NzA2IDE5IDE5IDE0Ljk3MDYgMTkgMTBDMTkgNS4wMjk0NCAxNC45NzA2IDEgMTAgMUM1LjAyOTQ0IDEgMSA1LjAyOTQ0IDEgMTBDMSAxNC45NzA2IDUuMDI5NDQgMTkgMTAgMTlaTTguODIxMDkgNi4yMjY4M0M5LjAyNjA1IDUuNzY2ODMgOS4zMjE1NyA1LjM1MjgzIDkuNjkgNS4wMDk1MkM3LjA3MyA1LjE2OTUyIDUgNy4zNDI1MiA1IDEwQzUgMTIuNzYxNSA3LjIzODUgMTUgMTAuMDAwNSAxNUMxMi42NTggMTUgMTQuODMxIDEyLjkyNyAxNC45OSAxMC4zMDk1QzE0LjY0NjcgMTAuNjc4IDE0LjIzMjcgMTAuOTczNSAxMy43NzI3IDExLjE3ODRDMTMuMzEyNyAxMS4zODM0IDEyLjgxNjEgMTEuNDkzNiAxMi4zMTI2IDExLjUwMjVDMTEuODA5MSAxMS41MTE0IDExLjMwOSAxMS40MTg3IDEwLjg0MiAxMS4yMzAxQzEwLjM3NTEgMTEuMDQxNSA5Ljk1MDkgMTAuNzYwOCA5LjU5NDgxIDEwLjQwNDdDOS4yMzg3MSAxMC4wNDg2IDguOTU3OTkgOS42MjQ0NSA4Ljc2OTM5IDkuMTU3NTFDOC41ODA3OCA4LjY5MDU3IDguNDg4MTYgOC4xOTA0MiA4LjQ5NzA0IDcuNjg2OTFDOC41MDU5MyA3LjE4MzM5IDguNjE2MTQgNi42ODY4MyA4LjgyMTA5IDYuMjI2ODNaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K',
            2
        );
    }

    /**
     * Enqueue admin styles.
     *
     * @since 1.0.0
     * 
     * @access public
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style( 'nocturne-admin-styles', NOCTURNE_URL . '/assets/css/admin-style.css' );
    }

    /**
     * Add admin page content
     *
     * @since 1.0.0
     */
    public function nocturne_getting_started_options_page() {
        $urls = array(
            'plugin-url' => 'https://nocturne.deothemes.com/',
            'docs'       => 'https://docs.deothemes.com/nocturne',
        );
        ?>
		<div class="nocturne-page-header">
			<div class="nocturne-page-header__container">
				<div class="nocturne-page-header__branding">
					<a href="<?php 
        echo esc_url( $urls['plugin-url'] );
        ?>" target="_blank" rel="noopener" >
						<img src="<?php 
        echo esc_url( NOCTURNE_URL . '/assets/img/nocturne_logo.png' );
        ?>" class="nocturne-page-header__logo" alt="<?php 
        echo esc_attr__( 'Nocturne', 'nocturne-dark-mode' );
        ?>" />
					</a>
					<span class="nocturne-theme-version"><?php 
        echo esc_html( NOCTURNE_VERSION );
        ?></span>
				</div>
				<div class="nocturne-page-header__tagline">
					<span  class="nocturne-page-header__tagline-text">
						<?php 
        echo esc_html__( 'Made by ', 'nocturne-dark-mode' );
        ?>
						<a href="https://deothemes.com/"><?php 
        echo esc_html__( 'DeoThemes', 'nocturne-dark-mode' );
        ?></a>						
					</span>					
				</div>				
			</div>
		</div>

		<div class="wrap nocturne-container">
			<div class="nocturne-grid">

				<div class="nocturne-grid-content">
					<div class="nocturne-body">
						<h1 class="nocturne-title"><?php 
        esc_html_e( 'Getting Started', 'nocturne-dark-mode' );
        ?></h1>
						<p class="nocturne-intro-text">
							<?php 
        echo esc_html__( 'Nocturne is now installed and ready to use. Get ready to build something beautiful. To get started check the video below. We hope you enjoy it!', 'nocturne-dark-mode' );
        ?>
						</p>

						<?php 
        if ( nocturne_fs()->is_not_paying() ) {
            ?>
							<section class="nocturne-section nocturne-upgrade-notice">
								<h2 class="nocturne-section-title"><?php 
            esc_html_e( 'Try Pro', 'nocturne-dark-mode' );
            ?></h2>
								<p class="nocturne-text">
									<?php 
            echo esc_html__( 'Nocturne Pro is fully integrated with Elementor Pro and comes with additional toggle variations.', 'nocturne-dark-mode' );
            ?>
								</p>
								<a href="<?php 
            echo esc_url( nocturne_fs()->get_upgrade_url() );
            ?>" class="button button-hero button-primary">
									<?php 
            echo esc_html__( 'Upgrade Now!', 'nocturne-dark-mode' );
            ?>
								</a>
							</section>
						<?php 
        }
        ?>

						<!-- Installation Video -->
						<iframe width="880" height="400" src="https://www.youtube.com/embed/BQfr7GOjkUQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

					</div> <!-- .body -->
				</div> <!-- .content -->

				<aside class="nocturne-grid-sidebar">
					<div class="nocturne-grid-sidebar-widget-area">

						<div class="nocturne-widget">
							<h2 class="nocturne-widget-title"><?php 
        echo esc_html__( 'Useful Links', 'nocturne-dark-mode' );
        ?></h2>
							<ul class="nocturne-useful-links">
								<li>
									<a href="https://docs.deothemes.com/nocturne" target="_blank"><?php 
        echo esc_html__( 'Documentation', 'nocturne-dark-mode' );
        ?></a>
								</li>
								<li>
									<a href="https://wordpress.org/support/plugin/nocturne-dark-mode/reviews/#new-post" target="_blank"><?php 
        echo esc_html__( 'Rate us ★★★★★', 'nocturne-dark-mode' );
        ?></a>
								</li>
								<li>
									<a href="https://twitter.com/deothemes"><?php 
        echo esc_html__( 'Follow us', 'nocturne-dark-mode' );
        ?></a>
								</li>
							</ul>
						</div>

					</div>					
				</aside>

			</div> <!-- .grid -->
		</div> <!-- .container -->

		<?php 
    }

    /**
     * Add Elementor Widget Categories
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category( 'nocturne-widgets', [
            'title' => esc_html__( 'Nocturne Widgets', 'nocturne-dark-mode' ),
            'icon'  => 'fa fa-plug',
        ] );
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin() {
        if ( isset( $_GET['activate'] ) ) {
            unset($_GET['activate']);
        }
        $message = sprintf( 
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'nocturne-dark-mode' ),
            '<strong>' . esc_html__( 'Nocturne', 'nocturne-dark-mode' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'nocturne-dark-mode' ) . '</strong>'
         );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset($_GET['activate']);
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'nocturne-dark-mode' ),
            '<strong>' . esc_html__( 'Nocturne', 'nocturne-dark-mode' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'nocturne-dark-mode' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset($_GET['activate']);
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'nocturne-dark-mode' ),
            '<strong>' . esc_html__( 'Nocturne', 'nocturne-dark-mode' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'nocturne-dark-mode' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
    }

    /**
     * Init Widgets
     *
     * Include widgets files and register them
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function register_widgets() {
        // Include Widget files
        require_once __DIR__ . '/widgets/nocturne-switcher.php';
        require_once __DIR__ . '/widgets/nocturne-image.php';
        // // Register Widgets
        Plugin::instance()->widgets_manager->register( new Nocturne_Switcher() );
        Plugin::instance()->widgets_manager->register( new Nocturne_Image() );
    }

    /**
     * Register custom JS scripts for widgets
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function register_scripts() {
        wp_enqueue_script(
            'nocturne-scripts',
            plugins_url( '/build/index.js', __FILE__ ),
            ['jquery'],
            NOCTURNE_VERSION,
            true
        );
    }

    /**
     * Enqueue custom CSS styles for widgets
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'nocturne-styles',
            plugins_url( '/build/index.css', __FILE__ ),
            [],
            NOCTURNE_VERSION
        );
        wp_style_add_data( 'nocturne-styles', 'rtl', 'replace' );
    }

    /**
     * Enqueue custom CSS for editor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function enqueue_editor_styles() {
        wp_enqueue_style( 'nocturne-editor-styles', plugins_url( '/assets/css/editor-style.css', __FILE__ ) );
        wp_style_add_data( 'nocturne-editor-styles', 'rtl', 'replace' );
    }

}

Nocturne::instance();