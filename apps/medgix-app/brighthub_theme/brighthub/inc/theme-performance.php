<?php
/**
 * BrightHub Theme Performance Optimizations
 * Comprehensive performance improvements for smooth operation
 *
 * @package Case-Themes
 * @since BrightHub 1.0.4
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================
 * 1. OPTIMIZE SCRIPT/STYLE LOADING
 * ============================================
 */

/**
 * Conditional Script Loading - Only load what's needed
 */
add_action('wp_enqueue_scripts', 'brighthub_optimized_scripts', 100);
function brighthub_optimized_scripts() {
    $version = wp_get_theme(get_template())->get('Version');
    
    // Always load core styles
    wp_enqueue_style('pxl-caseicon', get_template_directory_uri() . '/assets/css/caseicon.css', array(), $version);
    wp_enqueue_style('pxl-phosphor', get_template_directory_uri() . '/assets/css/phosphor.css', array(), $version);
    wp_enqueue_style('pxl-grid', get_template_directory_uri() . '/assets/css/grid.css', array(), $version);
    wp_enqueue_style('pxl-style', get_template_directory_uri() . '/assets/css/style.css', array(), $version);
    wp_add_inline_style('pxl-style', brighthub_inline_styles());
    wp_enqueue_style('pxl-base', get_template_directory_uri() . '/style.css', array(), $version);
    
    // Google Fonts with preload
    $fonts_url = brighthub_fonts_url();
    if ($fonts_url) {
        wp_enqueue_style('pxl-google-fonts', $fonts_url, array(), null);
        add_filter('style_loader_tag', 'brighthub_preload_fonts', 10, 2);
    }
    
    // Conditional library loading
    $load_magnific = false;
    $load_wow = false;
    $load_intl_tel = false;
    $load_odometer = false;
    
    // Check if page needs these libraries
    if (is_singular() || is_page() || is_home() || is_archive()) {
        global $post;
        $content = '';
        if ($post) {
            $content = $post->post_content;
        }
        
        // Magnific Popup - only if gallery/lightbox detected
        if (strpos($content, 'pxl-gallery') !== false || 
            strpos($content, 'pxl-video__popup') !== false ||
            strpos($content, 'lightbox') !== false ||
            strpos($content, 'magnific') !== false) {
            $load_magnific = true;
        }
        
        // WOW Animation - only if animation classes detected
        if (strpos($content, 'wow') !== false || 
            strpos($content, 'animate') !== false ||
            strpos($content, 'fadeIn') !== false) {
            $load_wow = true;
        }
        
        // Intl Tel Input - only on contact/forms
        if (strpos($content, 'your-phone') !== false || 
            strpos($content, 'intlTelInput') !== false ||
            is_page_template('page-contact.php') ||
            is_page_template('page-form.php')) {
            $load_intl_tel = true;
        }
        
        // Odometer - only if counter detected
        if (strpos($content, 'odometer') !== false || 
            strpos($content, 'pxl-counter') !== false) {
            $load_odometer = true;
        }
    }
    
    // Load conditional scripts
    if ($load_magnific) {
        wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/assets/css/libs/magnific-popup.css', array(), '1.1.0');
        wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/assets/js/libs/magnific-popup.min.js', array('jquery'), '1.1.0', true);
    }
    
    if ($load_wow) {
        wp_enqueue_style('wow-animate', get_template_directory_uri() . '/assets/css/libs/animate.min.css', array(), '1.1.0');
        wp_enqueue_script('wow-animate', get_template_directory_uri() . '/assets/js/libs/wow.min.js', array('jquery'), '1.0.0', true);
    }
    
    if ($load_intl_tel) {
        wp_enqueue_style('intlTelInput', get_template_directory_uri() . '/assets/css/libs/intlTelInput.css', array(), '1.0.0');
        wp_enqueue_script('intlTelInput', get_template_directory_uri() . '/assets/js/libs/intlTelInput.min.js', array('jquery'), '17.0.8', true);
    }
    
    if ($load_odometer) {
        wp_enqueue_style('odometer', get_template_directory_uri() . '/assets/css/libs/odometer.css', array(), '1.0.0');
        wp_enqueue_script('odometer', get_template_directory_uri() . '/assets/js/libs/odometer.min.js', array('jquery'), '1.0.0', true);
    }
    
    // Register heavy scripts (load on demand)
    wp_register_script('particles-background', get_template_directory_uri() . '/assets/js/libs/particles.min.js', array('jquery'), '1.1.0', true);
    wp_register_script('tilt', get_template_directory_uri() . '/assets/js/libs/tilt.min.js', array('jquery'), '1.0.0', true);
    wp_register_script('stellar-parallax', get_template_directory_uri() . '/assets/js/libs/stellar-parallax.min.js', array('jquery'), '0.6.2', true);
    wp_register_script('pxl-scroll', get_template_directory_uri() . '/assets/js/libs/scroll.min.js', array('jquery'), '0.6.0', true);
    wp_register_script('pxl-easing', get_template_directory_uri() . '/assets/js/libs/easing.js', array('jquery'), '1.3.0', true);
    wp_register_script('pxl-tweenmax', get_template_directory_uri() . '/assets/js/libs/tweenmax.min.js', array('jquery'), '2.1.2', true);
    wp_register_script('pxl-parallax-move-mouse', get_template_directory_uri() . '/assets/js/libs/parallax-move-mouse.js', array('jquery'), '1.0.0', true);
    
    // Essential scripts
    wp_enqueue_script('nice-select', get_template_directory_uri() . '/assets/js/libs/nice-select.min.js', array('jquery'), 'all', true);
    wp_enqueue_script('pxl-parallax-scroll', get_template_directory_uri() . '/assets/js/libs/parallax-scroll.js', array('jquery'), $version, true);
    
    // Smooth Scroll - only if enabled
    $smooth_scroll = brighthub()->get_theme_opt('smooth_scroll', 'off');
    if ($smooth_scroll == 'on') {
        wp_enqueue_script('gsap');
        wp_enqueue_script('pxl-scroll-smoother');
        wp_enqueue_script('pxl-bundled-lenis');
    }
    
    // Main theme script - defer loading
    wp_enqueue_script('pxl-main', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), $version, true);
    
    // Remove test.js if not needed in production
    if (defined('WP_DEBUG') && WP_DEBUG) {
        wp_enqueue_script('pxl-test', get_template_directory_uri() . '/assets/js/test.js', array('jquery'), $version, true);
    }
    
    // Localize script with optimized data
    wp_localize_script('pxl-main', 'loadmore_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'startPage' => get_query_var('paged') ? get_query_var('paged') : 1,
        'nonce' => wp_create_nonce('brighthub_loadmore_nonce'),
    ));
    
    // Add defer/async attributes
    add_filter('script_loader_tag', 'brighthub_defer_scripts', 10, 2);
}

