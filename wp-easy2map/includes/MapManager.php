<link href="<?php echo easy2map_get_plugin_url('/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script src="<?php echo easy2map_get_plugin_url('/scripts/bootstrap.min.js'); ?>"></script>

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
            window.location='?page=easy2map&action=deletemap&map_id=' + mapID;
        });
        jQuery('#are_you_sure').modal();
    }

</script>

<?php
global $wpdb;
$mapsTable = $wpdb->prefix . "easy2map_maps";
if (isset($_POST["mapName"])) {
    easy2map_save_map();
}

if (isset($_GET["action"]) && strcasecmp($_GET["action"], "deletemap") == 0 && isset($_GET["map_id"])){
    easy2map_delete_map($_GET["map_id"]);
}

?>

<div class="control-group mcm-control-group" style="margin-left:auto;margin-right:auto;width:90%;margin-top:10px;border:1px solid #EBEBEB;padding:5px;border-radius:5px;background:url(<?php echo easy2map_get_plugin_url('/images/e2m_favicon3030.png'); ?>) no-repeat;background-color:#EBEBEB;background-position: 1px 3px;">
    <h5 style="line-height:6px;margin-left:25px;">
        My Easy2Maps
        
        <a style="margin-top:-10px;float:right;margin-right:5%;font-size:20px;" href="?page=easy2map&action=edit&map_id=0">
                            <img src="<?php echo easy2map_get_plugin_url('/images/e2m_icon_add.png'); ?>" style="margin-right:10px;"> Create New Map</a>
        
        
    </h5>
</div>

<div class="wrap">

    <table id="MapManager" cellspacing="4" style="width:90%;margin-left:auto;margin-right:auto;" class="table table-striped table-bordered">
        <tr>
            <th>Map Center</th>
            <th><b>Map Name</b></th>
            <th><b>Short Code</b></th>
            <th style="text-align:center"><b>Edit Map</b></th>
            <th style="text-align:center"><b>Delete Map</b></th>
        </tr>

        <?php $results = $wpdb->get_results("SELECT * FROM $mapsTable WHERE IsActive = 1 ORDER BY ID DESC;");
        //if (count($results) == 0) header('Location: ?page=easy2map&action=edit&map_id=0&no_back=true'); 

        foreach ($results as $result) {
            $id = $result->ID;
            $name = stripslashes($result->MapName);
            
            $xmlSettings = simplexml_load_string($result->Settings);
            $xmlAttrs = $xmlSettings->attributes();
            ?>
            <tr id="trMap<?php echo $id; ?>">
                <td align="center" style="text-align:center">
                    
                    <img style="border:1px solid #EBEBEB" 
                         src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $xmlAttrs['lattitude']. ','. $xmlAttrs['longitude']; ?>&zoom=<?php echo $xmlAttrs['zoom']; ?>&size=80x80&maptype=roadmap&sensor=false"></img>
            
                    
                </td>
                <td style="width:30%;font-size:16px;font-weight:bold;"><?php echo $name; ?></td>
                <td nowrap><p nowrap style="text-align:center;font-size:1.2em;color:#033c90;padding:5px;background-color:#e7e7e7;border:1px solid #5b86c5;border-radius:3px;width:180px;">[easy2map id="<?php echo $id; ?>"]</p>
                </td>
                <td style="width:15%;text-align:center;vertical-align:middle;"><a href="?page=easy2map&action=edit&map_id=<?php echo $id; ?>">
                    <img src="<?php echo easy2map_get_plugin_url('/images/e2m_icon_edit.png'); ?>"></a></td>
                <td style="width:15%;text-align:center;vertical-align:middle;"><a onclick="areYouSure(<?php echo $id; ?>);" href="#"><img src="<?php echo easy2map_get_plugin_url('/images/e2m_icon_delete.png'); ?>"></a></td>
            </tr>
            <?php
        }
        ?>
    </table>

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




