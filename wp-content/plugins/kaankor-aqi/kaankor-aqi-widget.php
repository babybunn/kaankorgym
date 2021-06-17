<?php
/**
* Plugin Name: KaanKor AQI
* Description: Show weather details in a widget with https://aqicn.org/api/
* Version:     1.0.0
* Author:      pangp
* Author URI:  http://pangp.herokuapp.com
* License:     GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: kaankor-aqi
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KAANKORAQI_DIR_PATH', plugin_dir_url( __FILE__ ) );

function kaankoraqi_load_scripts() {
    wp_enqueue_style( 'kaankoraqi-widget-style', plugin_dir_url(__FILE__) . 'css/style-widget.css' );
}
add_action( 'wp_enqueue_scripts', 'kaankoraqi_load_scripts' );


// Load Class
require_once( plugin_dir_path(__FILE__) . '/includes/kaankor-aqi-class.php' );
require_once( plugin_dir_path(__FILE__) . '/includes/option-code.php' );
require_once( plugin_dir_path(__FILE__) . '/includes/widget-display.php' );

function kaankoraqi_plugin_activated() {
    $options_default = array(
        'latitude'  => '52.232600',
        'longitude' => '20.78101',
    );
    add_option('kaankoraqi_options', $options_default);
}
register_activation_hook( __FILE__, 'kaankoraqi_plugin_activated');

function kaankoraqi_plugin_uninstall() {
    delete_option('kaankoraqi_options');
}
register_uninstall_hook( __FILE__, 'kaankoraqi_plugin_uninstall');

// Register
function register_kaankor_aqi_widget() {
    register_widget( 'KaanKorAQI_Widget' );
}
add_action( 'widgets_init', 'register_kaankor_aqi_widget' );

//Adding options page
function kaankoraqi_create_options_page() {
    // $options = get_option( 'kaankoraqi_options' );
    // $capability = $options['user_can_set'] ? 'read' : 'manage_options';
    
    add_options_page( __( 'KaanKor AQI Settings', 'kaankor-aqi' ), __( 'KaanKor AQI Settings', 'kaankor-aqi' ), 'manage_options', __FILE__, 'kaankoraqi_options_code' );
}
add_action('admin_menu', 'kaankoraqi_create_options_page');