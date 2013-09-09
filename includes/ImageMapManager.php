<link href="<?php echo easy2mapimg_get_plugin_url('/css/bootstrap.css'); ?>" rel="stylesheet" media="screen">
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/bootstrap.min.js'); ?>"></script>

<style type="text/css">

    #MapManager{
        margin-left:auto;
        margin-right:auto;
        margin-top:10px;
        width:98%;
    }

    #MapManager td{
        border:1px solid #f2ecec;
        border-radius:2px;        
    }

    mcm-control-group{
        border:1px solid #EBEBEB;
        padding:5px;
        border-radius:5px;
        background-color: #EBEBEB;
    }

    .mcm-control-label{
        width:30%;
        font-weight:bold;
        padding-right:10px;
    }

</style>

<script>

    function areYouSure(mapID){
        jQuery('#btnDeleteMap').click(function(){
            window.location='?page=easy2mapimg&action=deletemap&map_id=' + mapID;
        });
        jQuery('#are_you_sure').modal();
    }

</script>

<?php
global $wpdb;
$mapsTable = $wpdb->prefix . "easy2mapimg_maps";
$mapPinTable = $wpdb->prefix . "easy2mapimg_map_points";
if (isset($_POST["mapName"])) {
    easy2map_e2m_img_save_map();
}

if (isset($_GET["action"]) && strcasecmp($_GET["action"], "deletemap") == 0 && isset($_GET["map_id"])){
    easy2mapimg_delete_map($_GET["map_id"]);
}

?>

<div class="control-group mcm-control-group" style="margin-left:auto;margin-right:auto;width:90%;margin-top:10px;border:1px solid #EBEBEB;padding:5px;border-radius:5px;background:url(<?php echo easy2mapimg_get_plugin_url('/images/easy2mapphotos.png'); ?>) no-repeat;background-color:#EBEBEB;background-position: 2px 1px;">
    <h5 style="line-height:6px;margin-left:25px;">&nbsp;
        
        <a style="margin-top:-10px;float:right;margin-right:5%;font-size:20px;" href="?page=easy2mapimg&action=edit&map_id=0">
                            <img src="<?php echo easy2mapimg_get_plugin_url('/images/e2m_icon_add.png'); ?>" style="margin-right:10px;"> Create New Photo Map</a>
        
        <?php if (self::easy2MapPhotoCodeValidator('') === false) { ?>
            <a style="float:right;margin-right:10%;font-size:1.25em;color:#70aa00;" href="?page=easy2mapimg&action=activation">Upgrade to Pro Version</a>
        <?php } else {?>
            <span style="float:right;margin-right:10%;font-size:1.3em;color:#70aa00;margin-top:-5px;"><img src="<?php echo easy2mapimg_get_plugin_url('/images/tick_small.png'); ?>" style="margin-right:5px;" />Pro Version</span>
            
         <?php }?>    
        
    </h5>
</div>

<div class="wrap">

    <table id="MapManager" cellspacing="4" style="width:90%;margin-left:auto;margin-right:auto;" class="table table-striped table-bordered">
        <tr>
            <th style="width:15%">Example Image</th>
            <th style="width:35%"><b>Photo Map Name</b></th>
            <th style="width:20%"><b>Short Code</b></th>
            <th style="width:15%;text-align:center"><b>Edit</b></th>
            <th style="width:15%;text-align:center"><b>Delete</b></th>
        </tr>

        <?php $results = $wpdb->get_results("SELECT * FROM $mapsTable WHERE IsActive = 1 ORDER BY ID DESC;");
        //if (count($results) == 0) header('Location: ?page=easy2mapimg&action=edit&map_id=0&no_back=true'); 

        foreach ($results as $result) {
            
            $firstImage = $wpdb->get_results($wpdb->prepare("SELECT PinImageMedium FROM $mapPinTable WHERE MapID = %s LIMIT 1;", $result->ID));
            if (count($firstImage) === 0) continue;            
            
            $id = $result->ID;
            $name = stripslashes($result->MapName);
            
            $xmlSettings = simplexml_load_string($result->Settings);
            $xmlAttrs = $xmlSettings->attributes();
            ?>
            <tr id="trMap<?php echo $id; ?>">
                <td align="center" style="text-align:center">
                    <?php foreach ($firstImage as $image) { ?>
                    <img style="border:1px solid #EBEBEB" 
                         src="<?php echo $image->PinImageMedium; ?>"></img>
                    <?php } ?>                    
                </td>
                <td style="font-size:16px;font-weight:bold;"><?php echo $name; ?></td>
                <td nowrap><p nowrap style="text-align:center;font-size:1.2em;color:#033c90;padding:5px;background-color:#e7e7e7;border:1px solid #5b86c5;border-radius:3px;width:180px;">[easy2mapimg id=<?php echo $id; ?>]</p>
                </td>
                <td style="text-align:center;vertical-align:middle;"><a href="?page=easy2mapimg&action=edit&map_id=<?php echo $id; ?>">
                    <img src="<?php echo easy2mapimg_get_plugin_url('/images/e2m_icon_edit.png'); ?>"></a></td>
                <td style="text-align:center;vertical-align:middle;"><a onclick="areYouSure(<?php echo $id; ?>);" href="#"><img src="<?php echo easy2mapimg_get_plugin_url('/images/e2m_icon_delete.png'); ?>"></a></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php if (count($results) > 0) { ?>
    <a style="float:left;margin-left:5%;font-size:1.1em;font-weight:bold;text-decoration: underline;" href="http://wordpress.org/plugins/easy2map-photos/" target="_blank">If you like this plugin, please rate us on Wordpress</a>
    <a style="float:right;margin-right:5%;font-size:1.1em;font-weight:bold;text-decoration: underline;" href="http://easy2map.com/contactUs.php" target="_blank">Your comments and feedback are always welcome</a>
    <?php } ?>

</div>

<div id="are_you_sure" style="width:600px" 
     class="modal hide fade" tabindex="-1" 
     role="dialog" aria-labelledby="winSettingsModalLabel" data-keyboard="true" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3>Are you *sure* you want to delete this map?</h3>
    </div>
    <div class="modal-body" style="max-height: 300px">
        This action cannot be reversed!
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button id="btnDeleteMap" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Delete This Map</button>
    </div>
</div>




