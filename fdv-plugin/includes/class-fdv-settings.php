<?php
namespace FDV;

if ( ! defined( 'ABSPATH' ) ) exit;

class Settings {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function add_admin_menu() {
        add_menu_page(
            'FDV Plugin Settings',
            'FDV Plugin',
            'manage_options',
            'fdv_plugin_settings',
            [ __CLASS__, 'settings_page' ],
            'dashicons-admin-generic'
        );
    }

    public static function settings_page() {
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

    public static function register_settings() {
        register_setting('fdv_plugin_options_group', 'fdv_plugin_options', [ __CLASS__, 'sanitize_options' ]);

        add_settings_section(
            'fdv_plugin_general_settings',
            __('General Settings', 'fdv_plugin'),
            function () {
                echo '<p>' . __('Configure the general settings for FDV Plugin.', 'fdv_plugin') . '</p>';
            },
            'fdv_plugin_settings'
        );

        // Setting-Felder registrieren
        self::add_field('language',         'Language',                  [ __CLASS__, 'language_cb' ]);
        self::add_field('maxRecursionDepth','Max Recursion Depth',      [ __CLASS__, 'number_cb' ]);
        self::add_field('maxDepth',         'Max Depth',                [ __CLASS__, 'number_cb' ]);
        self::add_field('maxExecutionTime', 'Max Execution Time (s)',    [ __CLASS__, 'number_cb' ]);
        self::add_field('maxElementsPerLevel','Max Elements Per Level', [ __CLASS__, 'number_cb' ]);
        self::add_field('sortPropertiesAndMethods','Sort Properties and Methods', [ __CLASS__, 'checkbox_cb' ]);
        self::add_field('ShowTimeInfo',     'Show Time Info',           [ __CLASS__, 'checkbox_cb' ]);
        self::add_field('Title',            'Dump Title',               [ __CLASS__, 'text_cb' ]);
        self::add_field('fdv_plugin_dump_wrapper', 'Enable Dump Wrapper',[ __CLASS__, 'checkbox_cb' ]);
        self::add_field('fdv_plugin_dump_wrapper_style', 'Wrapper Style',[ __CLASS__, 'text_cb' ]);
        self::add_field('customCssFile',    'CSS Template',             [ __CLASS__, 'css_template_cb' ]);
        self::add_field('fdv_plugin_auto_view_admin', 'FDV im Backend automatisch aktivieren', [ __CLASS__, 'checkbox_cb' ]);
        self::add_field('fdv_plugin_auto_view_frontend', 'FDV im Frontend automatisch aktivieren', [ __CLASS__, 'checkbox_cb' ]);
		self::add_field('dumpContainerMaxHeight', 'Dump Max Height (px)', [ __CLASS__, 'text_cb' ]);


        // Neue Sektion: Auto-Dump Variablen
        add_settings_section(
            'fdv_plugin_auto_dump_section',
            __('Auto Dump: Variables & Structures', 'fdv_plugin'),
            function () {
                echo '<p>' . __('Choose which global variables should be dumped automatically.', 'fdv_plugin') . '</p>';
            },
            'fdv_plugin_settings'
        );

        // Checkboxen für die Auswahl
        self::add_field('auto_dump_get',      '$_GET',      [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');
        self::add_field('auto_dump_post',     '$_POST',     [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');
        self::add_field('auto_dump_request',  '$_REQUEST',  [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');
        self::add_field('auto_dump_server',   '$_SERVER',   [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');
        self::add_field('auto_dump_session',  '$_SESSION',  [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');
        self::add_field('auto_dump_cookie',   '$_COOKIE',   [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');
        self::add_field('auto_dump_wp_info',  'FDV WP-Overview', [ __CLASS__, 'checkbox_cb' ], 'fdv_plugin_auto_dump_section');


		
    }

    private static function add_field($id, $title, $callback, $section = 'fdv_plugin_general_settings') {
        add_settings_field(
            $id,
            $title,
            $callback,
            'fdv_plugin_settings',
            $section,
            ['id' => $id]
        );
    }

    // === Field Callbacks ===

    public static function language_cb($args) {
        $options = get_option('fdv_plugin_options');
        $value = $options['language'] ?? 'en';
        ?>
        <select name="fdv_plugin_options[language]" id="<?php echo esc_attr($args['id']); ?>">
            <option value="en" <?php selected($value, 'en'); ?>>English</option>
            <option value="de" <?php selected($value, 'de'); ?>>Deutsch</option>
        </select>
        <?php
    }

    public static function number_cb($args) {
        $id = $args['id'];
        $options = get_option('fdv_plugin_options');
        ?>
        <input type="number" name="fdv_plugin_options[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($options[$id] ?? ''); ?>" />
        <?php
    }

    public static function text_cb($args) {
        $id = $args['id'];
        $options = get_option('fdv_plugin_options');
        ?>
        <input type="text" name="fdv_plugin_options[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($options[$id] ?? ''); ?>" />
        <?php
    }

    public static function checkbox_cb($args) {
        $id = $args['id'];
        $options = get_option('fdv_plugin_options');
        ?>
        <input type="checkbox" name="fdv_plugin_options[<?php echo esc_attr($id); ?>]" value="1" <?php checked(1, isset($options[$id]) ? $options[$id] : 0); ?> />
        <?php
    }

    public static function css_template_cb($args) {
        $id = $args['id'];
        $options = get_option('fdv_plugin_options');
        $selected = $options[$id] ?? 'FancyDumpVar.css';

        $templates = [
            'FancyDumpVar.css'        => 'FancyDumpVar (Default)',
            'monocrom.css'            => 'Monocrom',
            'black.css'               => 'Black',
            'VisualStudioCode.css'    => 'Visual Studio Code',
            'light.css'               => 'Light',
            'Colorful.css'            => 'Colorful',
        ];
        ?>
        <select name="fdv_plugin_options[<?php echo esc_attr($id); ?>]" id="<?php echo esc_attr($id); ?>">
            <?php foreach ($templates as $file => $label): ?>
                <option value="<?php echo esc_attr($file); ?>" <?php selected($selected, $file); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    // === Sanitize ===

    public static function sanitize_options($input) {
        $input['fdv_plugin_dump_wrapper'] = isset($input['fdv_plugin_dump_wrapper']) ? 1 : 0;
        $input['fdv_plugin_auto_view_admin'] = isset($input['fdv_plugin_auto_view_admin']) ? 1 : 0;
        $input['fdv_plugin_auto_view_frontend'] = isset($input['fdv_plugin_auto_view_frontend']) ? 1 : 0;

        // Nur erlaubte CSS-Dateien
        $valid_css_files = ['FancyDumpVar.css', 'monocrom.css', 'black.css', 'VisualStudioCode.css', 'light.css', 'Colorful.css'];
        if (!in_array($input['customCssFile'], $valid_css_files)) {
            $input['customCssFile'] = 'FancyDumpVar.css';
        }

        // Styles säubern
        $input['fdv_plugin_dump_wrapper_style'] = sanitize_text_field($input['fdv_plugin_dump_wrapper_style']);
		
		// max. Container Höhe
        $height = trim($input['dumpContainerMaxHeight'] ?? '');

        if (preg_match('/^\d+(px|em|rem|vh|%)$/', $height)) {
            $input['dumpContainerMaxHeight'] = $height;
        } else {
            $input['dumpContainerMaxHeight'] = '680px'; // fallback
        }
        
		


        $auto_fields = [
            'auto_dump_get',
            'auto_dump_post',
            'auto_dump_request',
            'auto_dump_server',
            'auto_dump_session',
            'auto_dump_cookie',
            'auto_dump_wp_info',
        ];
        
        foreach ($auto_fields as $field) {
            $input[$field] = isset($input[$field]) ? 1 : 0;
        }



        return $input;
    }
}
