<?php
/**
 * Plugin Name: FDV Plugin
 * Plugin URI: https://teitge.de
 * Description: Eine Debug-Utility zur eleganten Ausgabe von Variablen – ideal für die Modulentwicklung.
 * Version: 2.5.7
 * Author: Johannes Teitge
 * Author URI: https://teitge.de
 * License: GPL-3.0-or-later
 *
 * FancyDumpVar – Eine Utility-Klasse zur eleganten Ausgabe und Darstellung von Variablen.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin-Konstanten definieren
define( 'FDV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FDV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Entwicklungsmodus aktivieren/deaktivieren
if ( ! defined( 'FDV_DEV' ) ) {
    define( 'FDV_DEV', true ); // true = Entwicklermodus, false = Produktionsmodus
}

// Loader einbinden und starten
require_once FDV_PLUGIN_DIR . 'includes/loader.php';
\FDV\Loader::init();
