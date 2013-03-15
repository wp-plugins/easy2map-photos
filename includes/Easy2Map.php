<?php

class Easy2Map {

    const plugin_name = 'Easy2Map';
    const min_php_version = '5.0';
    const min_wp_version = '3.0';

    // Used to uniquely identify this plugin's menu page in the WP manager
    const admin_menu_slug = 'easy2map';

    /** Adds the necessary JavaScript and/or CSS to the pages to enable the Ajax search. */
    public static function head() {

        $src_Easy2MapAPI = "http://maps.google.com/maps/api/js?sensor=false";
        $src_Easy2Map = plugins_url('scripts/easy2map.js', dirname(__FILE__));
        $src_Xml2json = plugins_url('scripts/jquery.xml2json.js', dirname(__FILE__));
        //$src_Cluster = plugins_url('scripts/easy2map.cluster.js', dirname(__FILE__));

        wp_register_script('easy2map_js_api', $src_Easy2MapAPI);
        wp_register_script('easy2map_js_easy2map', $src_Easy2Map);
        wp_register_script('easy2map_js_Xml2json', $src_Xml2json);
        //wp_register_script('easy2map_js_cluster', $src_Cluster);

        wp_enqueue_script('easy2map_js_api');
        wp_enqueue_script('easy2map_js_easy2map');
        wp_enqueue_script('easy2map_js_Xml2json');
        
        //wp_enqueue_script('easy2map_js_cluster');
        
    }

    /** The main function for this plugin, similar to __construct() */
    public static function initialize() {

        Easy2MapTest::min_php_version(self::min_php_version);
        Easy2MapTest::min_wordpress_version(self::min_wp_version);
        
        wp_enqueue_script('jquery'); // make sure jQuery is loaded!
        wp_enqueue_script('jquery-ui-draggable'); 
        wp_enqueue_script('jquery-ui-droppable'); 

        //if (self::_is_searchable_page()) {
            //$src = plugins_url('css/easy2map.css', dirname(__FILE__));
            //wp_register_style('easy2map', $src);
            //wp_enqueue_style('easy2map');
        //}
    }

    /**     * Register the shortcodes used */
    public static function register_shortcodes() {
        add_shortcode('easy2map', 'Easy2Map::retrieve_map');
    }
    
