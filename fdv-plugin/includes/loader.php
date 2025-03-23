<?php
namespace FDV;

if ( ! defined( 'ABSPATH' ) ) exit;

class Loader {

    public static function init() {
        // Benötigte Dateien einbinden
        require_once FDV_PLUGIN_DIR . 'includes/class-fdv-core.php';
        require_once FDV_PLUGIN_DIR . 'includes/class-fdv-assets.php';
        require_once FDV_PLUGIN_DIR . 'includes/class-fdv-settings.php';

        // Module initialisieren
        Core::init();
        Assets::init();
        Settings::init();
    }
}
