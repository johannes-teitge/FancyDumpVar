<?php
/**
 * Plugin Name: FDV Plugin
 * Plugin URI: https://teitge.de
 * Description: Eine Debug-Utility zur eleganten Ausgabe von Variablen – ideal für die Modulentwicklung.
 * Version: 2.5.6
 * Author: Johannes Teitge
 * Author URI: https://teitge.de
 * License: GPL-3.0-or-later
 *
 * FancyDumpVar
 *
 * Eine Utility-Klasse zur eleganten Ausgabe und Darstellung von Variablen.
 *
 * Version: 2.5.6
 * Datum: 2025-03-14
 *
 * Copyright (C) 2025 Johannes Teitge <johannes@teitge.de>
 *
 * Dieses Programm ist freie Software: Du kannst es unter den Bedingungen der
 * GNU General Public License, wie von der Free Software Foundation veröffentlicht,
 * entweder Version 3 der Lizenz oder (nach Deiner Wahl) jeder späteren Version,
 * weiterverbreiten und/oder modifizieren.
 *
 * Dieses Programm wird in der Hoffnung, dass es nützlich sein wird, aber OHNE
 * JEDE GEWÄHRLEISTUNG bereitgestellt; auch ohne die implizite Garantie der
 * MARKTREIFE oder der VERWENDBARKEIT FÜR EINEN BESTIMMTEN ZWECK.
 * Lies die GNU General Public License für weitere Details.
 *
 * Du solltest eine Kopie der GNU General Public License zusammen mit diesem Programm
 * erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.
 */


/* Quick and Dirty Version 
// Einbinden der CSS-Datei ohne Version und modularen Ansatz
wp_enqueue_style('fancy-dumpvar-style', plugin_dir_url(__FILE__) . 'src/FancyDumpVar.css');

// Einbinden der JavaScript-Datei FancyDumpVar.js ohne Version und modularen Ansatz
wp_enqueue_script('fancy-dumpvar-script', plugin_dir_url(__FILE__) . 'src/FancyDumpVar.js', array(), '', true);

// Einbinden der JavaScript-Datei mark.js ohne Version und modularen Ansatz
wp_enqueue_script('fancy-dumpvar-mark', plugin_dir_url(__FILE__) . 'src/mark.js', array(), '', true);

*/


// Sicherheitsabfrage: Direkter Zugriff verhindern.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definiert den Entwicklungsmodus. Setze dies auf true, wenn du im Entwicklungsmodus bist.
if ( ! defined( 'FDV_DEV' ) ) {
    define( 'FDV_DEV', true ); // true für Entwicklungsmodus, false für Produktionsmodus
}

