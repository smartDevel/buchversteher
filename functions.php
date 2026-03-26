<?php
/**
 * Child Theme functions
 * 
 * Strukturiert in funktional getrennte Module.
 * Siehe /inc/ für Details.
 */

if (!defined('ABSPATH')) exit;

// Parent Theme CSS laden
if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri) {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('chld_thm_cfg_parent_css')) :
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style('chld_thm_cfg_parent', trailingslashit(get_template_directory_uri()) . 'style.css', array());
    }
endif;
add_action('wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10);

// Module laden
require_once __DIR__ . '/inc/meta.php';
require_once __DIR__ . '/inc/rest-endpoints.php';
require_once __DIR__ . '/inc/shortcodes.php';
require_once __DIR__ . '/inc/post-types.php';
require_once __DIR__ . '/inc/frontend.php';
require_once __DIR__ . '/inc/tag-cloud.php';
require_once __DIR__ . '/inc/yoast-og-image.php';

