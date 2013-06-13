<link href="<?php echo easy2mapimg_get_plugin_url('/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
<link href="<?php echo easy2mapimg_get_plugin_url('/css/bootstrap-wysihtml5.css'); ?>" rel="stylesheet" media="screen">
<link href="<?php echo easy2mapimg_get_plugin_url('/css/colorpicker.css'); ?>" rel="stylesheet" media="screen">
<link href="<?php echo easy2mapimg_get_plugin_url('/css/mapadmin.css'); ?>" rel="stylesheet" media="screen">

<script src="http://maps.google.com/maps/api/js?sensor=true&libraries=drawing,places"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/common.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/jquery.json2xml.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/jquery.xml2json.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/functions.imgmap.admin.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/functions.imgmappin.admin.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/bootstrap.min.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/wysihtml5-0.3.0.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/bootstrap-wysihtml5.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/carousel.js'); ?>"></script>
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/bootstrap-colorpicker.js'); ?>"></script>

<style type="text/css">
    #loadingImage{z-index:9999;position:fixed;left:50%;top:50%;width:75px;height:75px;margin-top:-37.5px;margin-left:-37.5px;border:1px single #FFFFFF;}
    #loadingImage img{border:none;}
    #loadingBackground{position:fixed;top:0;left:0;margin:0px;background-image: url("<?php echo easy2mapimg_get_plugin_url('/images/bg_white.png') ?>"); background-repeat: repeat; width:100%; height:100%; z-index: 9998}
    #easy2mapslider {padding-top:6px;display:none;position: absolute; bottom:0px;left:0px;z-index:99999;width: 100%; vertical-align: middle; height:67px;background-image: url("<?php echo easy2mapimg_get_plugin_url('/images/bg_grey.png') ?>"); background-repeat: repeat; }
</style>

<?php
include('CSSEditHTML.html');
$mapID = $_REQUEST["map_id"];
?>

<script>
    var $overlay, $styleElementIndex, $styleSelectedElement, $geocoder, $map, $mapSettings, $latlng, $arrTemplates, $mapPinID, $pinsArray = [], $markersArray = [], $selectedPin, $pinsClicked = [];
    var $pluginsURL = "<?php echo str_replace('index.php', '', easy2mapimg_get_plugin_url('/index.php')); ?>";
    var $mapID = <?php echo $mapID; ?>;
    
    jQuery.noConflict();
    
    jQuery("document").ready(function() {
        
        $geocoder = new google.maps.Geocoder();
        easy2map_imgmap_functions.retrieveMapSettings($mapID);
                
        //add autocomplete to the address search textbox
        var input = document.getElementById('address');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function(){
            
            var place = autocomplete.getPlace();
            easy2map_imgmappin_functions.SetPinPosition(place.geometry.location.lat(),place.geometry.location.lng());
        });
        
        jQuery('#pinDescription').wysihtml5({
            "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
            "emphasis": true, //Italics, bold, etc. Default true
            "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "link": true, //Button to insert a link. Default true'
            "html": true, //Button which allows you to edit the generated HTML. Default false
            "image": false, //Button to insert an image. Default true,
            "color": true //Button to change color of font  
        });
        
        notBusy();
        
        jQuery('#colourpicker').colorpicker().on('changeColor', function(ev){
            document.getElementById('txtDefaultValue_color').style.backgroundColor = ev.color.toHex();
        });
        
    });
    
    function showMapNameEdit(){
        
        jQuery('#mapName').val(jQuery('#mapName2').html()).toggle().select();
        jQuery('#mapEditPencil').toggle();
        jQuery('#mapName2').toggle();
        
    }
    
    function runMapNameEdit(e) {
        if (e.keyCode == 13) {
            document.getElementById('btnBack').focus();
            return false;
        }
    }
    
    function saveMapNameEdit(){
        
        var mapName = jQuery.trim(jQuery('#mapName').val()) == "" ? "Untitled Image Map" : jQuery.trim(jQuery('#mapName').val());
        
        jQuery('#mapName2').html(mapName).toggle();
        jQuery('#mapEditPencil').toggle();
        jQuery('#mapName').toggle();
        easy2map_imgmap_functions.saveMapName();
    }
    
    var easy2mapimg_functions = (function(){ 
        
        //prepare a pin item for editing
        return {
            clickPinItem : function(selectedPinID){
                
                if ($pinsClicked.indexOf(selectedPinID) === -1){
                    jQuery('#easy2mapmainimage').css({'background-image' : 'url(<?php echo easy2mapimg_get_plugin_url('/images/busy.gif'); ?>)', 'background-repeat': 'no-repeat', 'background-position': 'center'}).fadeIn('slow');
                    $pinsClicked.push(selectedPinID);
                }
                
                for (i = 0; i < $pinsArray.length; i++){

                    $pinsArray[i].setVisible(false);
                    if ($pinsArray[i].ID == selectedPinID){

                        $selectedPin = $pinsArray[i];
                        $selectedPin.setVisible(true);
                        $latlng = $selectedPin.position;

                        jQuery('<img/>').attr('src', $selectedPin.large).load(function() {
                            jQuery('#easy2mapslidertext').html($selectedPin.pinText).show();
                            jQuery('#easy2mapmainimage').css({'background-image' : 'url(' + $selectedPin.large + ')', 'background-repeat': 'no-repeat', 'background-position': 'center'}).fadeIn();
                            $map.setZoom(parseInt(jQuery('#markerZoom').val()));
                            $map.setCenter(new google.maps.LatLng($selectedPin.position.lat(), $selectedPin.position.lng()));
                        });
                    }
                }
                
            },
            noPinsLoaded : function(){
                jQuery('#easy2mapmainimage').css({'background-image' : 'url(<?php echo easy2mapimg_get_plugin_url('/images/frame.png') ?>)', 'background-repeat': 'no-repeat', 'background-position': 'center'}).fadeIn();
                        
            }
        }
        
    })();
    
    function pause(numberMillis) {
        var now = new Date();
        var exitTime = now.getTime() + numberMillis;
        while (true) {
            now = new Date();
            if (now.getTime() > exitTime)
                return;
        }
    }
    function busy(){
        jQuery('#loadingBackground').show();
        jQuery('#loadingImage').show();
        pause(150);
    }

    function notBusy(){
        jQuery('#loadingBackground').hide();
        jQuery('#loadingImage').hide();
    }
    
