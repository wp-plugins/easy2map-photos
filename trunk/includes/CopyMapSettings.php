<link href="<?php echo easy2map_get_plugin_url('/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
<script src="<?php echo easy2map_get_plugin_url('/scripts/bootstrap.min.js'); ?>"></script>
<script src="<?php echo easy2map_get_plugin_url('/scripts/functions.map.admin.js'); ?>"></script>
<script src="<?php echo easy2map_get_plugin_url('/scripts/common.js'); ?>"></script>
<?php
if (self::easy2MapPhotoCodeValidator('') === false) {
    die('<div style="color:#70aa00;width:90%;text-align:center;margin-bottom:5px;font-weight:bold;">Please upgrade to the Ultimate Version to access this functionality</div>');
}
$mapID = $_REQUEST["map_id"];
$copyMap = $_REQUEST["CopyMapID"];
global $wpdb;
global $current_user;
$current_user = wp_get_current_user();
$mapsTable = $wpdb->prefix . "easy2mapimg_maps";

if (intval($mapID) === 0) {

    $SQL = $wpdb->prepare("INSERT INTO $mapsTable
                (TemplateID,MapName,MapTitle,DefaultPinImage, Settings, LastInvoked, 
                PolyLines, CSSValues, CSSValuesPhoto, CSSValuesMap, IsActive)
                SELECT TemplateID, 'Untitled Photo Map', MapTitle, 
                DefaultPinImage,Settings, CURRENT_TIMESTAMP, PolyLines, 
                CSSValues, CSSValuesPhoto,CSSValuesMap, 1
                FROM $mapsTable WHERE ID = '%s';", $copyMap);
    
    if (!$wpdb->query($SQL)) {
        die("Error!");
    }

    $newRow = $wpdb->get_results("SELECT LAST_INSERT_ID() AS NewMapID;");

    foreach ($newRow as $row) {
        $mapID = $row->NewMapID;
    }
    
} else {

    $mapDetails = $wpdb->get_results($wpdb->prepare("SELECT * FROM $mapsTable WHERE ID = %s;", $copyMap));
    foreach ($mapDetails as $map) {

        $SQL = $wpdb->prepare("UPDATE $mapsTable
        SET
        TemplateID = '%s',
        DefaultPinImage ='%s',
        Settings = '%s',
        PolyLines = '%s',
        CSSValues = '%s',
        CSSValuesPhoto = '%s',
        CSSValuesMap = '%s'
        WHERE ID = '%s';", 
                $map->TemplateID, 
                $map->DefaultPinImage, 
                $map->Settings, 
                $map->PolyLines, 
                $map->CSSValues, 
                $map->CSSValuesPhoto, 
                $map->CSSValuesMap,
                $mapID);
        
        //update map settings
        $wpdb->query($SQL);
    }

} ?>

<script> jQuery(function() { 
    window.location = "?page=easy2mapimg&action=edit&map_id=<?php echo $mapID; ?>";});
</script>