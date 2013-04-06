<?php

/* 
  Plugin Name: Easy2Map Photos
  URI: http://easy2map.com/ 
  Description: The easiest tool available for creating geo-tagged photo galleries. Display photos & their geographic location in a great-looking slider gallery.
  Author: Steven Ellis 
  Version: 1.0.1 
  Author URI: http://easy2map.com/ 
 */

$e2m_image_function_names_used = array('easy2mapimg_retrieve_map_pins_callback',
'easy2mapimg_e2m_img_e2m_img_save_map_polylines_callback',
'easy2mapimg_e2m_img_delete_map_point_callback',
'easy2mapimg_delete_map',
'easy2mapimg_e2m_img_save_default_pin_image_callback',
'easy2mapimg_get_plugin_url',
'easy2mapimg_e2m_img_save_map_pin',
'easy2mapimg_e2m_img_update_map_pin_location',
'easy2mapimg_e2m_img_save_map',
'easy2mapimg_e2m_img_save_map_name',
'easy2mapimg_e2m_img_retrieve_pin_icons_callback',
'easy2mapimg_e2m_img_retrieve_map_settings',
'easy2mapimg_retrieve_map_HTML',
'easy2mapimg_e2m_img_retrieve_map_settings_callback',
'easy2mapimg_e2m_img_retrieve_map_templates_callback',
'easy2mapimg_admin_validation',
'easy2mapimg_e2m_img_retrieve_mappin_templates_callback');

$e2m_image_class_names_used = array('Easy2MapImg', 'Easy2MapImgTest', 'e2mImgMapItem', 'e2mImgMapTemplate', 'e2mImgMapPinTemplate', 'e2mImgMatchedPoint');
$e2m_image_constants_used = array('EASY2MAPIMG_PLUGIN_BOOTSTRAP', 'EASY2MAPIMG_PLUGIN_DIR');

$e2m_image_error_items = '';

function some_unique_easy2mapimgfunction_name_cannot_load() {
    global $e2m_image_error_items;
    echo '<div class="error"><p><strong>'
            . __('The "Easy2Map My Photos" plugin cannot load correctly') . '</strong> '
            . __('Another plugin has declared conflicting class, function, or constant names:') . "<ul'>$e2m_image_error_items</ul>" . '</p><p>'
            . __('You must deactivate the plugins that are using these conflicting names.') . '</p></div>';
}

foreach ($e2m_image_function_names_used as $f_name) {
    if (function_exists($f_name)) {
        $e2m_image_error_items .= '<li>' . __('Function: ') . $f_name . '</li>';
    }
}
// Check for conflicting Class names 
foreach ($e2m_image_class_names_used as $cl_name) {
    if (class_exists($cl_name)) {
        $e2m_image_error_items .= '<li>' . __('Class: ') . $cl_name . '</li>';
    }
}
// Check for conflicting Constants 
foreach ($e2m_image_constants_used as $c_name) {
    if (defined($c_name)) {
        $e2m_image_error_items .= '<li>' . __('Constant: ') . $c_name . '</li>';
    }
}
// Fire the error, or load the plugin. 
if ($e2m_image_error_items) {
    
    $e2m_image_error_items = '<ul>' . $e2m_image_error_items . '</ul>';
    add_action('admin_notices', 'some_unique_easy2mapimgfunction_name_cannot_load');
}

//load the application

if (!function_exists('easy2mapimg_define_constants')):

    function easy2mapimg_define_constants() {
        define('EASY2MAPIMG_PLUGIN_BOOTSTRAP', __FILE__);
        define('EASY2MAPIMG_PLUGIN_DIR', dirname(EASY2MAPIMG_PLUGIN_BOOTSTRAP));
    }

endif;

