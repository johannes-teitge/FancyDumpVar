<?php
namespace FDV;

if ( ! defined( 'ABSPATH' ) ) exit;

class Core {

    public static function init() {
        // Plugin-Deaktivierungs-Hook registrieren
        register_deactivation_hook( FDV_PLUGIN_DIR . 'fdv-plugin.php', [ __CLASS__, 'on_deactivate' ] );

        self::load_fdv_class();
    }

    /**
     * Wird beim Deaktivieren des Plugins aufgerufen
     */
    public static function on_deactivate() {
        delete_option( 'fdv_plugin_options' );

        // Log-Datei zur Nachverfolgung anlegen
        file_put_contents( FDV_PLUGIN_DIR . 'fdv-deaktiviert.txt', "Plugin wurde deaktiviert am: " . date('c') . "\n", FILE_APPEND );
        error_log('[FDV Plugin] Plugin wurde deaktiviert und Einstellungen wurden gelöscht.');
    }

    /**
     * Lädt die FancyDumpVar-Klasse und legt die Instanz in $GLOBALS['FDV'] ab
     */
    private static function load_fdv_class() {
        $fdv_file = FDV_PLUGIN_DIR . 'src/FancyDumpVar.php';
        $timestamp = date('Y-m-d H:i:s');

        if ( file_exists( $fdv_file ) ) {
            if ( ! class_exists( '\FancyDumpVar\FancyDumpVar' ) ) {
                require_once $fdv_file;
            }

            if ( class_exists( '\FancyDumpVar\FancyDumpVar' ) ) {
                if ( ! isset( $GLOBALS['FDV'] ) ) {
                    $GLOBALS['FDV'] = new \FancyDumpVar\FancyDumpVar();

                    // Optionen aus WP-Settings anwenden
                    $options = get_option( 'fdv_plugin_options' );
                    if ( is_array( $options ) ) {
                        $map = [
                            'Title'                      => 'Title',
                            'language'                   => 'language',
                            'fdv_plugin_dump_wrapper_style' => 'dumpWrapperStyle',
							'dumpContainerMaxHeight'        => 'dumpContainerMaxHeight', // <== NEU							
                        ];

                        foreach ( $map as $key => $optionName ) {
                            if ( isset( $options[$key] ) ) {
                                $GLOBALS['FDV']->setOption( $optionName, $options[$key] );
                            }
                        }
                    }

                    // Optional: Auto-Dump Backend
                    if ( ! empty( $options['fdv_plugin_auto_view_admin'] ) ) {
                        add_action( 'admin_notices', function () use ( $options ) {
                            if ( isset( $GLOBALS['FDV'] ) ) {

                                require_once FDV_PLUGIN_DIR . 'includes/debug-helper.php';                                
                            //    $GLOBALS['FDV']->dump( $options );
                                /*
                                $GLOBALS['FDV']->dump( $_GET );
                                $GLOBALS['FDV']->dump( $_POST );
                                $GLOBALS['FDV']->dump( $_REQUEST );
                                $GLOBALS['FDV']->dump( $_SERVER );
                                $GLOBALS['FDV']->dump( $_SESSION ?? [] );
                                $GLOBALS['FDV']->dump( $_COOKIE );
                                $GLOBALS['FDV WP-Overview'] = fdv_collect_debug_info();
                                $GLOBALS['FDV']->dump( $GLOBALS['FDV WP-Overview'] );      
                                */
                                
                                if (!empty($options['auto_dump_get'])) {
                                    $GLOBALS['FDV']->dump($_GET);
                                }
                                if (!empty($options['auto_dump_post'])) {
                                    $GLOBALS['FDV']->dump($_POST);
                                }
                                if (!empty($options['auto_dump_request'])) {
                                    $GLOBALS['FDV']->dump($_REQUEST);
                                }
                                if (!empty($options['auto_dump_server'])) {
                                    $GLOBALS['FDV']->dump($_SERVER);
                                }
                                if (!empty($options['auto_dump_session'])) {
                                    $GLOBALS['FDV']->dump($_SESSION ?? []);
                                }
                                if (!empty($options['auto_dump_cookie'])) {
                                    $GLOBALS['FDV']->dump($_COOKIE);
                                }
                                if (!empty($options['auto_dump_wp_info'])) {
                                    $GLOBALS['FDV WP-Overview'] = fdv_collect_debug_info();
                                    $GLOBALS['FDV']->dump($GLOBALS['FDV WP-Overview']);
                                }





                                $GLOBALS['FDV']->dumpOut();
                            }
                        });
                    }

                    // Optional: Auto-Dump Frontend
                    if ( ! empty( $options['fdv_plugin_auto_view_frontend'] ) ) {
                        add_action( 'wp_footer', function () {
                            if ( isset( $GLOBALS['FDV'] ) ) {
                                $GLOBALS['FDV']->dump( [ is_user_logged_in(), 'User logged in' ] );
                                $GLOBALS['FDV']->dumpOut();
                            }
                        });
                    }
                }

                // Log nur im Entwicklungsmodus
                if ( defined( 'FDV_DEV' ) && FDV_DEV === true ) {
                    error_log("[$timestamp] ✅ [INFO] FDV Plugin: FancyDumpVar erfolgreich geladen.");
                }
            } else {
                error_log("[$timestamp] ❌ [ERROR] FDV Plugin: Klasse 'FancyDumpVar' wurde nicht gefunden.");
            }
        } else {
            error_log("[$timestamp] ❌ [ERROR] FDV Plugin: Datei '$fdv_file' nicht gefunden.");
        }
    }





}