</script>

<div id="loadingBackground">
    <div id="loadingImage">
        <img src="<?php echo easy2mapimg_get_plugin_url('/images/busy.gif'); ?>"  />
    </div>
</div>

<div class="wrap" id="bodyTag">

    <form name="formAddPinImage" 
          target="frameAddPinIcon" 
          enctype="multipart/form-data" 
          id="formAddPinImage"
          action="?page=easy2mapimg&action=mappinimageupload&map_id=<?php echo $mapID; ?>"
          method="post">

        <input type="hidden" id="small" name="small">
        <input type="hidden" id="medium" name="medium">
        <input type="hidden" id="large" name="large">

        <table style="width:100%;margin-bottom:10px;" cellpadding="2" cellspacing="2">
            <tr><td style="width:50%;vertical-align:top;">

                    <h3><span style="cursor:pointer;" onclick="showMapNameEdit()" id="mapName2"></span>
                        <a id="mapEditPencil" href="#" style="display:none" onclick="showMapNameEdit()" class="smallE2MLink">edit</a>
                        <!---<i id="mapEditPencil" style="cursor:pointer;margin-top:8px;" onclick="showMapNameEdit()" class="icon-pencil"></i>--->

                        <input maxlength="128" name="mapName" onblur="saveMapNameEdit()"
                               onkeypress="return runMapNameEdit(event)"
                               id="mapName" value="" maxlength="128"
                               type="text" placeholder="Give your map a name" 
                               class="input-large" style="display:none;width:300px;margin-bottom:-6px" />

                        <!---<?php if (!isset($_REQUEST["no_back"])) { ?>
                                                                                                    <button onclick="window.location='?page=easy2mapimg&action=viewMaps'" type="button" 
                                                                                                            style="margin-left:30px;width:100px;" 
                                                                                                            class="btn">Back</button> 
                        <?php } ?>--->

                </td>
                <td nowrap align="center" id="TitleMapSize" style="text-align:center;width:50%;font-size:15px;">


                    <button id="btnSaveAndBack" onclick="easy2map_imgmap_functions.saveMap(true, true);" type="button" 
                            style="float:left;width:200px;"
                            class="btn btn-custom">Photo Map Completed</button>


                    <a id="btnBack" href="#" onclick="easy2map_imgmap_functions.saveMap(true, false);" type="button" 
                       style="float:right;margin-right:5px;">Back to Map Manager</a>


                </td>

            </tr>
        </table>

        <table style="width:100%" cellpadding="2" cellspacing="2">
            <tr><td style="width:34%;vertical-align:top;">
                    <div class="control-group" style="width:100%;">

                        <ul class="nav nav-pills">
                            <li class="active">
                                <a href="#easy2maptabs-1" data-toggle="tab">Photos</a>
                            </li>
                            <li><a href="#easy2maptabs-2" data-toggle="tab">Settings</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="easy2maptabs-1">

                                <p style="display:none;margin-bottom:20px;" id="AddMarkerOrSave">
                                    <a href="#" onclick="easy2map_imgmappin_functions.addNewMapMarker()">
                                        <img src="<?php echo easy2mapimg_get_plugin_url('/images/e2m_icon_add.png'); ?>" style="margin-right:10px;"> Add New Photo</a>

                                </p>

                                <table id="tblAddMapMarker" style="display:none;background-color:#EBEBEB;width:90%;" cellspacing="3" cellpadding="3" class="table table-bordered">
                                    <tr>
                                        <td id="AddEditPinTitle" colspan="2" class="instructions">Add New Photo
                                        </td>
                                    </tr>

                                    <tr id="pinImageUploadParent"><td colspan="2" align="center" style="text-align:center">

                                            <h5><input type='file' name='pinimage' 
                                                       id='pinimage' 
                                                       size='30' style="width:300px;vertical-align:middle;"
                                                       acceptedFileList='JPG;JPEG;PJPEG;GIF;PNG;X-PNG'
                                                       accept='image/*'></h5>
                                            <h6><i>Valid image files accepted (.jpg, .png, .gif)</i></h6>
                                            <button style="margin-right:auto;margin-left:auto;" class="btn btn-primary" data-dismiss="modal" 
                                                    onclick="easy2map_imgmappin_functions.uploadPinPicture()" aria-hidden="true">Upload Photo</button>

                                        </td></tr>

                                    <tr id="pinImageParent" style="display:none;"><td align="center" style="text-align:center" colspan="2">
                                            <img id="pinImagePreview">
                                        </td></tr>

                                <!---<tr id="pinNameParent" style="display:none;"><td colspan="2">

                                        <h5 style="margin-top:0px;">Image Title</h5>

                                        <input maxlength="128" name="pinName"
                                               id="pinName" value=""
                                               type="text" placeholder="Enter image title" 
                                               class="input-large" style="width:100%;margin:0;" />

                                    </td></tr>--->

                                    <tr id="pinDescriptionParent" style="display:none;">
                                        <td colspan="2" style="">

                                            <h5 style="margin-top:0px;">Give your photo/image a description (optional)</h5>
                                            <textarea style="width:100%;height:100px;margin:0;" placeholder="Enter description of photo here" id="pinDescription" name="pinDescription"></textarea>
                                        </td>
                                    </tr>

                                    <tr id="divAddressSearch" style="display:none;">
                                        <td colspan="2" style="vertical-align:middle;">
                                            <input class="input-xlarge" 
                                                   id="address" 
                                                   type="text"
                                                   placeholder="Enter address where this picture was taken" 
                                                   style="width:100%;margin:0">

                                        </td>
                                    </tr>
                                    <tr id="divDrag" style="display:none;">
                                        <td align="center" colspan="2" style="font-weight:bold;vertical-align:middle;text-align:center;font-size:13px">
                                            or drag the marker onto map
                                        </td>
                                    </tr>

                                    <tr id="divDrag2" style="display:none;">

                                        <td align="center" style="text-align:center;vertical-align:middle;">
                                            <button id="btnUploadIcon" onclick="easy2map_imgmappin_functions.openImagesDirectory(jQuery('#draggable').attr('src'))" type="button" 
                                                    style="margin-top:auto;margin-bottom:auto;width:120px;" 
                                                    class="btn">Change Icon</button>
                                        </td>

                                        <td align="center" style="text-align:center;vertical-align:middle;font-size:13px;" class="instructions">

                                            <img id="draganddrop" src="<?php echo easy2mapimg_get_plugin_url('/images/draganddrop.png'); ?>" 
                                                 style="float:right;">

                                            <img id="draggable" name="draggable"
                                                 style="z-index:9999;cursor:move;vertical-align:top"/>

                                        </td>
                                    </tr>

                                    <tr id="divPinAddEditParent" style="display:none;">
                                        <td style="text-align:left;" colspan="2">

                                            <button id="btnDeletePin" onclick="easy2map_imgmappin_functions.deleteSelectedPoint();" type="button" 
                                                    style="float:right;display:none;margin-top:10px;width:70px;" 
                                                    class="btn">Delete</button>

                                            <button id="btnSavePin" onclick="easy2map_imgmappin_functions.saveMapPin();" type="button" 
                                                    style="float:left;display:none;margin-left:5px;margin-top:10px;width:150px;" 
                                                    class="btn btn-custom">Save Photo</button>        

                                            <button id="btnCancelPin" onclick="easy2map_imgmappin_functions.retrieveMapPoints();" type="button" 
                                                    style="display:none;margin-left:15px;margin-top:10px;width:70px;" 
                                                    class="btn">Cancel</button>

                                        </td>
                                    </tr>
                                </table>
                                <table id="tblMapMarkers" style="margin-left:auto;margin-right:auto;display:none;width:320px;margin-top:20px;"  class="table table-striped">
                                </table>
                            </div>
                            <div class="tab-pane" id="easy2maptabs-2">
                                <?php require_once 'SettingsEdit.php'; ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width:1%"></td>
                <td style="width:65%;vertical-align:top;">
                    <div class="control-group" style="clear:both;width:100%;">
                        <div id="divPreview" style="width:100%;min-height:500px;"></div> 
                </td>
            </tr>
        </table>

    </form>