if (!function_exists('easy2mapimg_add_actions')):

    function easy2mapimg_add_actions() {

        add_action('wp_head', 'Easy2MapImg::head');
        add_action('init', 'Easy2MapImg::initialize');
        add_action('init', 'Easy2MapImg::create_admin_tables');
        add_action('init', 'Easy2MapImg::register_shortcodes');
        add_action('admin_menu', 'Easy2MapImg::create_admin_menu');

        add_action('wp_ajax_e2m_img_retrieve_map_points', 'easy2mapimg_retrieve_map_pins_callback');
        add_action('wp_ajax_e2m_img_delete_map_point', 'easy2mapimg_e2m_img_delete_map_point_callback');
        add_action('wp_ajax_e2m_img_retrieve_pin_icons', 'easy2mapimg_e2m_img_retrieve_pin_icons_callback');
        add_action('wp_ajax_e2m_img_save_default_pin_image', 'easy2mapimg_e2m_img_save_default_pin_image_callback');
        add_action('wp_ajax_e2m_img_retrieve_map_settings', 'easy2mapimg_e2m_img_retrieve_map_settings_callback');
        add_action('wp_ajax_e2m_img_retrieve_map_templates', 'easy2mapimg_e2m_img_retrieve_map_templates_callback');
        add_action('wp_ajax_e2m_img_retrieve_mappin_templates', 'easy2mapimg_e2m_img_retrieve_mappin_templates_callback');
        add_action('wp_ajax_e2m_img_e2m_img_save_map_polylines', 'easy2mapimg_e2m_img_e2m_img_save_map_polylines_callback');
        add_action('wp_ajax_e2m_img_save_map', 'easy2mapimg_e2m_img_save_map');
        add_action('wp_ajax_e2m_img_save_map_name', 'easy2mapimg_e2m_img_save_map_name');
        add_action('wp_ajax_e2m_img_save_map_pin', 'easy2mapimg_e2m_img_save_map_pin');
        add_action('wp_ajax_e2m_img_update_map_pin_location', 'easy2mapimg_e2m_img_update_map_pin_location');
        
        add_action('wp_ajax_nopriv_e2m_img_retrieve_map_points', 'easy2mapimg_retrieve_map_pins_callback');
        add_action('wp_ajax_nopriv_e2m_img_delete_map_point', 'easy2mapimg_e2m_img_delete_map_point_callback');
        add_action('wp_ajax_nopriv_e2m_img_retrieve_pin_icons', 'easy2mapimg_e2m_img_retrieve_pin_icons_callback');
        add_action('wp_ajax_nopriv_e2m_img_save_default_pin_image', 'easy2mapimg_e2m_img_save_default_pin_image_callback');
        add_action('wp_ajax_nopriv_e2m_img_retrieve_map_settings', 'easy2mapimg_e2m_img_retrieve_map_settings_callback');
        add_action('wp_ajax_nopriv_e2m_img_retrieve_map_templates', 'easy2mapimg_e2m_img_retrieve_map_templates_callback');
        add_action('wp_ajax_nopriv_e2m_img_retrieve_mappin_templates', 'easy2mapimg_e2m_img_retrieve_mappin_templates_callback');
        add_action('wp_ajax_nopriv_e2m_img_e2m_img_save_map_polylines', 'easy2mapimg_e2m_img_e2m_img_save_map_polylines_callback');
        add_action('wp_ajax_nopriv_e2m_img_save_map', 'easy2mapimg_e2m_img_save_map');
        add_action('wp_ajax_nopriv_e2m_img_save_map_name', 'easy2mapimg_e2m_img_save_map_name');
        add_action('wp_ajax_nopriv_e2m_img_save_map_pin', 'easy2mapimg_e2m_img_save_map_pin');
        add_action('wp_ajax_nopriv_e2m_img_update_map_pin_location', 'easy2mapimg_e2m_img_update_map_pin_location');
        
    }

endif;

if (!function_exists('easy2mapimg_require_dependancies')):

    function easy2mapimg_require_dependancies() {
        require_once (EASY2MAPIMG_PLUGIN_DIR . '/includes/Easy2MapImg.php');
        require_once (EASY2MAPIMG_PLUGIN_DIR . '/test/Easy2MapImgTest.php');
        require_once (EASY2MAPIMG_PLUGIN_DIR . '/includes/Functions.php');
    }

endif;

if (!function_exists('easy2mapimg_register_hooks')):

    function easy2mapimg_register_hooks() {
    }

endif;

/* BOOTSTRAPPING STARTS */
easy2mapimg_define_constants();
easy2mapimg_require_dependancies();
easy2mapimg_add_actions();
easy2mapimg_register_hooks();
//easy2mapimg_add_shortcode_support();
//easy2mapimg_add_filters();
//easy2mapimg_init_db_settings();
/* BOOTSTRAPPING ENDS */

?>
