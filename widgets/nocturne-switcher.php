<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly.
class Nocturne_Switcher extends Widget_Base {
    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
        wp_register_script(
            'nocturne-switcher',
            NOCTURNE_URL . 'build/widget/index.js',
            [],
            NOCTURNE_VERSION,
            false
        );
    }

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'nocturne-switcher';
    }

    /**
     * Get widget title.
     *
     * Retrieve widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Dark Mode Switcher', 'nocturne-dark-mode' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-dual-button nocturne-icon';
    }

    /**
     * Retrieve the list of scripts the widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends() {
        return ['nocturne-switcher'];
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['nocturne-widgets'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['dark mode', 'switch'];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        $this->section_toggle_style();
        $this->section_icon_style();
    }

    /**
     * Content > Toggle Style.
     */
    private function section_toggle_style() {
        $this->start_controls_section( 'section_toggle_style', [
            'label' => esc_html__( 'Toggle', 'nocturne-dark-mode' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $styles = [
            'style-1' => esc_html__( 'Style 1', 'nocturne-dark-mode' ),
        ];
        $this->add_control( 'toggle_style', [
            'label'       => esc_html__( 'Style', 'nocturne-dark-mode' ),
            'type'        => Controls_Manager::SELECT,
            'default'     => 'style-1',
            'label_block' => true,
            'options'     => $styles,
        ] );
        if ( nocturne_fs()->is_not_paying() ) {
            $this->add_control( 'upgrade_notice', [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => '<strong>' . esc_html__( 'More styles available in Pro!', 'nocturne-dark-mode' ) . '</strong><br>' . sprintf( __( '<a href="%s" target="_blank">Upgrade Now</a>.', 'nocturne-dark-mode' ), esc_url( nocturne_fs()->get_upgrade_url() ) ),
                'separator'       => 'after',
                'content_classes' => 'nocturne-upgrade-notice elementor-panel-alert elementor-panel-alert-info',
            ] );
        }
        $this->end_controls_section();
    }

    /**
     * Style > Icon.
     */
    private function section_icon_style() {
        $this->start_controls_section( 'section_icon_style', [
            'label' => esc_html__( 'Toggle', 'nocturne-dark-mode' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );
        $this->add_responsive_control( 'icon_base_size', [
            'label'     => esc_html__( 'Base Size', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::SLIDER,
            'range'     => [
                'px' => [
                    'min'  => 1,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'separator' => 'before',
            'condition' => [
                'toggle_style' => 'style-1',
            ],
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_responsive_control( 'icon_size', [
            'label'     => esc_html__( 'Icon Size', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::SLIDER,
            'range'     => [
                'px' => [
                    'min'  => 1,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'condition' => [
                'toggle_style' => 'style-1',
            ],
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_control( 'nocturne_switcher_colors_heading', [
            'label'     => esc_html__( 'Colors', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );
        $this->start_controls_tabs( 'tabs_icon_style' );
        $this->start_controls_tab( 'tab_icon_normal', [
            'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
        ] );
        $this->add_control( 'icon_bg_color', [
            'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_color', [
            'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger svg'                => 'fill: {{VALUE}};',
                '{{WRAPPER}} .nocturne-dark-mode-trigger__selector--style-3' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_border_color', [
            'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger' => 'border-color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_icon_hover', [
            'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
        ] );
        $this->add_control( 'icon_bg_color_hover', [
            'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger:hover, {{WRAPPER}} .nocturne-dark-mode-trigger:focus' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_color_hover', [
            'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger:hover svg, {{WRAPPER}} .nocturne-dark-mode-trigger:focus svg'                                                                                       => 'fill: {{VALUE}};',
                '{{WRAPPER}} .nocturne-dark-mode-trigger:hover .nocturne-dark-mode-trigger__selector--style-3, {{WRAPPER}} .nocturne-dark-mode-trigger:focus .nocturne-dark-mode-trigger__selector--style-3' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_border_color_hover', [
            'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .nocturne-dark-mode-trigger:hover, {{WRAPPER}} .nocturne-dark-mode-trigger:focus' => 'border-color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        // Dark Mode
        $this->add_control( 'nocturne_switcher_heading_dark_mode', [
            'label'     => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ] );
        $this->start_controls_tabs( 'tabs_icon_style_dark' );
        $this->start_controls_tab( 'tab_icon_normal_dark', [
            'label' => esc_html__( 'Normal', 'nocturne-dark-mode' ),
        ] );
        $this->add_control( 'icon_bg_color_dark', [
            'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_color_dark', [
            'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger svg'                => 'fill: {{VALUE}};',
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger__selector--style-3' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_border_color_dark', [
            'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger' => 'border-color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();
        $this->start_controls_tab( 'tab_icon_hover_dark', [
            'label' => esc_html__( 'Hover', 'nocturne-dark-mode' ),
        ] );
        $this->add_control( 'icon_bg_color_hover_dark', [
            'label'     => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger:hover, .dark {{WRAPPER}} .nocturne-dark-mode-trigger:focus' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_color_hover_dark', [
            'label'     => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger:hover svg, .dark {{WRAPPER}} .nocturne-dark-mode-trigger:focus svg'                                                                                       => 'fill: {{VALUE}};',
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger:hover .nocturne-dark-mode-trigger__selector--style-3, .dark {{WRAPPER}} .nocturne-dark-mode-trigger:focus .nocturne-dark-mode-trigger__selector--style-3' => 'background-color: {{VALUE}};',
            ],
        ] );
        $this->add_control( 'icon_border_color_hover_dark', [
            'label'     => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '.dark {{WRAPPER}} .nocturne-dark-mode-trigger:hover, .dark {{WRAPPER}} .nocturne-dark-mode-trigger:focus' => 'border-color: {{VALUE}};',
            ],
        ] );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $style = ( isset( $settings['toggle_style'] ) ? $settings['toggle_style'] : 'style-1' );
        if ( 'style-1' === $style ) {
            ?>
			<button class="js-dark-mode-trigger-<?php 
            echo esc_attr( $this->get_id() );
            ?> nocturne-dark-mode-trigger border-solid group flex !p-0 h-10 w-10 items-center justify-center rounded-full border border-jacarta-100 bg-white transition-colors hover:border-transparent hover:bg-accent focus:border-transparent focus:bg-accent dark:border-transparent dark:bg-white/[.15] dark:hover:bg-accent" aria-label="light">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="dark-mode-light h-4 w-4 fill-jacarta-700 transition-colors group-hover:fill-white group-focus:fill-white dark:hidden">
					<path fill="none" d="M0 0h24v24H0z"></path>
					<path d="M11.38 2.019a7.5 7.5 0 1 0 10.6 10.6C21.662 17.854 17.316 22 12.001 22 6.477 22 2 17.523 2 12c0-5.315 4.146-9.661 9.38-9.981z"></path>
				</svg>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="dark-mode-dark hidden h-4 w-4 fill-jacarta-700 transition-colors group-hover:fill-white group-focus:fill-white dark:block dark:fill-white">
					<path fill="none" d="M0 0h24v24H0z"></path>
					<path d="M12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12zM11 1h2v3h-2V1zm0 19h2v3h-2v-3zM3.515 4.929l1.414-1.414L7.05 5.636 5.636 7.05 3.515 4.93zM16.95 18.364l1.414-1.414 2.121 2.121-1.414 1.414-2.121-2.121zm2.121-14.85l1.414 1.415-2.121 2.121-1.414-1.414 2.121-2.121zM5.636 16.95l1.414 1.414-2.121 2.121-1.414-1.414 2.121-2.121zM23 11v2h-3v-2h3zM4 11v2H1v-2h3z"></path>
				</svg>
			</button>
		<?php 
        }
        ?>
		
		<?php 
    }

}