</div>
                    
<form name="formCopymapSettings" 
              id="formCopymapSettings"
              action="?page=easy2mapimg&action=copymapsettings&map_id=<?php echo $mapID; ?>"
              method="post">
<input type="hidden" name="CopyMapID" id="CopyMapID">
</form>                    

<form name="formAddPinIcon" 
      target="frameAddPinIcon" 
      enctype="multipart/form-data" 
      id="formAddPinIcon"
      action="?page=easy2mapimg&action=mappiniconsave&map_id=<?php echo $mapID; ?>"
      method="post">

    <div id="mapPinIconList" style="width:600px" 
         class="modal hide fade" tabindex="-1" 
         role="dialog" aria-labelledby="winSettingsModalLabel" data-keyboard="true" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3>My map icons</h3>
        </div>
        <div class="modal-body" style="max-height: 400px">
            <h5>                
                <?php if (self::easy2MapPhotoCodeValidator('') === false) { ?>
                    <i>(Please upgrade to the Pro Version to upload your own custom icons)</i>
                    <br><br>
                <?php } else { ?>  
                    Upload own icon: 
                    <input type='file' name='pinicon' 
                           id='pinicon' 
                           size='30' style="width:300px;vertical-align:middle;"
                           acceptedFileList='JPG;JPEG;PJPEG;GIF;PNG;X-PNG'
                           accept='image/*'></h5>
                <h6><i>Valid image files accepted (.jpg, .png, .gif) with a maximum file size of 5MB.</i></h6>
            <?php } ?>
            <table style="width:96%" 
                   cellpadding="4" 
                   cellspacing="4" id="tblPinImages"></table>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <?php if (self::easy2MapPhotoCodeValidator('') === true) { ?><button class="btn btn-primary" data-dismiss="modal" onclick="easy2map_imgmappin_functions.uploadPinIcon()" aria-hidden="true">Upload Icon</button><?php } ?>
        </div>
    </div> 

