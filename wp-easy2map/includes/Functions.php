<?php

if (!function_exists('easy2map_retrieve_map_pins_callback')):

    function easy2map_retrieve_map_pins_callback() {

        global $wpdb;
        $mapPointsTable = $wpdb->prefix . "easy2map_map_points";
        $returnValue = array();
        $mapID = $_REQUEST["MapID"];

        $mapPins = $wpdb->get_results($wpdb->prepare("SELECT * 
        FROM $mapPointsTable 
        WHERE MapID = '%s' 
        ORDER BY Title;", $mapID));

        foreach ($mapPins as $mapPin) {

            $mapPoint = new e2mMatchedPoint($mapPin->ID,
                            $mapPin->LatLong,
                            $mapPin->Title,
                            $mapPin->PinImageURL,
                            stripcslashes($mapPin->Settings),
                            stripcslashes($mapPin->DetailsHTML));

            array_push($returnValue, $mapPoint);
        }

        echo json_encode($returnValue);
        die;
    }

endif;

if (!function_exists('easy2map_save_map_polylines_callback')):

    function easy2map_save_map_polylines_callback() {

        global $wpdb;
        $mapsTable = $wpdb->prefix . "easy2map_maps";
        $mapID = $_REQUEST["mapID"];
        $PolyLines = urldecode($_REQUEST['PolyLines']);

        $wpdb->query(sprintf("UPDATE $mapsTable
        SET PolyLines = '%s'
        WHERE ID = '%s';", $PolyLines, $mapID));

        echo json_encode("");
        die;
    }

endif;

if (!function_exists('easy2map_on_uninstall_hook')):

    function easy2map_on_uninstall_hook() {

        if (EASY2MAP_PLUGIN_BOOTSTRAP != WP_UNINSTALL_PLUGIN) {
            return;
        }
        
        global $wpdb;
        $error =  "<div id='error' class='error'><p>%s</p></div>";
        $map_table = $wpdb->prefix . "easy2map_maps";
        $map_points_table = $wpdb->prefix . "easy2map_map_points";
        $map_point_templates_table = $wpdb->prefix . "easy2map_pin_templates";
        $map_templates_table = $wpdb->prefix . "easy2map_templates";
            
        $SQLMapPoints = "DROP TABLE `$map_points_table`";
        if (!$wpdb->query($SQLMapPoints)){
            echo sprintf($error, __("Could not drop easy2map map points table.", 'easy2map'));
            return;
        }
        
        $SQLMaps = "DROP TABLE `$map_table`";
        if (!$wpdb->query($SQLMaps)){
            echo sprintf($error, __("Could not drop easy2map map table.", 'easy2map'));
            return;
        }
        
        $SQLMapPointTemplates = "DROP TABLE `$map_point_templates_table`";
        if (!$wpdb->query($SQLMapPointTemplates)){
            echo sprintf($error, __("Could not drop easy2map map point templates table.", 'easy2map'));
            return;
        }
        
        $SQLMapTemplates = "DROP TABLE `$map_templates_table`";
        if (!$wpdb->query($SQLMapTemplates)){
            echo sprintf($error, __("Could not drop easy2map map templates table.", 'easy2map'));
            return;
        }
        
    }

endif;


if (!function_exists('easy2map_delete_map_point_callback')):

    function easy2map_delete_map_point_callback() {

        global $wpdb;
        $mapPointsTable = $wpdb->prefix . "easy2map_map_points";
        $mapPointID = $_REQUEST["MapPointID"];
        $wpdb->query($wpdb->prepare("DELETE FROM $mapPointsTable WHERE ID = '%s';", $mapPointID));
        echo json_encode("");
        die;
    }

endif;

if (!function_exists('easy2map_delete_map')):

    function easy2map_delete_map($mapID) {

        global $wpdb;
        $mapPointsTable = $wpdb->prefix . "easy2map_map_points";
        $mapsTable = $wpdb->prefix . "easy2map_maps";
        $wpdb->query($wpdb->prepare("DELETE FROM $mapPointsTable WHERE MapID = '%s';", $mapID));
        $wpdb->query($wpdb->prepare("DELETE FROM $mapsTable WHERE ID = '%s';", $mapID));
    }

endif;

if (!function_exists('easy2map_save_default_pin_image_callback')):

    function easy2map_save_default_pin_image_callback() {

        global $wpdb;
        $mapTable = $wpdb->prefix . "easy2map_maps";

        $MapID = $_REQUEST['MapID'];
        $MapPinImage = urldecode($_REQUEST['PinImage']);
        $wpdb->query($wpdb->prepare("UPDATE $mapTable SET DefaultPinImage = '%s' WHERE ID = '%s';", stripcslashes($MapPinImage), $MapID));
        echo json_encode($MapPinImage);
        die;
    }

endif;

function easy2map_get_plugin_url($fileAndLocation) {
    return plugins_url($fileAndLocation, dirname(__FILE__));
}

if (!function_exists('easy2map_save_map_pin')):

    function easy2map_save_map_pin() {

        global $wpdb;
        global $current_user;
        $current_user = wp_get_current_user();
        $mapPointsTable = $wpdb->prefix . "easy2map_map_points";
        $mapsTable = $wpdb->prefix . "easy2map_maps";

        if (isset($_REQUEST["mapPointID"]) && (int) $_REQUEST["mapPointID"] != 0) {

            //this is a map pin update

            $mapPointID = $_REQUEST["mapPointID"];

            if (!$wpdb->query($wpdb->prepare("
                UPDATE $mapPointsTable
                SET LatLong = '%s', PinImageURL = '%s', 
                Title = '%s', Settings = '%s', DetailsHTML = '%s'
                WHERE ID = %s;", 
                    $_REQUEST['latLong'], 
                    urldecode($_REQUEST['icon']), 
                    $_REQUEST['pinTitle'], 
                    $_REQUEST['pinSettingsXML'], 
                    urldecode($_REQUEST["pinHTML"]), 
                    $mapPointID))) {
                echo json_encode(0);
                die;
            }
        } else {

            $wpdb->query($wpdb->prepare("
            UPDATE $mapsTable
            SET isActive = 1
            WHERE ID = %s;", 
            $_REQUEST["mapID"]));
            
            //this is a map pin insert
            $wpdb->query($wpdb->prepare("
            INSERT INTO $mapPointsTable(
                MapID
                ,CreatedByUserID
                ,LatLong
                ,Title
                ,PinImageURL
                ,Settings
                ,DetailsHTML
            ) VALUES (%s, %s, '%s', '%s', 
                '%s', '%s', '%s');", 
                $_REQUEST["mapID"], 
                $current_user->ID, 
                $_REQUEST['latLong'], 
                $_REQUEST['pinTitle'], 
                $_REQUEST['icon'], 
                $_REQUEST['pinSettingsXML'], 
                $_REQUEST["pinHTML"]));

            $mapPointID = $wpdb->insert_id;
        }

        echo json_encode($mapPointID);
        die;
    }

endif;

if (!function_exists('easy2map_update_map_pin_location')):

    function easy2map_update_map_pin_location() {

        global $wpdb;
        $mapPointsTable = $wpdb->prefix . "easy2map_map_points";

        $wpdb->query($wpdb->prepare("
        UPDATE $mapPointsTable
        SET LatLong = '%s'
        WHERE ID = %s;", 
            $_REQUEST['latLong'], 
            $_REQUEST["mapPointID"]));
        
        echo json_encode("");
        die;
    }

endif;

if (!function_exists('easy2map_save_map')):

    function easy2map_save_map() {

        global $wpdb;
        global $current_user;
        $current_user = wp_get_current_user();
        $mapID = $_REQUEST["mapID"];
        $mapsTable = $wpdb->prefix . "easy2map_maps";

        if (intval($mapID) != 0) {

            //this is a map update
            $wpdb->query(sprintf("
                UPDATE $mapsTable
                SET TemplateID = '%s',
                    MapName = '%s',
                    Settings = '%s',
                    CSSValues = '%s',
                    MapHTML = '%s',
                    IsActive = 1
                WHERE ID = %s;", 
                    $_REQUEST['mapTemplateName'], 
                    $_REQUEST['mapName'], 
                    urldecode($_REQUEST['mapSettingsXML']), 
                    urldecode($_REQUEST["mapCSSXML"]), 
                    urldecode($_REQUEST["mapHTML"]), $mapID));
        } else {

            //this is a map insert
            if (!$wpdb->query(sprintf("
            INSERT INTO $mapsTable(
                TemplateID,
                MapName,
                DefaultPinImage,
                Settings,
                LastInvoked,
                PolyLines,
                CSSValues,
                MapHTML,
                IsActive
            ) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 0);", 
                    $_REQUEST['mapTemplateName'], 
                    $_REQUEST['mapName'], 
                    str_replace('index.php', '', easy2map_get_plugin_url('/index.php')) . "images/map_pins/pins/111.png",
                    urldecode($_REQUEST['mapSettingsXML']), 
                    getdate(), '', 
                    urldecode($_REQUEST["mapCSSXML"]), 
                    urldecode($_REQUEST["mapHTML"])))) {
                die("Error!");
            }

            $newRow = $wpdb->get_results("SELECT LAST_INSERT_ID() AS NewMapID;");

            foreach ($newRow as $row) {
                
                $mapID = $row->NewMapID;
                
                $imagesDirectory = WP_CONTENT_DIR . "/uploads/";
                
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }
                
                $imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/";
                
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }
                
                $imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/";
                
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }
                
                $imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/map_pins/";
                
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }
                
                $imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/map_pins/uploaded/";
                
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }
                
                $imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/map_pins/uploaded/" . $mapID . "/";
                
                if (!is_dir($imagesDirectory)) {
                    mkdir($imagesDirectory);
                }
            }
        }

        echo json_encode($mapID);
        die;
    }