    public static function create_admin_tables(){
        
        global $wpdb;
        $error =  "<div id='error' class='error'><p>%s</p></div>";
        $map_table = $wpdb->prefix . "easy2map_maps";
        $map_points_table = $wpdb->prefix . "easy2map_map_points";
        $map_point_templates_table = $wpdb->prefix . "easy2map_pin_templates";
        $map_templates_table = $wpdb->prefix . "easy2map_templates";
        
        $result = $wpdb->get_var("show tables like '$map_table'");

        if (strtolower($result) != strtolower($map_table)) {
            
            $SQL = "CREATE TABLE `$map_table` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT,
            `TemplateID` bigint(20) DEFAULT NULL,
            `MapName` varchar(256) DEFAULT NULL,
            `MapTitle` varchar(512) DEFAULT NULL,
            `DefaultPinImage` varchar(256) DEFAULT NULL,
            `Settings` text,
            `LastInvoked` datetime DEFAULT NULL,
            `PolyLines` text,
            `CSSValues` text,
            `MapHTML` text,
            `IsActive` smallint(6),
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";
            
            if (!$wpdb->query($SQL)){
                echo sprintf($error, __("Could not create easy2map maps table.", 'easy2map'));
		return;
            }
        }
        
        $result = $wpdb->get_var("show tables like '$map_points_table'");

        if (strtolower($result) != strtolower($map_points_table)) {
            
            $SQL = "CREATE TABLE `$map_points_table` (
                `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                `MapID` bigint(20) DEFAULT NULL,
                `CreatedByUserID` bigint(20) DEFAULT NULL,
                `LatLong` varchar(128) DEFAULT NULL,
                `Title` varchar(512) DEFAULT NULL,
                `PinImageURL` varchar(512) DEFAULT NULL,
                `Settings` varchar(512) DEFAULT NULL,
                `DetailsHTML` text,
                PRIMARY KEY (`ID`),
                UNIQUE KEY `ID_UNIQUE` (`ID`),
                KEY `wp_easy2map_map_points_MapID` (`MapID`),
                CONSTRAINT `wp_easy2map_map_points_MapID` FOREIGN KEY (`MapID`) REFERENCES `$map_table` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";
            
            if (!$wpdb->query($SQL)){
                echo sprintf($error, __("Could not create easy2map pins table.", 'easy2map'));
		return;
            }
            
        }
        
        $result = $wpdb->get_var("show tables like '$map_point_templates_table'");

        if (strtolower($result) != strtolower($map_point_templates_table)) {
            
            $SQL = "CREATE TABLE `$map_point_templates_table` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `TemplateName` varchar(128) DEFAULT NULL,
            `TemplateHTML` text,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`)
          ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";
            
            if (!$wpdb->query($SQL)){
                echo sprintf($error, __("Could not create easy2map pin templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert1 = "INSERT INTO `$map_point_templates_table`
            (`TemplateName`,
            `TemplateHTML`)
            VALUES
            ('Thumbnail Image on Left', 
            '<table style=\"margin-top:8px;overflow:hidden;vertical-align:middle;width:325px;border-radius:0px;margin-left:5px;margin-right:20px;\"><tr><td style=\"vertical-align:top;padding:6px;border-style:solid;border-width:1px;vertical-align:top;border-color:#FFFFFF;\"><img style=\"border:0px solid #000000;border-radius: 0px;\" src=\"http://easy2map.com/images/css_templates/thumbnail20120424025713000000_CSS.png\" border=\"0\"></td><td style=\"width:100%;vertical-align:top;\"><p align=\"left\" style=\"border-color:#E53840;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;background-color:#E53840;border-radius:0px;margin:3px;\">Section 1</p><p align=\"left\" style=\"margin:3px;background-color:#009AD7;border-color:#009AD7;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:top;border-radius:0px;\">Section 2</p><p align=\"left\" style=\"margin:3px;background-color:#DADADA;border-color:#DADADA;border-style:solid;border-width:1px;color:#000000;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;border-radius:0px;\">Section 3</p><p align=\"left\" style=\"margin:3px;background-color:#FFFFFF;border-color:#EBEBEB;border-style:solid;border-width:1px;color:#000000;font-size:12px;font-weight:normal;padding:4px;text-align:left;border-radius:0px;\">Section 4</p></td></tr></table>')";
            
            if (!$wpdb->query($SQLInsert1)){
                echo sprintf($error, __("Could not insert data into easy2map pin templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert2 = "INSERT INTO `$map_point_templates_table`
            (`TemplateName`,
            `TemplateHTML`)
            VALUES
            ('Thumbnail Image on Right',
            '<table style=\"margin-top:8px;overflow:hidden;vertical-align:middle;width:325px;border-radius:0px;margin-left:5px;margin-right:20px;\"><tr><td style=\"width:100%;vertical-align:top;\"><p align=\"left\" style=\"border-color:#E53840;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;background-color:#E53840;border-radius:0px;margin:3px;\">Section 1</p><p align=\"left\" style=\"margin:3px;background-color:#009AD7;border-color:#009AD7;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:top;border-radius:0px;\">Section 2</p><p align=\"left\" style=\"margin:3px;background-color:#DADADA;border-color:#DADADA;border-style:solid;border-width:1px;color:#000000;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;border-radius:0px;\">Section 3</p><p align=\"left\" style=\"margin:3px;background-color:#FFFFFF;border-color:#EBEBEB;border-style:solid;border-width:1px;color:#000000;font-size:12px;font-weight:normal;padding:4px;text-align:left;border-radius:0px;\">Section 4</p></td><td style=\"vertical-align:top;padding:6px;border-style:solid;border-width:1px;vertical-align:top;border-color:#FFFFFF;\"><img style=\"border:0px solid #000000;border-radius: 0px;\" src=\"http://easy2map.com/images/css_templates/thumbnail20120424025713000000_CSS.png\" border=\"0\"></td></tr></table>')";
            
            if (!$wpdb->query($SQLInsert2)){
                echo sprintf($error, __("Could not insert data into easy2map pin templates table.", 'easy2map'));
		return;
            }
            
        }
        
        $result = $wpdb->get_var("show tables like '$map_templates_table'");

        if (strtolower($result) != strtolower($map_templates_table)) {
            
            $SQL = "CREATE TABLE `$map_templates_table` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `TemplateName` varchar(256) DEFAULT NULL,
            `ExampleImage` varchar(512) DEFAULT NULL,
            `DisplayOrder` smallint(6) DEFAULT NULL,
            `CSSValues` text,
            `TemplateHTML` text,
            `StyleParentOnly` smallint(6) DEFAULT NULL,
            `Active` smallint(6) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`)
          ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";
            
            if (!$wpdb->query($SQL)){
                echo sprintf($error, __("Could not create easy2map templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert3 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `TemplateHTML`,
            `StyleParentOnly`,
            `Active`
            )
            VALUES
            ('Fixed-width map', '', 1,
            '<settings border-style=\"solid\" border-width=\"2px\" border-color=\"#f0f0f0\" border-radius=\"0px\" width=\"600px\" height=\"400px\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<div id=\"divMap\" style=\"\"></div>', 0, 0)";
                        
            if (!$wpdb->query($SQLInsert3)){
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert4 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `TemplateHTML`,
            `StyleParentOnly`,
            `Active`
            )
            VALUES
            ('Responsive-width map', '', 2,
            '<settings border-style=\"solid\" border-width=\"0px\" border-color=\"#EBEBEB\" border-radius=\"0px\" width=\"100%\" height=\"400px\" margin-bottom=\"auto\" margin-left=\"auto\" margin-right=\"auto\" margin-top=\"auto\" />',
            '<div id=\"divMap\" style=\"\"></div>', 0, 0)";
            
            if (!$wpdb->query($SQLInsert4)){
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert5 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `TemplateHTML`,
            `StyleParentOnly`,
            `Active`
            )
            VALUES
            ('Fixed-width map with white edge', '', 3,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" width=\"640px\" height=\"480px\"  margin-left=\"auto\" margin-right=\"auto\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td id=\"tdMap\" editable=\"0\" style=\"vertical-align:top;\"><div id=\"divMap\" style=\"background-color: #EBEBEB;border-style:solid;border-width:1px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"></div></td></tr></table></div>', 1, 1)";
            
            if (!$wpdb->query($SQLInsert5)){
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert6 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `TemplateHTML`,
            `StyleParentOnly`,
            `Active`)
            VALUES
            ('Fixed-width map with concave bottom shadow', '', 4,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" width=\"600px\" height=\"400px\"  margin-left=\"auto\" margin-right=\"auto\" margin-bottom=\"0px\" />',
            '<div align=\"center\" style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td align=\"center\" id=\"tdMap\" editable=\"0\" style=\"vertical-align:top;\"><div align=\"center\" id=\"divMap\" style=\"background-color: #EBEBEB;border-style:solid;border-width:1px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"></div></td></tr></table><img style=\"width:600px\" id=\"easy2mapIimgShadow\" src=\"[siteurl]images/map_templates/easy2map_map-shadow_bottom_1.png\"/></div>', 1, 0)";
            
            if (!$wpdb->query($SQLInsert6)){
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2map'));
		return;
            }
            
            $SQLInsert7 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `TemplateHTML`,
            `StyleParentOnly`,
            `Active`)
            VALUES
            ('Fixed-width map with convex bottom shadow', '', 5,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" width=\"600px\" height=\"400px\"  margin-left=\"auto\" margin-right=\"auto\" margin-bottom=\"0px\" />',
            '<div align=\"center\" style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td align=\"center\" id=\"tdMap\" editable=\"0\" style=\"vertical-align:top;\"><div align=\"center\" id=\"divMap\" style=\"background-color: #EBEBEB;border-style:solid;border-width:1px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"></div></td></tr></table><img style=\"width:600px\" id=\"easy2mapIimgShadow\" src=\"[siteurl]images/map_templates/easy2map_map-shadow_bottom_2.png\"/></div>', 1, 0)";
            
            if (!$wpdb->query($SQLInsert7)){
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2map'));
		return;
            }
        }
    }
    

    /**     * Create custom post-type menu */
    public static function create_admin_menu() {
        add_menu_page('My Easy2Maps', // page title
                'Easy2Map', // menu title
                'manage_options', // capability 
                self::admin_menu_slug, // menu slug 
                'Easy2Map::get_admin_page', // callback 
                plugins_url('images/e2m_favicon2020.png', dirname(__FILE__)) //default icon
        );
        
        /*if (current_user_can('edit_posts') || current_user_can('edit_pages')){
            
            if(get_user_option('rich_editing') == 'true')
            {
                    add_filter("mce_external_plugins", "Easy2Map::add_easy2map_tinymce_plugin");
                    add_filter('mce_buttons', 'Easy2Map::register_easy2map_button');
            }
        }*/
        
    }
    
    /*public static function add_easy2map_tinymce_plugin($plugin_array) {
        $plugin_array['easy2map'] = plugins_url('easy2map-tinymce.php', dirname(__FILE__));
        return $plugin_array;
    }
    
    function register_easy2map_button($buttons) {
        array_push($buttons, "|", "easy2map");
        return $buttons;
     }*/

    /** Prints the administration page for this plugin. */
    public static function get_admin_page() {
        
        if (isset($_GET["action"]) && strcasecmp($_GET["action"], "addeditpins") == 0 && isset($_GET["map_id"])){
            include('AddEditMapPins.php');
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "edit") == 0 && isset($_GET["map_id"])){
            //include('AddEditMaps.php');
            //include('MapAdminister.php');
            include('MapAdmin.php');
        }else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "mappinimagesave") == 0 && isset($_GET["map_id"])){
            include('MapPinImageSave.php');
        }else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "mappreview") == 0 && isset($_GET["map_id"])){
            include('MapPreview.php');
        } else {
            include('MapManager.php');
        }
    }

    /**
     * * _is_searchable_page * 
     * * Any page that's not in the WP admin area is considered searchable. 
     * * @return boolean Simple true/false as to whether the current page is searchable. */
    private static function _is_searchable_page() {
        if (is_admin()) {
            return false;
        } else {
            return true;
        }
    }

    public static function retrieve_map($raw_args, $content = null) {
        $defaults = array('id' => '',);
        $sanitized_args = shortcode_atts($defaults, $raw_args);
        if (empty($sanitized_args['id'])) {
            return '';
        }
        
        $mapHTML = easy2map_retrieve_map_HTML( $sanitized_args['id']);
        $mapHTML = str_ireplace("divMap", "easy2map_canvas_" . $sanitized_args['id'], $mapHTML);
        
        return '
            <style type="text/css">
            #easy2map_canvas_' . $sanitized_args['id'] . ' img {
                max-width: none !important;
                border-radius: 0px !important;
                box-shadow: 0 0 0 transparent !important;
            }    
            #easy2map_canvas_' . $sanitized_args['id'] . ' table,td {
              margin: -1.5em 0 !important;
              padding: 0px !important;
            }
            </style><input type="hidden" id="easy2map_ajax_url_' . $sanitized_args['id'] . '" value="' . admin_url("admin-ajax.php") . '">' . $mapHTML;
    }
}

?>