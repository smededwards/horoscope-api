<?php
/**
 * Plugin Name: Horoscope API
 * Plugin URI:  https://github.com/smededwards/horoscope-plugin
 * Description: A plugin to fetch and display horoscope data.
 * Author:      Michael Edwards
 * Author URI:  https://github.com/smededwards/
 * Text Domain: horoscope-plugin
 * Version:     1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

// Define the plugin path
define('HOROSCOPE_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include necessary files
require_once HOROSCOPE_PLUGIN_PATH . 'includes/Admin.php';
require_once HOROSCOPE_PLUGIN_PATH . 'includes/Cache.php';
require_once HOROSCOPE_PLUGIN_PATH . 'includes/HoroscopeAPI.php';
require_once HOROSCOPE_PLUGIN_PATH . 'includes/Shortcode.php';

// Enqueue styles and scripts
function horoscope_plugin_enqueue_scripts()
{
    wp_enqueue_script('horoscope-js', plugins_url('/build/script.js', __FILE__), array(), '1.0.0', true);
    wp_enqueue_style('horoscope-css', plugins_url('/build/style.css', __FILE__), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'horoscope_plugin_enqueue_scripts');

// Initialize the plugin
function horoscope_api_init()
{
    new \HoroscopePlugin\HoroscopeAPI();
    new \HoroscopePlugin\Shortcode();
    new \HoroscopePlugin\Admin();
}
add_action('plugins_loaded', 'horoscope_api_init');

// Register activation and deactivation hooks
register_activation_hook(__FILE__, function () {
    new \HoroscopePlugin\Cache();
});

register_deactivation_hook(__FILE__, function () {
    $cache = new \HoroscopePlugin\Cache();
    $cache->deleteCache();
});