endif;


if (!function_exists('easy2map_save_map_name')):

    function easy2map_save_map_name() {

        global $wpdb;
        $mapID = $_REQUEST["mapID"];
        $mapsTable = $wpdb->prefix . "easy2map_maps";
        
        $wpdb->query(sprintf("
            UPDATE $mapsTable
            SET MapName = '%s'
            IsActive = 1
            WHERE ID = %s;", 
                $_REQUEST['mapName'], 
                $mapID));

        echo json_encode($mapID);
        die;
    }

endif;



if (!function_exists('easy2map_retrieve_pin_icons_callback')):

    function easy2map_retrieve_pin_icons_callback() {
    
        $arrImages = array();
    
        //retrieve map pins
        $dirPins = EASY2MAP_PLUGIN_DIR . "/images/map_pins/pins/";
        $urlPins = plugins_url('/images/map_pins/pins/', dirname(__FILE__) . '/');
        
        if ($handlePins = opendir($dirPins)) {

            // iterate over the directory entries
            while (false !== ($entry = readdir($handlePins))) {

                // match on .php extension
                if (preg_match("/\.png$/", $entry)) {
                    array_push($arrImages, $urlPins . $entry);
                }

                if (preg_match("/\.jpg$/", $entry)) {
                    array_push($arrImages, $urlPins . $entry);
                }

                if (preg_match("/\.gif$/", $entry)) {
                    array_push($arrImages, $urlPins . $entry);
                }
            }

            // close the directory
            closedir($handlePins);
        } 

        //retrieve uploaded files
        if ((int)$_REQUEST['mapID'] != 0){
            $dirUploaded = WP_CONTENT_DIR . "/uploads/easy2map/images/map_pins/uploaded/" . $_REQUEST['mapID'] . "/";
            $urlUploaded = WP_CONTENT_URL . "/uploads/easy2map/images/map_pins/uploaded/" . $_REQUEST['mapID'] . "/";

            if ($handleUploaded = opendir($dirUploaded)) {

                // iterate over the directory entries
                while (false !== ($entry = readdir($handleUploaded))) {

                    // match on .php extension
                    if (preg_match("/\.png$/", $entry)) {
                        array_push($arrImages, $urlUploaded . $entry);
                    }

                    if (preg_match("/\.jpg$/", $entry)) {
                        array_push($arrImages, $urlUploaded . $entry);
                    }

                    if (preg_match("/\.gif$/", $entry)) {
                        array_push($arrImages, $urlUploaded . $entry);
                    }
                }

                // close the directory
                closedir($handleUploaded);
            }
        }

        echo json_encode($arrImages);
        die;
    }