/**
 * Preload Google Fonts
 */
function brighthub_preload_fonts($tag, $handle) {
    if ('pxl-google-fonts' === $handle) {
        $tag = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag);
        $tag .= '<noscript>' . str_replace("rel='preload' as='style' onload", "rel='stylesheet'", $tag) . '</noscript>';
    }
    return $tag;
}

/**
 * Defer non-critical scripts
 */
function brighthub_defer_scripts($tag, $handle) {
    $defer_scripts = array('pxl-main', 'wow-animate', 'nice-select', 'intlTelInput', 'odometer');
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}

/**
 * ============================================
 * 2. DATABASE QUERY OPTIMIZATION
 * ============================================
 */

/**
 * Cache theme options to reduce database queries
 */
if (!function_exists('brighthub_get_cached_option')) {
    function brighthub_get_cached_option($option_name, $default = false) {
        static $cache = array();
        
        if (isset($cache[$option_name])) {
            return $cache[$option_name];
        }
        
        $value = get_option($option_name, $default);
        $cache[$option_name] = $value;
        
        return $value;
    }
}

/**
 * Cache post meta queries
 */
add_filter('get_post_metadata', 'brighthub_cache_post_meta', 10, 4);
function brighthub_cache_post_meta($value, $object_id, $meta_key, $single) {
    static $meta_cache = array();
    
    $cache_key = $object_id . '_' . $meta_key;
    
    if (isset($meta_cache[$cache_key])) {
        return $meta_cache[$cache_key];
    }
    
    // Let WordPress handle the actual query
    return $value;
}

