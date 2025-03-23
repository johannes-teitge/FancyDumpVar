<?php
/**
 * FDV Debug Helper v2 – mit optimierten Icons & phpinfo-Support
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function fdv_collect_debug_info() {
    global $wp, $wp_query, $post, $wp_filter;

    // Volle URL inkl. Parameter
    $full_url   = home_url(add_query_arg(null, null));
    $parsed_url = parse_url($full_url);
    $parsed_params = [];
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $parsed_params);
    }

    return [

        // 🌐 Web & Request
        '🌐 Request Info' => [
            '🌍 Full URL'         => $full_url,
            '🔎 Parsed URL Info'  => [
                '🏠 Host'   => $parsed_url['host'] ?? '',
                '📄 Path'   => $parsed_url['path'] ?? '',
                '❓ Query'  => $parsed_url['query'] ?? '',
                '📦 Params' => $parsed_params,
            ],
            '📥 REQUEST_URI'      => $_SERVER['REQUEST_URI'] ?? '',
            '↩️ HTTP_REFERER'     => $_SERVER['HTTP_REFERER'] ?? '',
            '🧭 User Agent'       => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ],

        // 👤 Benutzer
        '👤 User Context' => [
            '🆔 User ID'           => get_current_user_id(),
            '🙋‍♂️ User Object'     => wp_get_current_user(),
            '🔐 Roles'             => wp_get_current_user()->roles,
            '🛡️ Capabilities'      => wp_get_current_user()->allcaps,
            '🖥️ is_admin()'        => is_admin(),
            '⚡ is_ajax()'         => defined('DOING_AJAX') && DOING_AJAX,
            '⏰ is_cron()'         => defined('DOING_CRON') && DOING_CRON,
        ],

        // 🧩 WordPress Umgebung
        '🧩 WP Environment' => [
            '📄 Post Object'      => $post,
            '🔍 WP Query'         => $wp_query,
            '🧱 WP Object'        => $wp,
            '🎯 Current Action'   => current_action(),
            '🧩 Current Filter'   => current_filter(),
            '🔧 Hooks (keys)'     => array_keys($wp_filter ?? []),
        ],

        // 🎨 Template & Theme
        '🎨 Theme & Template' => [
            '🖼️ Active Theme'     => wp_get_theme()->get('Name'),
            '🗂️ Template File'    => [
                '📁 Path'   => get_page_template(),
                '✅ Exists' => file_exists(get_page_template()),
            ],
            '🧾 Template Dir'     => get_template_directory(),
            '🎨 Stylesheet Dir'   => get_stylesheet_directory(),
        ],

        // ⚙️ System & Server
        '⚙️ System Info' => [
            '🐘 PHP Version'         => phpversion(),
            '📦 Loaded Extensions'   => get_loaded_extensions(),
            '💾 Memory Limit'        => ini_get('memory_limit'),
            '⏱️ Max Execution Time'  => ini_get('max_execution_time'),
            '📤 Upload Max Filesize' => ini_get('upload_max_filesize'),
            '📁 ABSPATH'             => ABSPATH,
            '🔌 WP_PLUGIN_DIR'       => WP_PLUGIN_DIR,
            '📂 Upload Dir Info'     => wp_get_upload_dir(),
            '🧾 Raw phpinfo() HTML::html' => fdv_get_phpinfo_html(),
        ],

        // 📦 Laufzeit & Plugins
        '📦 Runtime Info' => [
            '🔌 Active Plugins'      => get_option('active_plugins'),
            '📊 Memory Usage (MB)'   => round(memory_get_usage() / 1048576, 2),
            '📈 Memory Peak (MB)'    => round(memory_get_peak_usage() / 1048576, 2),
        ],

        // 🐞 Debug Log
        '🐞 Debug Log (last 10 lines)' => fdv_get_last_debug_log_lines(10),
    ];
}

function fdv_get_last_debug_log_lines($lines = 10) {
    $log_path = WP_CONTENT_DIR . '/debug.log';

    if (!file_exists($log_path)) {
        return '📭 debug.log not found.';
    }

    $file = @file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($file === false) {
        return '🚫 Unable to read debug.log.';
    }

    return array_slice($file, -$lines);
}

function fdv_get_phpinfo_html() {
    ob_start();
    phpinfo();
    $html = ob_get_clean();

    // Entferne alle <style>- und <body>-tags
    $html = preg_replace('#<style[^>]*>.*?</style>#is', '', $html);
    $html = preg_replace('#<(html|head|body)[^>]*>#is', '', $html);
    $html = preg_replace('#</(html|head|body)>#is', '', $html);

    return '<div id="fdv-phpinfo">' . $html . '</div>';
}