</form>


<div id="mapShortCode" style="width:700px" 
     class="modal hide fade" tabindex="-1" 
     role="dialog" aria-labelledby="winSettingsModalLabel" data-keyboard="true" aria-hidden="true">
    <div class="modal-header">
        <h3>Your photo map has been saved</h3>
    </div>
    <div  class="modal-body" style="font-size:16px;max-height:400px">
        Your map's short-code is&nbsp;&nbsp;:&nbsp;&nbsp;
        <input type="text" style="text-align:center;width:220px;font-size:1.1em;" id="txtShortCode">
        <br><br>
        <i>(copy and paste this code into your posts or pages to display your photo map)</i>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" onclick=" window.location = '?page=easy2mapimg&action=viewmaps'" aria-hidden="true">OK</button>
    </div>
</div>


<div id="photoUploadError" style="width:700px" 
     class="modal hide fade" tabindex="-1" 
     role="dialog" aria-labelledby="winSettingsModalLabel" data-keyboard="true" aria-hidden="true">
    <div class="modal-header">
        <h3>Error trying to upload your photo</h3>
    </div>
    <div  class="modal-body" style="font-size:16px;max-height:400px">
        There was an error trying to upload your photo.<br>
        Please check that the photo size does not exceed your website's maximum allowed file upload size, and try again.
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>


<iframe name="frameAddPinIcon" 
        id="frameAddPinIcon" 
        width="98%" height="98%" 
        frameborder="0" style="display:none;margin:auto;" 
        scrolling="auto" src=""></iframe>