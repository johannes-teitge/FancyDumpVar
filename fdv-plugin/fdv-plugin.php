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
 * FancyDumpVar
 *
 * Eine Utility-Klasse zur eleganten Ausgabe und Darstellung von Variablen.
 *
 * Version: 2.5.7
 * Datum: 2025-03-22
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

            // Nun globale Variable anlegen
            // Globale Instanz von FancyDumpVar erstellen
            if ( ! isset( $GLOBALS['FDV'] ) ) {
                $GLOBALS['FDV'] = new \FancyDumpVar\FancyDumpVar();

                // Holen der Optionen aus den WordPress-Settings
                $options = get_option('fdv_plugin_options');   
                
                // Optionen aus den Settings verwenden
                if (isset($options['Title'])) {
                    $GLOBALS['FDV']->setOption('Title', $options['Title']);
                }  
                
                // Optionen aus den Settings verwenden
                if (isset($options['language'])) {
                    $GLOBALS['FDV']->setOption('language', $options['language']);
                }       
 
                // Optionen aus den Settings verwenden
                if (isset($options['fdv_plugin_dump_wrapper_style'])) {
                    $GLOBALS['FDV']->setOption('dumpWrapperStyle', $options['fdv_plugin_dump_wrapper_style']);
                }                    
                
                $GLOBALS['FDV']->dump($options);


            }            


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

    // Holen der Optionen aus den WordPress-Settings
    $options = get_option('fdv_plugin_options');      
    if (isset($options['customCssFile'])) {    
      $css_filename = $options['customCssFile'];
    } else {
      $css_filename  = 'FancyDumpVar.css';
    }  

    // CSS-Dateipfad relativ zum Plugin-Verzeichnis
    $css_file = 'src/assets/css/'.$css_filename;
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



// ******************************* SETTINGS ************************************

// Menü im Adminbereich hinzufügen
function fdv_plugin_add_admin_menu() {
    // Menüpunkt für das Plugin hinzufügen
    add_menu_page(
        'FDV Plugin Settings', // Der Titel der Seite
        'FDV Plugin',          // Der Titel des Menüs
        'manage_options',      // Die Berechtigung, um den Menüpunkt zu sehen
        'fdv_plugin_settings', // Der Slug für die Seite
        'fdv_plugin_settings_page', // Die Callback-Funktion für das Anzeigen der Seite
        'dashicons-admin-generic'  // Das Icon des Menüpunkts
    );
}
add_action('admin_menu', 'fdv_plugin_add_admin_menu');

