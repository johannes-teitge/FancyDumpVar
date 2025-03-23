<?php
namespace FDV;

if ( ! defined( 'ABSPATH' ) ) exit;

class Assets {

    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    public static function enqueue_assets() {
        $timestamp = date('Y-m-d H:i:s');

        // Optionen laden
        $options = get_option('fdv_plugin_options', []);
        $css_filename = $options['customCssFile'] ?? 'FancyDumpVar.css';

        // CSS
        $css_file = 'src/assets/css/' . $css_filename;
        $css_path = FDV_PLUGIN_DIR . $css_file;
        $css_url  = FDV_PLUGIN_URL . $css_file;

        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'fancy-dumpvar-style',
                $css_url,
                [],
                filemtime( $css_path )
            );

            if ( defined( 'FDV_DEV' ) && FDV_DEV ) {
                error_log("[$timestamp] ✅ [INFO] FDV Plugin: CSS '$css_filename' erfolgreich eingebunden.");
            }
        } else {
            error_log("[$timestamp] ❌ [ERROR] FDV Plugin: CSS-Datei '$css_filename' nicht gefunden.");
        }

        // FancyDumpVar.js
        self::enqueue_js_file('FancyDumpVar.js', 'fancy-dumpvar-script');

        // mark.js
        self::enqueue_js_file('mark.js', 'fancy-dumpvar-mark');
    }

    /**
     * Hilfsfunktion zum Einbinden von JS-Dateien
     */
    private static function enqueue_js_file(string $filename, string $handle) {
        $timestamp = date('Y-m-d H:i:s');

        $js_file = 'src/assets/js/' . $filename;
        $js_path = FDV_PLUGIN_DIR . $js_file;
        $js_url  = FDV_PLUGIN_URL . $js_file;

        if ( file_exists( $js_path ) ) {
            wp_enqueue_script(
                $handle,
                $js_url,
                [],
                filemtime( $js_path ),
                true // im Footer einfügen
            );

            if ( defined( 'FDV_DEV' ) && FDV_DEV ) {
                error_log("[$timestamp] ✅ [INFO] FDV Plugin: JS '$filename' erfolgreich eingebunden.");
            }
        } else {
            error_log("[$timestamp] ❌ [ERROR] FDV Plugin: JS-Datei '$filename' nicht gefunden.");
        }
    }
}
