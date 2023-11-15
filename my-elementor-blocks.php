<?php
/**
 * Plugin Name: My Elementor Blocks
 * Description: Custom Elementor blocks for displaying ACF fields.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Реєстрація AJAX обробників для залогованих і незалогованих користувачів
add_action( 'wp_ajax_get_terms_for_taxonomy', 'get_terms_for_taxonomy_handler' );
add_action( 'wp_ajax_nopriv_get_terms_for_taxonomy', 'get_terms_for_taxonomy_handler' );

function get_terms_for_taxonomy_handler() {
    $taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
    if ( ! empty( $taxonomy ) ) {
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ) );

        wp_send_json_success( $terms );
    } else {
        wp_send_json_error( 'No terms found.' );
    }

    wp_die();
}

// Підключення JavaScript файлу у редакторі Elementor
function enqueue_elementor_editor_scripts() {
    wp_enqueue_script(
        'my-elementor-script',
        plugin_dir_url( __FILE__ ) . 'assets/js/my-elementor-script.js', 
        array( 'jquery' ),
        null,
        true
    );

    // Локалізація ajaxurl для використання у JavaScript
    wp_localize_script(
        'my-elementor-script',
        'ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
    );
}

add_action( 'elementor/editor/before_enqueue_scripts', 'enqueue_elementor_editor_scripts' );

// Реєстрація віджета після ініціалізації Elementor
function register_my_custom_widgets() {
    require_once( __DIR__ . '/my-custom-widget.php' );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \My_Custom_Widget() );
}

add_action( 'elementor/widgets/widgets_registered', 'register_my_custom_widgets' );