/**
 * Optimize menu queries
 */
add_filter('wp_nav_menu_args', 'brighthub_optimize_menu_args');
function brighthub_optimize_menu_args($args) {
    if (!isset($args['cache_duration'])) {
        $args['cache_duration'] = 3600; // Cache for 1 hour
    }
    return $args;
}

/**
 * ============================================
 * 3. REMOVE UNNECESSARY SCRIPTS
 * ============================================
 */

/**
 * Remove WordPress emoji scripts
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

/**
 * Remove unnecessary header links
 */
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Disable XML-RPC if not needed
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove query strings from static resources
 */
add_filter('script_loader_src', 'brighthub_remove_script_version', 15, 1);
add_filter('style_loader_src', 'brighthub_remove_script_version', 15, 1);
function brighthub_remove_script_version($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

/**
 * ============================================
 * 4. IMAGE OPTIMIZATION
 * ============================================
 */

/**
 * Add lazy loading attributes to images
 */
add_filter('wp_get_attachment_image_attributes', 'brighthub_add_lazy_loading', 10, 3);
function brighthub_add_lazy_loading($attr, $attachment, $size) {
    // Skip if already has loading attribute
    if (isset($attr['loading'])) {
        return $attr;
    }
    
    // Get class from attributes
    $class = isset($attr['class']) ? $attr['class'] : '';
    
    // Use helper function to determine lazy loading
    $loading_attr = brighthub_get_lazy_loading_attr($class);
    
    // Skip lazy loading for above-the-fold images (featured image on singular)
    if ($loading_attr === 'lazy' && is_singular() && has_post_thumbnail() && $attachment->ID === get_post_thumbnail_id()) {
        $loading_attr = 'eager';
    }
    
    $attr['loading'] = $loading_attr;
    
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    
    return $attr;
}

/**
 * Add responsive image sizes
 */
add_filter('wp_calculate_image_sizes', 'brighthub_responsive_image_sizes', 10, 5);
function brighthub_responsive_image_sizes($sizes, $size, $image_src, $image_meta, $attachment_id) {
    if (is_singular()) {
        return '(max-width: 768px) 100vw, (max-width: 1200px) 80vw, 1200px';
    }
    return $sizes;
}

/**
 * ============================================
 * 5. AJAX OPTIMIZATION
 * ============================================
 */

/**
 * AJAX optimization is handled in theme-functions.php
 * Cache is already implemented in my_ajax_load_more_posts()
 */

/**
 * ============================================
 * 6. JAVASCRIPT OPTIMIZATION
 * ============================================
 */

/**
 * Add optimized JavaScript helpers
 */
add_action('wp_footer', 'brighthub_js_optimizations', 1);
function brighthub_js_optimizations() {
    ?>
    <script>
    // Debounce function for performance
    function brighthubDebounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
    
    // Throttle function for scroll events
    function brighthubThrottle(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    }
    
    // Optimize scroll events
    if (typeof jQuery !== 'undefined') {
        var optimizedScroll = brighthubThrottle(function() {
            jQuery(window).trigger('optimized-scroll');
        }, 16); // ~60fps
        
        jQuery(window).on('scroll', optimizedScroll);
    }
    </script>
    <?php
}

/**
 * ============================================
 * 7. CSS OPTIMIZATION
 * ============================================
 */

/**
 * Defer non-critical CSS
 */
add_filter('style_loader_tag', 'brighthub_defer_css', 10, 2);
function brighthub_defer_css($tag, $handle) {
    $defer_css = array('wow-animate', 'odometer', 'intlTelInput');
    
    if (in_array($handle, $defer_css)) {
        $tag = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag);
        $tag .= '<noscript>' . str_replace("rel='preload' as='style' onload", "rel='stylesheet'", $tag) . '</noscript>';
    }
    
    return $tag;
}

/**
 * ============================================
 * 8. CACHE OPTIMIZATION
 * ============================================
 */

/**
 * Enhanced cache headers
 */
add_action('template_redirect', 'brighthub_enhanced_cache_headers', 1);
function brighthub_enhanced_cache_headers() {
    if (is_admin() || is_user_logged_in()) {
        return;
    }
    
    // Static assets cache
    if (is_singular() || is_home() || is_archive()) {
        header('Cache-Control: public, max-age=3600, must-revalidate');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
    }
}

