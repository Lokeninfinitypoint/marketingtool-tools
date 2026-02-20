<?php
/**
 * MarketingTool Pro Functions
 */

// Enqueue styles and scripts
function marketingtool_scripts() {
    wp_enqueue_style('marketingtool-style', get_stylesheet_uri());
    wp_enqueue_script('marketingtool-js', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'marketingtool_scripts');

// Register menus
function marketingtool_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'marketingtool-pro'),
        'footer' => __('Footer Menu', 'marketingtool-pro'),
    ));
}
add_action('init', 'marketingtool_menus');

// Theme support
function marketingtool_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'marketingtool_setup');
