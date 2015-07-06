<?php

if (self::easy2MapPhotoCodeValidator('') === false) {
    echo '<div style="color:#70aa00;width:90%;text-align:center;margin-bottom:5px;font-weight:bold;">Please upgrade to the Pro Version to edit settings</div>';
}
echo '<table style="background-color:#EBEBEB;width:90%;" cellspacing="2" cellpadding="2" class="table table-bordered"><tr><td colspan="2" class="instructions">Photo Map Layout</td></tr><tr><td colspan="2"><select ';
if (self::easy2MapPhotoCodeValidator('') === false)
    echo 'disabled="disabled"';
echo ' onchange="easy2map_imgmap_functions.changeMapTemplate()" size="8" id="MapTemplateName" name="MapTemplateName" style="font-size:12px;width:300px"></select></td></tr>';
echo '<tr><td><h5>Photo Size</h5></td><td><select name="photoSize" id="photoSize" proVersion="';
echo self::easy2MapPhotoCodeValidator('') . '"';
echo ' onchange="easy2map_imgmap_functions.changePhotoSize(jQuery(this).val())" style="width:150px;"></select></td></tr>';
echo '<tr><td><h5>Map Size</h5></td><td><select name="mapSize" id="mapSize" onchange="easy2map_imgmap_functions.changeMapSize(jQuery(this).val())" style="width:150px"></select></td></tr>'
 . '<tr><td><h5>Map Type</h5></td><td><select name="mapType" id="mapType" onchange="easy2map_imgmap_functions.changeMapSize(jQuery(\'#mapSize\').val())" style="width:150px">'
 . '<option value="HYBRID">HYBRID</option><option value="ROADMAP">ROADMAP</option><option value="SATELLITE">SATELLITE</option><option value="TERRAIN">TERRAIN</option></select></td></tr>'
 . '<tr><td><h5>Default Map Zoom</h5></td><td><select name="markerZoom" id="markerZoom" onchange="easy2map_imgmap_functions.changeMapSize(jQuery(\'#mapSize\').val())" style="width:50px">'
 . '<option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>'
 . '<option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option>'
 . '<option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option>'
 . '<option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option>'
 . '<option value="20">20</option></select></td></tr>'
 . '<tr><td colspan="2"><ul class="nav nav-pills"><li class="active"><a href="#MapTemplateCSS" data-toggle="tab">Template Style</a></li><li><a href="#MapTemplatePhotoCSS" data-toggle="tab">Photo Style</a></li><li><a href="#MapTemplateMapCSS" data-toggle="tab">Map Style</a></li></ul>'
 . '<div class="tab-content"><div class="tab-pane active" id="MapTemplateCSS"></div><div class="tab-pane" id="MapTemplatePhotoCSS"></div><div class="tab-pane" id="MapTemplateMapCSS"></div></div></td></tr></table>';

$activeMaps = easy2mapimg_retrieve_active_maps();

if (isset($activeMaps) && count($activeMaps) > 0) {

    echo '<table style="background-color:#EBEBEB;width:90%;" cellspacing="2" cellpadding="2" class="table table-bordered">'
    . '<tr><td class="instructions">Copy settings from another photo map</td></tr>'
    . '<tr><td><select size="3" id="CopyMapSettings" name="CopyMapSettings" ';

    if (self::easy2MapPhotoCodeValidator('') === false)
        echo 'disabled="disabled"';
    echo 'style="font-size:12px;width:300px">';
    foreach ($activeMaps as $activeMap) {
        echo '<option value="' . $activeMap->ID . '">' . $activeMap->MapName . '</option>';
    }
    echo '</select></td></tr><tr><td style="text-align:center"><button onclick="easy2map_imgmap_functions.copyMapSettings()" class="btn" data-dismiss="modal" aria-hidden="true">Copy Map Settings</button></td></tr></table>';
}
   
?>