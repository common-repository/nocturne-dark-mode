<?php
/**
 * Plugin Customizer.
 * @author  	 DeoThemes
 * @copyright  (c) Copyright by DeoThemes
 * @link       https://deothemes.com
 * @package 	 NocturneDarkMode
 * @since 		 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

require_once( __DIR__ . '/custom-html-control.php' );

// Add floating switch
if ( get_theme_mod( 'dark_mode_show_default_switch_setting', true ) ) {
	add_action( 'wp_body_open', function() {
		?>
		<button class="js-dark-mode-trigger nocturne-dark-mode-trigger nocturne-dark-mode-floating-trigger fixed bottom-6 right-6 z-50 group flex !p-0 h-10 w-10 items-center justify-center rounded-full border border-solid border-jacarta-100 bg-white transition-colors hover:border-transparent hover:bg-accent focus:border-transparent focus:bg-accent dark:border-transparent dark:bg-white/[.15] dark:hover:bg-accent" aria-label="light">
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
	} );
}


function nocturne_customize_register( $wp_customize ) {

	$wp_customize->add_panel(
		'dark_mode_settings',
		array(
			'title'      => esc_html__( 'Dark Mode', 'nocturne-dark-mode' ),
			'priority'   => 40,
			'capability' => 'edit_theme_options',
		)
	);

	// Site Colors Section
	$wp_customize->add_section( 'nocturne_colors_section', array(
		'title' => esc_html__( 'Site Colors', 'nocturne-dark-mode' ),
		'panel' => 'dark_mode_settings',
		'capability' => 'edit_theme_options',
	) );

	// Default scheme
	$wp_customize->add_setting(
		'dark_mode_default_scheme_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'nocturne_sanitize_checkbox',
		)
	);

	$wp_customize->add_control( 'dark_mode_default_scheme', array(
		'type' => 'checkbox',
		'label' => esc_html__( 'Activate dark mode as default', 'nocturne-dark-mode' ),
		'section' => 'nocturne_colors_section',
    'settings'  => 'dark_mode_default_scheme_setting',
	) );

	// Dark mode site colors
	$wp_customize->add_setting(
		'dark_mode_site_colors_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control( new Nocturne_Custom_HTML_Control( $wp_customize, 'dark_mode_site_colors', array(
		'section'    => 'nocturne_colors_section',
		'settings'   => 'dark_mode_site_colors_setting',
		'input_attrs' => array(
			'html' => '<h2 class="nocturne-customizer-title">' . esc_html__( 'Dark Mode Site Colors', 'nocturne-dark-mode' ) . '</h2>',
		),
	)));

	// Body background
	$wp_customize->add_setting(
		'dark_mode_bg_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_bg_color',
			array(
				'label' 	 => esc_html__( 'Background', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_colors_section',
				'settings' => 'dark_mode_bg_color_setting',
			)
		)
	);

	// Headings
	$wp_customize->add_setting(
		'dark_mode_headings_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_headings_color',
			array(
				'label' 	 => esc_html__( 'Headings', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_colors_section',
				'settings' => 'dark_mode_headings_color_setting',
			)
		)
	);

	// Text
	$wp_customize->add_setting(
		'dark_mode_text_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_text_color',
			array(
				'label' 	 => esc_html__( 'Text', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_colors_section',
				'settings' => 'dark_mode_text_color_setting',
			)
		)
	);

	// Floating Toggle Section
	$wp_customize->add_section( 'nocturne_floating_toggle_section', array(
		'title' => esc_html__( 'Floating Toggle', 'nocturne-dark-mode' ),
		'panel' => 'dark_mode_settings',
		'capability' => 'edit_theme_options',
	) );

	// Floating toggle
	$wp_customize->add_setting(
		'dark_mode_show_default_switch_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'nocturne_sanitize_checkbox',
			'default'						=> true,
		)
	);

	$wp_customize->add_control( 'dark_mode_show_default_switch', array(
		'type' => 'checkbox',
		'label' => esc_html__( 'Floating toggle', 'nocturne-dark-mode' ),
		'description' => esc_html__( 'Requires page reload', 'nocturne-dark-mode' ),
		'transport' => 'postMessage',
		'section' => 'nocturne_floating_toggle_section',
    'settings'  => 'dark_mode_show_default_switch_setting',
	) );

	// BG color
	$wp_customize->add_setting(
		'dark_mode_floating_toggle_bg_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_floating_toggle_bg_color',
			array(
				'label' 	 => esc_html__( 'Background Color', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_floating_toggle_section',
				'settings' => 'dark_mode_floating_toggle_bg_color_setting',
			)
		)
	);

	// Border color
	$wp_customize->add_setting(
		'dark_mode_floating_toggle_border_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_floating_toggle_border_color',
			array(
				'label' 	 => esc_html__( 'Border Color', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_floating_toggle_section',
				'settings' => 'dark_mode_floating_toggle_border_color_setting',
			)
		)
	);

	// Icon color
	$wp_customize->add_setting(
		'dark_mode_floating_toggle_icon_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_floating_toggle_icon_color',
			array(
				'label' 	 => esc_html__( 'Icon Color', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_floating_toggle_section',
				'settings' => 'dark_mode_floating_toggle_icon_color_setting',
			)
		)
	);

	// BG dark mode color
	$wp_customize->add_setting(
		'dark_mode_floating_toggle_dark_mode_bg_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_floating_toggle_dark_mode_bg_color',
			array(
				'label' 	 => esc_html__( 'Dark Mode Background Color', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_floating_toggle_section',
				'settings' => 'dark_mode_floating_toggle_dark_mode_bg_color_setting',
			)
		)
	);

	// Icon dark mode color
	$wp_customize->add_setting(
		'dark_mode_floating_toggle_dark_mode_icon_color_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'					=> 'postMessage'
		)
	);	

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'dark_mode_floating_toggle_dark_mode_icon_color',
			array(
				'label' 	 => esc_html__( 'Dark Mode Icon Color', 'nocturne-dark-mode' ),
				'section'  => 'nocturne_floating_toggle_section',
				'settings' => 'dark_mode_floating_toggle_dark_mode_icon_color_setting',
			)
		)
	);

	// Bottom offset
	$wp_customize->add_setting(
		'dark_mode_toggle_bottom_offset_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'						=> '',
			'transport'					=> 'postMessage'
		)
	);

	$wp_customize->add_control( 'dark_mode_toggle_bottom_offset', array(
		'label'      => esc_html__( 'Toggle bottom offset (px)', 'nocturne-dark-mode' ),
		'section'    => 'nocturne_floating_toggle_section',
		'settings'   => 'dark_mode_toggle_bottom_offset_setting',
		'type'       => 'text',
	) );

	// Right offset
	$wp_customize->add_setting(
		'dark_mode_toggle_right_offset_setting',
		array(
			'type'       				=> 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'						=> '',
			'transport'					=> 'postMessage'
		)
	);

	$wp_customize->add_control( 'dark_mode_toggle_right_offset', array(
		'label'      => esc_html__( 'Toggle right offset (px)', 'nocturne-dark-mode' ),
		'section'    => 'nocturne_floating_toggle_section',
		'settings'   => 'dark_mode_toggle_right_offset_setting',
		'type'       => 'text',
	) );

}
add_action( 'customize_register', 'nocturne_customize_register' );


/**
 * Check if the floating switch setting is true
 */
