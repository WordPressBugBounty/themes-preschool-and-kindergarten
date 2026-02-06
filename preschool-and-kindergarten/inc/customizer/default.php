<?php 
/**
 * Default Theme Option.
 *
 * @package Preschool and Kindergarten
 */
 
function preschool_and_kindergarten_customize_register_default( $wp_customize ) {
	
	/* Default Settings*/
    $wp_customize->add_panel(
     	'wp_default_panel',
     	array( 
     		'priority' => 10,
     		'capability' => 'edit_theme_options',
     		'theme_support' => '',
     		'title' => __('Default Settings','preschool-and-kindergarten'),
     		'description' => __('Default section provided by wordpress customizer','preschool-and-kindergarten'),
     	)
    );

	$wp_customize->add_section(
        'preschool_and_kindergarten_typography_section',
        array(
            'title' => __( 'Typography Settings', 'preschool-and-kindergarten' ),
            'priority' => 50,
        )
    );

    $wp_customize->add_setting(
        'ed_localgoogle_fonts',
        array(
            'default'           => false,
            'sanitize_callback' => 'preschool_and_kindergarten_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'ed_localgoogle_fonts',
        array(
            'label'   => __( 'Load Google Fonts Locally', 'preschool-and-kindergarten' ),
            'section' => 'preschool_and_kindergarten_typography_section',
            'type'    => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'ed_preload_local_fonts',
        array(
            'default'           => false,
            'sanitize_callback' => 'preschool_and_kindergarten_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'ed_preload_local_fonts',
        array(
            'label'           => __( 'Preload Local Fonts', 'preschool-and-kindergarten' ),
            'section'         => 'preschool_and_kindergarten_typography_section',
            'type'            => 'checkbox',
            'active_callback' => 'preschool_and_kindergarten_flush_fonts_callback'
        )
    );
    

    $wp_customize->add_setting(
        'flush_google_fonts',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses',
        )
    );

    $wp_customize->add_control(
        'flush_google_fonts',
        array(
            'label'       => __( 'Flush Local Fonts Cache', 'preschool-and-kindergarten' ),
            'description' => __( 'Click the button to reset the local fonts cache.', 'preschool-and-kindergarten' ),
            'type'        => 'button',
            'settings'    => array(),
            'section'     => 'preschool_and_kindergarten_typography_section',
            'input_attrs' => array(
                'value' => __( 'Flush Local Fonts Cache', 'preschool-and-kindergarten' ),
                'class' => 'button button-primary flush-it',
            ),
            'active_callback' => 'preschool_and_kindergarten_flush_fonts_callback'
        )
    );

    $wp_customize->get_section( 'title_tagline' )->panel     = 'wp_default_panel';
    $wp_customize->get_section( 'colors' )->panel            = 'wp_default_panel';
    $wp_customize->get_section( 'background_image' )->panel  = 'wp_default_panel';
    $wp_customize->get_section( 'static_front_page' )->panel = 'wp_default_panel'; 
	$wp_customize->get_section( 'preschool_and_kindergarten_typography_section' )->panel    = 'wp_default_panel';
    $wp_customize->get_section( 'preschool_and_kindergarten_typography_section' )->priority = 80;

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'background_color' )->transport = 'refresh';
    $wp_customize->get_setting( 'background_image' )->transport = 'refresh';	

	/** Default Settings Ends */
     
    if ( version_compare( get_bloginfo('version'),'4.9', '>=') ) {
        $wp_customize->get_section( 'static_front_page' )->title = __( 'Static Front Page', 'preschool-and-kindergarten' );
    }
}
add_action( 'customize_register', 'preschool_and_kindergarten_customize_register_default' );

function preschool_and_kindergarten_flush_fonts_callback( $control ){
    $ed_localgoogle_fonts   = $control->manager->get_setting( 'ed_localgoogle_fonts' )->value();
    $control_id   = $control->id;
    
    if ( $control_id == 'flush_google_fonts' && $ed_localgoogle_fonts ) return true;
    if ( $control_id == 'ed_preload_local_fonts' && $ed_localgoogle_fonts ) return true;
    return false;
}