// Definiert den Plugin-Pfad und URL für einfache Nutzung
define( 'FDV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FDV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


// Überprüfen, ob die Datei existiert und dann einbinden
$fdv_file = FDV_PLUGIN_DIR . 'src/FancyDumpVar.php';

$timestamp = date('Y-m-d H:i:s');  // Aktueller Zeitstempel

// Emojis für verschiedene Status
$success_icon = "✅";  // Erfolg
$error_icon = "❌";    // Fehler
$info_icon = "ℹ️";    // Information 

if (file_exists($fdv_file)) {
    // Überprüfen, ob die Klasse bereits geladen ist
    if (class_exists('\FancyDumpVar\FancyDumpVar')) {
        // Loggen, dass die Klasse bereits geladen wurde
        if (FDV_DEV === true) {
            error_log("[$timestamp] [$info_icon] [INFO] FDV Plugin: Klasse 'FancyDumpVar' ist bereits geladen. Keine erneute Einbindung erforderlich.");
        }
    } else {
        // Falls die Klasse noch nicht geladen wurde, einbinden
        require_once $fdv_file;
        
        // Überprüfen, ob die Klasse nach dem Laden verfügbar ist
        if (class_exists('\FancyDumpVar\FancyDumpVar')) {
            // Nur loggen, wenn FDV_DEV auf true gesetzt ist
            if (FDV_DEV === true) {
                error_log("[$timestamp] [$success_icon] [INFO] FDV Plugin: Klasse 'FancyDumpVar' erfolgreich geladen aus der Datei: $fdv_file");
            }
        } else {
            error_log("[$timestamp] [$error_icon] [ERROR] FDV Plugin: Klasse 'FancyDumpVar' konnte nicht geladen werden aus der Datei: $fdv_file");
        }
    }
} else {
    error_log("[$timestamp] [$error_icon] [ERROR] FDV Plugin: 'FancyDumpVar.php' nicht gefunden unter: $fdv_file");
}


// Funktion zum Enqueue von CSS und JS
function fdv_enqueue_assets() {
    // Debugging: Loggen, dass die Funktion aufgerufen wurde
    if ( defined( 'FDV_DEV' ) && FDV_DEV === true ) {
        $timestamp = date('Y-m-d H:i:s');
        error_log("[$timestamp] [ℹ️] [INFO] FDV Plugin: Enqueue-Assets-Funktion wurde aufgerufen.");
    }

    // CSS-Dateipfad relativ zum Plugin-Verzeichnis
    $css_file = 'src/assets/css/FancyDumpVar.css';
    $css_url = FDV_PLUGIN_URL . $css_file;
    if ( file_exists( FDV_PLUGIN_DIR . $css_file ) ) {
        wp_enqueue_style(
            'fancy-dumpvar-style',      // Handle
            $css_url,                   // URL der CSS-Datei
            array(),                     // Abhängigkeiten (falls vorhanden)
            filemtime( FDV_PLUGIN_DIR . $css_file ) // Version basierend auf Dateizeitstempel
        );
        // Debugging: Erfolgreiches Einbinden der CSS-Datei
        if ( defined( 'FDV_DEV' ) && FDV_DEV === true ) {
            error_log("[$timestamp] [✅] [INFO] FDV Plugin: CSS-Datei 'FancyDumpVar.css' erfolgreich eingebunden.");
        }
    } else {
        error_log("[$timestamp] [❌] [ERROR] FDV Plugin: FancyDumpVar.css nicht gefunden.");
    }

    // JavaScript-Dateipfad relativ zum Plugin-Verzeichnis
    $js_file_fancy_dump_var = 'src/assets/js/FancyDumpVar.js';
    $js_url_fancy_dump_var = FDV_PLUGIN_URL . $js_file_fancy_dump_var;
    if ( file_exists( FDV_PLUGIN_DIR . $js_file_fancy_dump_var ) ) {
        wp_enqueue_script(
            'fancy-dumpvar-script',           // Handle
            $js_url_fancy_dump_var,           // URL der JS-Datei
            array(),                          // Abhängigkeiten (falls vorhanden)
            filemtime( FDV_PLUGIN_DIR . $js_file_fancy_dump_var ), // Version basierend auf Dateizeitstempel
            true                               // Am Ende der Seite einfügen
        );
        // Debugging: Erfolgreiches Einbinden der JavaScript-Datei
        if ( defined( 'FDV_DEV' ) && FDV_DEV === true ) {
            error_log("[$timestamp] [✅] [INFO] FDV Plugin: JS-Datei 'FancyDumpVar.js' erfolgreich eingebunden.");
        }
    } else {
        error_log("[$timestamp] [❌] [ERROR] FDV Plugin: FancyDumpVar.js nicht gefunden.");
    }

    // JavaScript-Dateipfad relativ zum Plugin-Verzeichnis
    $js_file_mark = 'src/assets/js/mark.js';
    $js_url_mark = FDV_PLUGIN_URL . $js_file_mark;
    if ( file_exists( FDV_PLUGIN_DIR . $js_file_mark ) ) {
        wp_enqueue_script(
            'fancy-dumpvar-mark',             // Handle
            $js_url_mark,                     // URL der JS-Datei
            array(),                          // Abhängigkeiten (falls vorhanden)
            filemtime( FDV_PLUGIN_DIR . $js_file_mark ), // Version basierend auf Dateizeitstempel
            true                               // Am Ende der Seite einfügen
        );
        // Debugging: Erfolgreiches Einbinden der mark.js-Datei
        if ( defined( 'FDV_DEV' ) && FDV_DEV === true ) {
            error_log("[$timestamp] [✅] [INFO] FDV Plugin: JS-Datei 'mark.js' erfolgreich eingebunden.");
        }
    } else {
        error_log("[$timestamp] [❌] [ERROR] FDV Plugin: mark.js nicht gefunden.");
    }

}

// Die Funktion an den 'wp_enqueue_scripts'-Hook anhängen
add_action( 'wp_enqueue_scripts', 'fdv_enqueue_assets' );

// Falls im Admin-Bereich, dann auch für den Adminbereich:
add_action( 'admin_enqueue_scripts', 'fdv_enqueue_assets' );