// Die Seite für die Einstellungen
function fdv_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('FDV Plugin Settings', 'fdv_plugin'); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('fdv_plugin_options_group');
            do_settings_sections('fdv_plugin_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Einstellungen registrieren
function fdv_plugin_settings_init() {
    // Einstellungen registrieren
    register_setting('fdv_plugin_options_group', 'fdv_plugin_options', 'fdv_plugin_options_sanitize');

    // Sektion hinzufügen
    add_settings_section(
        'fdv_plugin_general_settings',
        'General Settings',
        'fdv_plugin_settings_section_cb',
        'fdv_plugin_settings'
    );

    // Optionen hinzufügen
    add_settings_field(
        'fdv_plugin_language',        // ID der Option
        'Language',                   // Titel der Option
        'fdv_plugin_language_cb',     // Callback-Funktion
        'fdv_plugin_settings',        // Seite
        'fdv_plugin_general_settings' // Sektion
    );

    add_settings_field(
        'fdv_plugin_max_recursion_depth',
        'Max Recursion Depth',
        'fdv_plugin_max_recursion_depth_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_max_depth',
        'Max Depth',
        'fdv_plugin_max_depth_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_max_execution_time',
        'Max Execution Time (seconds)',
        'fdv_plugin_max_execution_time_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_max_elements_per_level',
        'Max Elements Per Level',
        'fdv_plugin_max_elements_per_level_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_sort_properties_and_methods',
        'Sort Properties and Methods',
        'fdv_plugin_sort_properties_and_methods_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_show_time_info',
        'Show Time Information',
        'fdv_plugin_show_time_info_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_title',
        'Title',
        'fdv_plugin_title_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_dump_wrapper',      // ID der Option
        'Enable Dump Wrapper',          // Titel der Option
        'fdv_plugin_dump_wrapper_cb',   // Callback-Funktion
        'fdv_plugin_settings',          // Seite
        'fdv_plugin_general_settings'   // Sektion
    );

    add_settings_field(
        'fdv_plugin_dump_wrapper_style',
        'Dump Wrapper Style',
        'fdv_plugin_dump_wrapper_style_cb',
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );

    add_settings_field(
        'fdv_plugin_custom_css_file',
        'Template',
        'fdv_plugin_custom_css_file_cb', // Separate Callback für das Dropdown
        'fdv_plugin_settings',
        'fdv_plugin_general_settings'
    );
}
add_action('admin_init', 'fdv_plugin_settings_init');

// Callback für die Sektion
function fdv_plugin_settings_section_cb() {
    echo '<p>' . __('Configure the general settings for FDV Plugin.', 'fdv_plugin') . '</p>';
}

// Callback für "Language"
function fdv_plugin_language_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <select name="fdv_plugin_options[language]" id="language">
        <option value="en" <?php selected($options['language'], 'en'); ?>>English</option>
        <option value="de" <?php selected($options['language'], 'de'); ?>>Deutsch</option>
    </select>
    <p class="description"><?php _e('Select the language for the plugin.', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Max Recursion Depth"
function fdv_plugin_max_recursion_depth_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="number" name="fdv_plugin_options[maxRecursionDepth]" value="<?php echo esc_attr($options['maxRecursionDepth'] ?? '50'); ?>" />
    <p class="description"><?php _e('Maximum recursion depth for variable dumping.', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Max Depth"
function fdv_plugin_max_depth_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="number" name="fdv_plugin_options[maxDepth]" value="<?php echo esc_attr($options['maxDepth'] ?? '8'); ?>" />
    <p class="description"><?php _e('Maximum depth for variable expansion.', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Max Execution Time"
function fdv_plugin_max_execution_time_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="number" name="fdv_plugin_options[maxExecutionTime]" value="<?php echo esc_attr($options['maxExecutionTime'] ?? '5'); ?>" />
    <p class="description"><?php _e('Maximum execution time for dumping (in seconds).', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Max Elements Per Level"
function fdv_plugin_max_elements_per_level_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="number" name="fdv_plugin_options[maxElementsPerLevel]" value="<?php echo esc_attr($options['maxElementsPerLevel'] ?? '10'); ?>" />
    <p class="description"><?php _e('Maximum number of elements per level for dumping.', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Sort Properties and Methods"
function fdv_plugin_sort_properties_and_methods_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="checkbox" name="fdv_plugin_options[sortPropertiesAndMethods]" value="1" <?php checked(1, isset($options['sortPropertiesAndMethods']) ? $options['sortPropertiesAndMethods'] : 0); ?> />
    <label for="fdv_plugin_sort_properties_and_methods"><?php _e('Sort properties and methods when displaying variables.', 'fdv_plugin'); ?></label>
    <?php
}

// Callback für "Show Time Information"
function fdv_plugin_show_time_info_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="checkbox" name="fdv_plugin_options[ShowTimeInfo]" value="1" <?php checked(1, isset($options['ShowTimeInfo']) ? $options['ShowTimeInfo'] : 0); ?> />
    <label for="fdv_plugin_show_time_info"><?php _e('Show time information for each variable.', 'fdv_plugin'); ?></label>
    <?php
}

// Callback für "Title"
function fdv_plugin_title_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="text" name="fdv_plugin_options[Title]" value="<?php echo esc_attr($options['Title'] ?? ''); ?>" />
    <p class="description"><?php _e('Set the title for the dump output.', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Enable Dump Wrapper"
function fdv_plugin_dump_wrapper_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="checkbox" name="fdv_plugin_options[fdv_plugin_dump_wrapper]" value="1" <?php checked(1, isset($options['fdv_plugin_dump_wrapper']) ? $options['fdv_plugin_dump_wrapper'] : 0); ?> />
    <label for="fdv_plugin_dump_wrapper"><?php _e('Enable dump wrapper?', 'fdv_plugin'); ?></label>
    <?php
}

// Callback für "Dump Wrapper Style"
function fdv_plugin_dump_wrapper_style_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <input type="text" name="fdv_plugin_options[fdv_plugin_dump_wrapper_style]" value="<?php echo esc_attr($options['fdv_plugin_dump_wrapper_style'] ?? ''); ?>" />
    <p class="description"><?php _e('Geben Sie Cutom Inner CSS ein, falls gewünscht.', 'fdv_plugin'); ?></p>
    <?php
}

// Callback für "Select CSS Template"
function fdv_plugin_custom_css_file_cb() {
    $options = get_option('fdv_plugin_options');
    ?>
    <select name="fdv_plugin_options[customCssFile]" id="customCssFile">
        <option value="FancyDumpVar.css" <?php selected($options['customCssFile'], 'FDV standard'); ?>>FancyDumpVar.css (Default)</option>
        <option value="monocrom.css" <?php selected($options['customCssFile'], 'Monocrom'); ?>>Monocrom.css</option>
        <option value="black.css" <?php selected($options['customCssFile'], 'Schwarz'); ?>>Black.css</option>
        <option value="VisualStudioCode.css" <?php selected($options['customCssFile'], 'VisualStudioCode'); ?>>Visual Studio Code.css</option>
        <option value="light.css" <?php selected($options['customCssFile'], 'Hell'); ?>>Light.css</option>
        <option value="Colorful.css" <?php selected($options['customCssFile'], 'Bunt'); ?>>Colorful.css</option>
    </select>
    <p class="description"><?php _e('Bitte wählen Sie die gewünschte Darstellung.', 'fdv_plugin'); ?></p>
    <?php  
}

// Einstellungen sanitisieren
function fdv_plugin_options_sanitize($input) {
    // Sicherstellen, dass nur gültige Werte gespeichert werden
    $valid_css_files = ['FancyDumpVar.css', 'monocrom.css', 'black.css', 'VisualStudioCode.css', 'light.css', 'Colorful.css'];
    if (!in_array($input['customCssFile'], $valid_css_files)) {
        $input['customCssFile'] = 'FancyDumpVar.css'; // Standardwert
    }

    // Die Checkbox "Enable Dump Wrapper"
    $input['fdv_plugin_dump_wrapper'] = isset($input['fdv_plugin_dump_wrapper']) ? 1 : 0;

    // Benutzerdefinierten Stil sanitisieren
    $input['fdv_plugin_dump_wrapper_style'] = sanitize_text_field($input['fdv_plugin_dump_wrapper_style']);

    return $input;
}