endif;


if (!function_exists('easy2map_retrieve_map_settings')):

    function easy2map_retrieve_map_settings($mapID) {

        global $wpdb;
        $mapTable = $wpdb->prefix . "easy2map_maps";

        if (intval($mapID) === 0) {

            $settings = new e2mMapItem("0",
                            "1",
                            "",
                            str_replace('index.php', '', easy2map_get_plugin_url('/index.php')) . "images/map_pins/pins/111.png",
                            '<settings lattitude="9.51119363015591" longitude="15.191190643725605" zoom="2" clusterpins="1" mapType="ROADMAP" width="800" height="600" backgroundColor="B52932" draggable="1" scrollWheel="1" mapTypeControl="1" mapTypeControl_style="DROPDOWN_MENU" mapTypeControl_position="TOP_RIGHT" panControl="1" panControl_position="TOP_LEFT" rotateControl="1" rotateControl_position="TOP_LEFT" scaleControl="1" scaleControl_position="TOP_LEFT" streetViewControl="1" streetViewControl_position="TOP_LEFT" zoomControl="1" zoomControl_position="TOP_LEFT" zoomControl_style="LARGE" polyline_strokecolor="000000" polyline_opacity="1.0" polyline_strokeweight="1"/>',
                            '', '');

            return $settings;
        }

        $mapSettings = $wpdb->get_results($wpdb->prepare("SELECT * 
        FROM $mapTable 
        WHERE ID = '%s';", $mapID));

        foreach ($mapSettings as $row) {

            $settings = new e2mMapItem($row->ID,
                            $row->TemplateID,
                            $row->MapName,
                            $row->DefaultPinImage,
                            $row->Settings,
                            $row->CSSValues,
                            $row->PolyLines);

            return $settings;
        }

        return null;
    }

endif;


if (!function_exists('easy2map_retrieve_map_HTML')):

    function easy2map_retrieve_map_HTML($mapID) {

        global $wpdb;
        $mapTable = $wpdb->prefix . "easy2map_maps";

        $mapHTML = $wpdb->get_results($wpdb->prepare("SELECT MapHTML 
        FROM $mapTable 
        WHERE ID = '%s';", $mapID));

        foreach ($mapHTML as $row) {
            return $row->MapHTML;
        }

        return "";
    }

endif;


if (!function_exists('easy2map_retrieve_map_settings_callback')):

    function easy2map_retrieve_map_settings_callback() {

        $mapID = $_REQUEST["mapID"];
        $settings = easy2map_retrieve_map_settings($mapID);
        echo json_encode($settings);
        die;
    }

endif;



if (!function_exists('easy2map_retrieve_map_templates_callback')):

    function easy2map_retrieve_map_templates_callback() {

        global $wpdb;
        $mapsTable = $wpdb->prefix . "easy2map_maps";
        $templatesTable = $wpdb->prefix . "easy2map_templates";
        $returnValue = array();
        $mapID = $_REQUEST["mapID"];

        $templates = $wpdb->get_results($wpdb->prepare("SELECT A.ID, 
            IFNULL(B.TemplateID,1) AS SelectedTemplate,
            A.TemplateName, A.ExampleImage, IFNULL(B.CSSValues, A.CSSValues) AS CSSValues
            ,IFNULL(B.MapHTML, A.TemplateHTML) AS TemplateHTML, 
            IFNULL(A.StyleParentOnly,0) AS StyleParentOnly
            FROM $templatesTable A
            LEFT JOIN $mapsTable B ON (A.ID = B.TemplateID AND B.ID = %s)
            WHERE A.Active = 1    
            ORDER BY A.DisplayOrder;", $mapID));

        foreach ($templates as $template) {

            $mapTemplate = new e2mMapTemplate($template->ID,
                            $template->SelectedTemplate,
                            $template->TemplateName,
                            $template->ExampleImage,
                            stripcslashes($template->CSSValues),
                            stripcslashes($template->TemplateHTML),
                            $template->StyleParentOnly);

            array_push($returnValue, $mapTemplate);
        }

        echo json_encode($returnValue);
        die;
    }

endif;


if (!function_exists('easy2map_retrieve_mappin_templates_callback')):

    function easy2map_retrieve_mappin_templates_callback() {

        global $wpdb;
        $templatesTable = $wpdb->prefix . "easy2map_pin_templates";
        $returnValue = array();

        $templates = $wpdb->get_results("SELECT * FROM $templatesTable
        ORDER BY TemplateName");

        foreach ($templates as $template) {

            $mapTemplate = new e2mMapPinTemplate($template->ID,
                            $template->TemplateName,
                            stripcslashes($template->TemplateHTML));

            array_push($returnValue, $mapTemplate);
        }

        echo json_encode($returnValue);
        die;
    }

endif;

class e2mMapItem {

    public $ID;
    public $TemplateID;
    public $MapName;
    public $DefaultPinImage;
    public $Settings;
    public $CSSValues;
    public $PolyLines;

    public function __construct($ID, $TemplateID, $MapName, $DefaultPinImage, $Settings, $CSSValues, $PolyLines) {
        $this->ID = $ID;
        $this->TemplateID = $TemplateID;
        $this->MapName = $MapName;
        $this->DefaultPinImage = $DefaultPinImage;
        $this->Settings = $Settings;
        $this->CSSValues = $CSSValues;
        $this->PolyLines = $PolyLines;
    }

}

class e2mMapTemplate {

    public $ID;
    public $SelectedTemplate;
    public $TemplateName;
    public $ExampleImage;
    public $CSSValues;
    public $TemplateHTML;
    public $StyleParentOnly;

    public function __construct($ID, $SelectedTemplate, $TemplateName, $ExampleImage, $CSSValues, $TemplateHTML, $StyleParentOnly) {

        $this->ID = $ID;
        $this->SelectedTemplate = $SelectedTemplate;
        $this->TemplateName = $TemplateName;
        $this->ExampleImage = $ExampleImage;
        $this->CSSValues = $CSSValues;
        $this->TemplateHTML = $TemplateHTML;
        $this->StyleParentOnly = $StyleParentOnly;
    }

}

class e2mMapPinTemplate {

    public $ID;
    public $TemplateName;
    public $TemplateHTML;

    public function __construct($ID, $TemplateName, $TemplateHTML) {

        $this->ID = $ID;
        $this->TemplateName = $TemplateName;
        $this->TemplateHTML = $TemplateHTML;
    }

}

class e2mMatchedPoint {

    public $ID;
    public $LatLong;
    public $Title;
    public $ImageURL;
    public $Settings;
    public $MapPinHTML;

    public function __construct($ID, $LatLong, $Title, $ImageURL, $Settings, $MapPinHTML) {

        $this->ID = $ID;
        $this->LatLong = $LatLong;
        $this->Title = $Title;
        $this->ImageURL = $ImageURL;
        $this->Settings = $Settings;
        $this->MapPinHTML = $MapPinHTML;
    }

}

?>