function nocturne_floating_switch_is_checkbox_checked( $control ) {
	return $control->manager->get_setting( 'dark_mode_show_default_switch_setting' )->value() == true;
}

/**
 * Customizer generate CSS
 */
function nocturne_customize_css() {
  ?>
    <style type="text/css">
			.dark h1,.dark h2,.dark h3,.dark h4,.dark h5,.dark h6 { color: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_headings_color_setting', '#ffffff' ) ); ?> }
      .dark body {
				color: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_text_color_setting', '#9c9c9c' ) ); ?>;
				background-color: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_bg_color_setting', '#000000' ) ); ?>;
			}
			.dark .nocturne-dark-mode-floating-trigger {
				background-color: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_floating_toggle_dark_mode_bg_color_setting', '#ffffff26' ) ); ?>;
			}
			.nocturne-dark-mode-floating-trigger {
				bottom: <?php echo get_theme_mod( 'dark_mode_toggle_bottom_offset_setting', '' ) . 'px'; ?>;
				right: <?php echo get_theme_mod( 'dark_mode_toggle_right_offset_setting', '' ) . 'px'; ?>;
				background-color: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_floating_toggle_bg_color_setting', '#ffffff' ) ); ?>;
				border-color: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_floating_toggle_border_color_setting', '#e7e8ec' ) ); ?>;
			}
			.nocturne-dark-mode-floating-trigger .dark-mode-light {
				fill: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_floating_toggle_icon_color_setting', '#131740' ) ); ?>;
			}
			.nocturne-dark-mode-floating-trigger .dark-mode-dark {
				fill: <?php echo sanitize_hex_color( get_theme_mod( 'dark_mode_floating_toggle_dark_mode_icon_color_setting', '#ffffff' ) ); ?>;
			}			
    </style>
  <?php
}
add_action( 'wp_head', 'nocturne_customize_css' );


/**
 * Customize live preview changes
 */
function nocturne_customizer_live_preview() {
	wp_enqueue_script( 'nocturne-customizer', plugins_url( '/customizer.js', __FILE__ ), array( 'jquery','customize-preview' ), NOCTURNE_VERSION, true );
}
add_action( 'customize_preview_init', 'nocturne_customizer_live_preview' );

/**
 * Sanitize checkbox
 */
function nocturne_sanitize_checkbox( $checked ) {
  return ( ( isset( $checked ) && true == $checked ) ? true : false );
}