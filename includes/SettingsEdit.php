<?php

$premiumYN = self::easy2MapCodeValidator(get_option('easy2map-key'));

if ($premiumYN === false) {
    echo '<div style="color:#70aa00;width:90%;text-align:center;margin-bottom:5px;font-weight:bold;">Please upgrade to the Ultimate Version to edit settings</div>';
}

echo '<h5 style="margin-top:15px">Map Type</h5>' 
. '<select proVersion="';
echo $premiumYN . '" onclick="easy2map_map_functions.changeMapType();" size="1" 
id="mapType" name="mapType" style="font-size:12px;width:300px;"';
if ($premiumYN === false){
    echo ' disabled="disabled" ' ;
}
echo '><option value="HYBRID">HYBRID</option><option selected="selected" value="ROADMAP">ROADMAP</option><option value="SATELLITE">SATELLITE</option><option value="TERRAIN">TERRAIN</option></select>';
echo '<h5 style="margin-top:15px">Map Template</h5>'
 . '<select onclick="easy2map_map_functions.changeMapTemplate()" size="11" 
                                    id="MapTemplateName" name="MapTemplateName" 
                                    style="display:block;font-size:12px;width:300px"';
if ($premiumYN === false){
    echo ' disabled="disabled"' ;
}
echo '></select>'
. '<h5 style="margin-top:15px">Map Style</h5>' 
. '<ul class="nav nav-pills"><li class="active"><a href="#MapTemplateCSS" data-toggle="tab">Map</a></li><li><a href="#MapTemplateListCSS" data-toggle="tab">Markers List</a></li><li><a href="#MapTemplateHeadingCSS" data-toggle="tab">Map Heading</a></li></ul>'        
. '<div class="tab-content"><div class="tab-pane active" id="MapTemplateCSS"></div><div class="tab-pane" id="MapTemplateListCSS"></div><div class="tab-pane" id="MapTemplateHeadingCSS"></div></div>';    

echo '<h5 style="margin-top:15px">Map Import / Export</h5>';
if (intval($mapID) > 0) {
    
    if ($premiumYN === false) {
        
    } else {
        echo '<h5><a href="#" onclick="document.formExport.submit();">Export map (excluding markers)</a></h5>'
        . '<h5 style="margin-top:10px;"><a href="#" onclick="document.formExport2.submit()">Export map (including markers)</a></h5>';
    }
} else {
   if ($premiumYN === false) {
        
    } else {
        echo '<h6>(Please save your map before attempting to export it)</h6>';
    } 
    
}
if ($premiumYN === false) {
    
} else {
    
    echo '<h5 style="margin-top:10px;"><a href="#" onclick="document.formImport.submit()">Import map &amp; markers</a></h5>';
    
    if (intval($mapID) > 0) {
        echo '<h5 style="margin-top:10px;"><a href="#" onclick="document.formImport2.submit()">Import markers only</a></h5>';
    }
}
?>