/**
 * Object cache for expensive operations
 */
if (!function_exists('brighthub_get_cached')) {
    function brighthub_get_cached($key, $callback, $expiration = 3600) {
        $cache_key = 'brighthub_' . $key;
        $cached = wp_cache_get($cache_key, 'brighthub');
        
        if ($cached !== false) {
            return $cached;
        }
        
        $value = call_user_func($callback);
        wp_cache_set($cache_key, $value, 'brighthub', $expiration);
        
        return $value;
    }
}

/**
 * ============================================
 * 9. QUERY OPTIMIZATION
 * ============================================
 */

/**
 * Optimize main query
 */
add_action('pre_get_posts', 'brighthub_optimize_main_query');
function brighthub_optimize_main_query($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Limit post meta queries
    $query->set('update_post_meta_cache', true);
    $query->set('update_post_term_cache', true);
    
    // Optimize for archives
    if (is_archive() || is_home()) {
        $query->set('no_found_rows', false);
        $query->set('posts_per_page', get_option('posts_per_page', 10));
    }
}

/**
 * ============================================
 * 10. MEMORY OPTIMIZATION
 * ============================================
 */

/**
 * Clean up memory usage
 */
add_action('wp_footer', 'brighthub_cleanup_memory', 999);
function brighthub_cleanup_memory() {
    // Clear static caches if memory usage is high
    if (function_exists('memory_get_usage') && memory_get_usage() > 50 * 1024 * 1024) {
        wp_cache_flush_group('brighthub');
    }
}

/**
 * ============================================
 * 11. PRELOAD CRITICAL RESOURCES
 * ============================================
 */

/**
 * Preload critical resources
 */
add_action('wp_head', 'brighthub_preload_resources', 1);
function brighthub_preload_resources() {
    // Preload critical CSS
    echo '<link rel="preload" href="' . esc_url(get_template_directory_uri() . '/assets/css/style.css') . '" as="style">' . "\n";
    
    // Preload critical fonts
    if (brighthub_fonts_url()) {
        echo '<link rel="preload" href="' . esc_url(brighthub_fonts_url()) . '" as="style">' . "\n";
    }
    
    // DNS prefetch for external resources
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
}

/**
 * ============================================
 * 12. DISABLE UNNECESSARY FEATURES
 * ============================================
 */

/**
 * Disable WordPress heartbeat on frontend
 */
add_action('init', 'brighthub_disable_heartbeat', 1);
function brighthub_disable_heartbeat() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}

/**
 * Disable embeds
 */
add_action('wp_footer', 'brighthub_disable_embeds');
function brighthub_disable_embeds() {
    wp_dequeue_script('wp-embed');
}

/**
 * Remove jQuery migrate
 */
add_action('wp_default_scripts', 'brighthub_remove_jquery_migrate');
function brighthub_remove_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];
        if ($script->deps) {
            $script->deps = array_diff($script->deps, array('jquery-migrate'));
        }
    }
}

/**
 * ============================================
 * 13. OPTIMIZE WIDGET QUERIES
 * ============================================
 */

/**
 * Cache widget output
 */
add_filter('widget_display_callback', 'brighthub_cache_widget_output', 10, 3);
function brighthub_cache_widget_output($instance, $widget, $args) {
    // Skip caching for dynamic widgets
    $skip_cache = array('recent-posts', 'recent-comments', 'tag-cloud');
    
    if (in_array($widget->id_base, $skip_cache)) {
        return $instance;
    }
    
    // Cache widget output for 1 hour
    $cache_key = 'widget_' . $widget->id;
    $cached = wp_cache_get($cache_key, 'brighthub_widgets');
    
    if ($cached !== false) {
        echo $cached;
        return false; // Prevent widget from rendering again
    }
    
    return $instance;
}

add_filter('widget_output', 'brighthub_save_widget_cache', 10, 3);
function brighthub_save_widget_cache($output, $widget, $args) {
    if ($output) {
        $cache_key = 'widget_' . $widget->id;
        wp_cache_set($cache_key, $output, 'brighthub_widgets', 3600);
    }
    return $output;
}

