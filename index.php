<?php

/* 
  Plugin Name: Easy2Map WordPress Plugin 
  URI: http://easy2map.com/ 
  Description: The easiest tool available for creating custom & great-looking Google Maps. Add multiple pins and customize maps with drag-and-drop simplicity.
  Author: Steven Ellis 
  Version: 1.0.6 
  Author URI: http://easy2map.com/ 
 */

$e2m_function_names_used = array('easy2map_retrieve_map_pins_callback', 'easy2map_save_map_polylines_callback',
    'easy2map_on_uninstall_hook', 'easy2map_delete_map_point_callback', 'easy2map_delete_map',
    'easy2map_save_default_pin_image_callback', 'easy2map_get_plugin_url', 'easy2map_save_map_pin',
    'easy2map_update_map_pin_location', 'easy2map_save_map', 'easy2map_save_map_name', 'easy2map_retrieve_pin_icons_callback',
    'easy2map_retrieve_map_settings', 'easy2map_retrieve_map_HTML',
    'easy2map_retrieve_map_settings_callback', 'easy2map_retrieve_map_templates_callback', 'easy2map_retrieve_mappin_templates_callback');

$e2m_class_names_used = array('Easy2Map', 'Easy2MapTest', 'easy2MapItem', 'easy2mapTemplate', 'easy2mapPinTemplate', 'easy2mapmatchedPoint');
$e2m_constants_used = array('EASY2MAP_PLUGIN_BOOTSTRAP', 'EASY2MAP_PLUGIN_DIR');

$e2m_error_items = '';

function some_unique_easy2mapfunction_name_cannot_load() {
    global $e2m_error_items;
    echo '<div class="error"><p><strong>'
            . __('The "Easy2Map" plugin cannot load correctly') . '</strong> '
            . __('Another plugin has declared conflicting class, function, or constant names:') . "<ul'>$e2m_error_items</ul>" . '</p><p>'
            . __('You must deactivate the plugins that are using these conflicting names.') . '</p></div>';
}

/* * * The following code tests whether or not this plugin can be safely loaded. 
  If there are no name conflicts, the loader.php is included and the plugin is loaded,
  otherwise, an error is displayed in the manager.

 */// Check for conflicting function names 
foreach ($e2m_function_names_used as $f_name) {
    if (function_exists($f_name)) {
        $e2m_error_items .= '<li>' . __('Function: ') . $f_name . '</li>';
    }
}
// Check for conflicting Class names 
foreach ($e2m_class_names_used as $cl_name) {
    if (class_exists($cl_name)) {
        $e2m_error_items .= '<li>' . __('Class: ') . $cl_name . '</li>';
    }
}
// Check for conflicting Constants 
foreach ($e2m_constants_used as $c_name) {
    if (defined($c_name)) {
        $e2m_error_items .= '<li>' . __('Constant: ') . $c_name . '</li>';
    }
}
// Fire the error, or load the plugin. 
if ($e2m_error_items) {
    
    $e2m_error_items = '<ul>' . $e2m_error_items . '</ul>';
    add_action('admin_notices', 'some_unique_easy2mapfunction_name_cannot_load');
} 

//load the application

if (!function_exists('easy2map_define_constants')):

    function easy2map_define_constants() {
        define('EASY2MAP_PLUGIN_BOOTSTRAP', __FILE__);
        define('EASY2MAP_PLUGIN_DIR', dirname(EASY2MAP_PLUGIN_BOOTSTRAP));
    }

endif;

if (!function_exists('easy2map_add_actions')):

    function easy2map_add_actions() {

        add_action('wp_head', 'Easy2Map::head');
        add_action('init', 'Easy2Map::initialize');
        add_action('init', 'Easy2Map::create_admin_tables');
        add_action('init', 'Easy2Map::register_shortcodes');
        add_action('admin_menu', 'Easy2Map::create_admin_menu');

        add_action('wp_ajax_retrieve_map_points', 'easy2map_retrieve_map_pins_callback');
        add_action('wp_ajax_delete_map_point', 'easy2map_delete_map_point_callback');
        add_action('wp_ajax_retrieve_pin_icons', 'easy2map_retrieve_pin_icons_callback');
        add_action('wp_ajax_save_default_pin_image', 'easy2map_save_default_pin_image_callback');
        add_action('wp_ajax_retrieve_map_settings', 'easy2map_retrieve_map_settings_callback');
        add_action('wp_ajax_retrieve_map_templates', 'easy2map_retrieve_map_templates_callback');
        add_action('wp_ajax_retrieve_mappin_templates', 'easy2map_retrieve_mappin_templates_callback');
        add_action('wp_ajax_save_map_polylines', 'easy2map_save_map_polylines_callback');
        add_action('wp_ajax_save_map', 'easy2map_save_map');
        add_action('wp_ajax_save_map_name', 'easy2map_save_map_name');
        add_action('wp_ajax_save_map_pin', 'easy2map_save_map_pin');
        add_action('wp_ajax_update_map_pin_location', 'easy2map_update_map_pin_location');
        
        add_action('wp_ajax_nopriv_retrieve_map_points', 'easy2map_retrieve_map_pins_callback');
        add_action('wp_ajax_nopriv_delete_map_point', 'easy2map_delete_map_point_callback');
        add_action('wp_ajax_nopriv_retrieve_pin_icons', 'easy2map_retrieve_pin_icons_callback');
        add_action('wp_ajax_nopriv_save_default_pin_image', 'easy2map_save_default_pin_image_callback');
        add_action('wp_ajax_nopriv_retrieve_map_settings', 'easy2map_retrieve_map_settings_callback');
        add_action('wp_ajax_nopriv_retrieve_map_templates', 'easy2map_retrieve_map_templates_callback');
        add_action('wp_ajax_nopriv_retrieve_mappin_templates', 'easy2map_retrieve_mappin_templates_callback');
        add_action('wp_ajax_nopriv_save_map_polylines', 'easy2map_save_map_polylines_callback');
        add_action('wp_ajax_nopriv_save_map', 'easy2map_save_map');
        add_action('wp_ajax_nopriv_save_map_name', 'easy2map_save_map_name');
        add_action('wp_ajax_nopriv_save_map_pin', 'easy2map_save_map_pin');
        add_action('wp_ajax_nopriv_update_map_pin_location', 'easy2map_update_map_pin_location');
    }

endif;

if (!function_exists('easy2map_require_dependancies')):

    function easy2map_require_dependancies() {
        require_once (EASY2MAP_PLUGIN_DIR . '/includes/Easy2Map.php');
        require_once (EASY2MAP_PLUGIN_DIR . '/test/Easy2MapTest.php');
        require_once (EASY2MAP_PLUGIN_DIR . '/includes/Functions.php');
    }

endif;

if (!function_exists('easy2map_register_hooks')):

    function easy2map_register_hooks() {
        register_uninstall_hook(EASY2MAP_PLUGIN_BOOTSTRAP, 'easy2map_on_uninstall_hook');
    }

endif;

/* BOOTSTRAPPING STARTS */
easy2map_define_constants();
easy2map_require_dependancies();
easy2map_add_actions();
easy2map_register_hooks();
//easy2map_add_shortcode_support();
//easy2map_add_filters();
//easy2map_init_db_settings();
/* BOOTSTRAPPING ENDS */


?>
