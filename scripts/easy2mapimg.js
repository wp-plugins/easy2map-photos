jQuery(function() {
    easy2mapimg_functions.initialise_all_easy2imgmaps();
});

var easy2mapimg_functions = (function() {

    var $markersArray = [];
    var $markersClicked = [];
    var direction = 'right';
    var e = this;
    var i = 0;
    var speed = 200;
    var $mapsArray = [];
    var $mapZoomArray = [];

    easy2mapimg_ajax_location = function(mapID) {
        var arrMapID = mapID.split("_");
        var id = arrMapID[arrMapID.length - 1];
        return jQuery('#easy2mapimg_ajax_url_' + id).val();
    };

    easy2mapimg_creategooglemap = function(mapID, mapControl, mapOptions, markerzoom) {
        $mapsArray[mapID] = new google.maps.Map(document.getElementById(mapControl), mapOptions);
        $mapZoomArray[mapID] = markerzoom;
    };

    insertNewImgMapPoint = function(objMapPoint, map) {

        this.Marker = new google.maps.Marker({
            position: new google.maps.LatLng(objMapPoint.lattitude, objMapPoint.longitude),
            draggable: false,
            map: map,
            mapID: objMapPoint.mapID,
            title: objMapPoint.title.replace(/\\/gi, ''),
            ID: objMapPoint.ID,
            icon: objMapPoint.icon,
            pinHTML: objMapPoint.pinHTML,
            small: objMapPoint.small,
            medium: objMapPoint.medium,
            large: objMapPoint.large,
            pinText: objMapPoint.pinText,
            visible: false
        });

        //retrieve pin settings
        var pinSettings = jQuery.xml2json(objMapPoint.settings);

        var loadBehaviour = 1;
        var loadURL = "";

        if (pinSettings.load && pinSettings.load != "") {
            loadBehaviour = parseInt(pinSettings.load);
        }

        if (pinSettings.url && pinSettings.url != "" && pinSettings.url != "http://") {
            loadURL = pinSettings.url;
        }

        //if pin 'load' settings set to '1', open popup on pin click (default behaviour)
        if (loadBehaviour === 1 || (loadBehaviour === 3 && loadURL === "")) {
            google.maps.event.addListener(this.Marker, "click", function(mEvent) {
                //var infoWindow = new google.maps.InfoWindow();
                //infoWindow.setContent(marker.pinHTML);
                //infoWindow.open(marker.map, marker);
            });
        }
        //if pin 'load' settings set to '2', open popup on window load
        if (loadBehaviour === 2) {

            //var infoWindow = new google.maps.InfoWindow();
            //infoWindow.setContent(marker.pinHTML);
            // infoWindow.open(marker.map, marker);

        }
        //if pin 'load' settings set to '3', open URL
        if (loadBehaviour === 3 && loadURL !== "") {

            google.maps.event.addListener(this.Marker, "click", function(mEvent) {
                window.location.href = loadURL;
            });

        }
    };

    replaceAll = function(strOrig, strFind, strReplace) {

        var intCount = strOrig.indexOf(strFind);
        while (intCount !== -1)
        {
            strOrig = replaceChars(strOrig, intCount, strFind.length, strReplace);
            intCount = strOrig.indexOf(strFind);
        }
        return strOrig;
    };

    replaceChars = function(strOrig, intPos, intNoChars, strReplace) {
        if (intPos < 0)
            intPos = 0;
        if (intPos >= strOrig.length)
            intPos = strOrig.length - 1;
        if (intNoChars < 0)
            intNoChars = 0;
        if (intNoChars > strOrig.length)
            intNoChars = strOrig.length;
        return (strOrig.substring(0, intPos) + strReplace + strOrig.substring(intPos + intNoChars));
    };

    retrieve_easy2mapimg_pins = function(map, mapControl, arrMapPins) {

        var arrMapID = mapControl.split("_");
        var mapID = arrMapID[arrMapID.length - 1];

        var data = {
            action: 'e2m_img_retrieve_map_points',
            MapID: mapID,
            maxPins: 250
        };

        jQuery.ajax({
            type: "POST",
            url: easy2mapimg_ajax_location(mapControl),
            data: data,
            dataType: 'json',
            success: function(returnData) {

                if (typeof returnData == "undefined" || typeof returnData == "null")
                    return;
                if (!returnData)
                    return;
                if (returnData.length == 0)
                    return;

                for (var t = 0; t < returnData.length; t++) {

                    var arrLatLng = replaceAll(replaceAll(replaceAll(returnData[t].LatLong, ' ', ''), '(', ''), ')', '').split(',');

                    var objMapPoint = {
                        lattitude: arrLatLng[0],
                        longitude: arrLatLng[1],
                        title: returnData[t].Title,
                        ID: returnData[t].ID,
                        mapID: mapID,
                        small: returnData[t].PinImageSmall,
                        medium: returnData[t].PinImageMedium,
                        large: returnData[t].PinImageLarge,
                        icon: returnData[t].ImageURL,
                        settings: returnData[t].Settings,
                        pinHTML: returnData[t].MapPinHTML,
                        pinText: returnData[t].MapPinText
                    }
                    arrMapPins.push(objMapPoint);
                }

                for (var i = 0; i < arrMapPins.length; i++) {
                    var newMarker = new insertNewImgMapPoint(arrMapPins[i], map);
                    $markersArray.push(newMarker);
                }

                jQuery("#easy2mapslider" + mapID + "Parent li:first").each(function() {
                    easy2mapimg_functions.clickPinItem(jQuery(this).attr('sliderID'));
                });
            }
        });

        jQuery("#easy2mapslider" + mapID).css("display", "block");

        jQuery('#easy2mapslider' + mapID).find('.easy2mapnext').click(function() {

            easy2mapimg_functions.e2mslider_animate('right', mapID);
            return false;
        });

        jQuery('#easy2mapslider' + mapID).find('.easy2mapprev').click(function() {
            easy2mapimg_functions.e2mslider_animate('left', mapID);
            return false;
        });

        if (jQuery('#easy2mapslider' + mapID + 'Parent').find('li').length > 1) {

            jQuery("#easy2mapimg_canvas_" + mapID).hover(function() {
                jQuery("#easy2mapslider" + mapID).hide();

            });

            jQuery("#easy2mapmainimage" + mapID).hover(function() {

                if (jQuery('#easy2mapimg_canvas_' + mapID + ':hover').length === 0) {
                    jQuery("#easy2mapslider" + mapID).fadeIn();
                } else {
                    jQuery("#easy2mapslider" + mapID).hide();
                }

            }
            , function() {
                jQuery(this).find("#easy2mapslider" + mapID).fadeOut();
            }
            );

        } else {
            jQuery("#easy2mapslider" + mapID).hide();
        }

    };

    retrieve_easy2mapimg_settings = function(mapControl) {

        var arrMapPins = [];
        var arrMapID = mapControl.split("_");
        var mapID = arrMapID[arrMapID.length - 1];

        jQuery.ajax({
            type: 'POST',
            url: easy2mapimg_ajax_location(mapControl),
            dataType: 'json',
            data: {
                mapID: mapID,
                action: "e2m_img_retrieve_map_settings"
            },
            success: function(returnData) {

                var mapSettings = jQuery.xml2json(returnData.Settings);

                var $lat = mapSettings.lattitude;
                var $lng = mapSettings.longitude;
                var $zoom = parseInt(mapSettings.zoom);
                var $latlng = new google.maps.LatLng($lat, $lng);
                var $mapType = mapSettings.mapType.toUpperCase();
                var $mapTypeControl_style = mapSettings.mapTypeControl_style.toUpperCase();
                var $mapTypeControl_position = mapSettings.mapTypeControl_position.toUpperCase();
                var $zoomControlOptions_style = mapSettings.zoomControl_style.toUpperCase();
                var $zoomControlOptions_position = mapSettings.zoomControl_position.toUpperCase();
                var $scaleControlOptions_position = mapSettings.zoomControl_position.toUpperCase();

                if ($mapType === "ROADMAP")
                    $mapType = google.maps.MapTypeId.ROADMAP;
                else if ($mapType === "HYBRID")
                    $mapType = google.maps.MapTypeId.HYBRID;
                else if ($mapType === "SATELLITE")
                    $mapType = google.maps.MapTypeId.SATELLITE;
                else if ($mapType === "TERRAIN")
                    $mapType = google.maps.MapTypeId.TERRAIN;
                else
                    $mapType = google.maps.MapTypeId.ROADMAP;

                if ($mapTypeControl_style === "DEFAULT")
                    $mapTypeControl_style = google.maps.MapTypeControlStyle.DEFAULT;
                else if ($mapTypeControl_style === "DROPDOWN_MENU")
                    $mapTypeControl_style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
                else if ($mapTypeControl_style === "SATELLITE")
                    $mapTypeControl_style = google.maps.MapTypeControlStyle.SATELLITE;
                else
                    $mapTypeControl_style = google.maps.MapTypeControlStyle.DEFAULT;

                if ($mapTypeControl_position === "TOP_LEFT")
                    $mapTypeControl_position = google.maps.ControlPosition.TOP_LEFT;
                else if ($mapTypeControl_position === "TOP_RIGHT")
                    $mapTypeControl_position = google.maps.ControlPosition.TOP_RIGHT;
                else if ($mapTypeControl_position === "TOP_CENTER")
                    $mapTypeControl_position = google.maps.ControlPosition.TOP_CENTER;
                else
                    $mapTypeControl_position = google.maps.ControlPosition.TOP_RIGHT;

                if ($zoomControlOptions_style === "DEFAULT")
                    $zoomControlOptions_style = google.maps.ZoomControlStyle.DEFAULT;
                else if ($zoomControlOptions_style === "LARGE")
                    $zoomControlOptions_style = google.maps.ZoomControlStyle.LARGE;
                else
                    $zoomControlOptions_style = google.maps.ZoomControlStyle.SMALL;

                if ($zoomControlOptions_position === "TOP_LEFT")
                    $zoomControlOptions_position = google.maps.ControlPosition.TOP_LEFT;
                else if ($zoomControlOptions_position === "TOP_RIGHT")
                    $zoomControlOptions_position = google.maps.ControlPosition.TOP_RIGHT;
                else if ($zoomControlOptions_position === "TOP_CENTER")
                    $zoomControlOptions_position = google.maps.ControlPosition.TOP_CENTER;
                else
                    $zoomControlOptions_position = google.maps.ControlPosition.TOP_RIGHT;

                if ($scaleControlOptions_position === "TOP_LEFT")
                    $scaleControlOptions_position = google.maps.ControlPosition.TOP_LEFT;
                else if ($scaleControlOptions_position === "TOP_RIGHT")
                    $scaleControlOptions_position = google.maps.ControlPosition.TOP_RIGHT;
                else if ($scaleControlOptions_position === "TOP_CENTER")
                    $scaleControlOptions_position = google.maps.ControlPosition.TOP_CENTER;
                else
                    $scaleControlOptions_position = google.maps.ControlPosition.TOP_RIGHT;

                var mapOptions = {
                    zoom: $zoom,
                    center: $latlng,
                    mapTypeId: $mapType,
                    mapTypeControl: !!parseInt(mapSettings.mapTypeControl),
                    mapTypeControlOptions: {
                        style: $mapTypeControl_style,
                        position: $mapTypeControl_position
                    },
                    zoomControl: !!parseInt(mapSettings.zoomControl),
                    navigationControl: true,
                    zoomControlOptions: {
                        style: $zoomControlOptions_style,
                        position: $zoomControlOptions_position
                    },
                    scaleControl: !!parseInt(mapSettings.scaleControl),
                    scaleControlOptions: {
                        position: $scaleControlOptions_position
                    },
                    streetViewControl: !!parseInt(mapSettings.streetViewControl),
                    panControl: !!parseInt(mapSettings.panControl),
                    draggable: !!parseInt(mapSettings.draggable)
                };

                easy2mapimg_creategooglemap(mapID, mapControl, mapOptions, parseInt(mapSettings.markerzoom));
                google.maps.event.addDomListener(window, 'load', retrieve_easy2mapimg_pins($mapsArray[mapID], mapControl, arrMapPins));

            }
        });

    };

    return{
        //prepare a pin item for editing
        clickPinItem: function(selectedPinID) {
            
            for (var i = 0; i < $markersArray.length; i++) {

                var $selectedPin = $markersArray[i];

                if (parseInt($selectedPin.Marker.ID) === parseInt(selectedPinID)) {

                    $selectedPin.Marker.setVisible(true);

                    for (var j = 0; j < $markersArray.length; j++) {
                        if (parseInt($markersArray[j].Marker.mapID) === parseInt($selectedPin.Marker.mapID) && parseInt($markersArray[j].Marker.ID) !== parseInt(selectedPinID)) {
                            $markersArray[j].Marker.setVisible(false);
                        }
                    }

                    if ($markersClicked.indexOf(selectedPinID) === -1) {
                        jQuery('#easy2mapmainimage' + $selectedPin.Marker.mapID).css({
                            'background-image': 'url(' + $easy2mapphotowaitingImage + ')',
                            'background-repeat': 'no-repeat',
                            'background-position': 'center'
                        }).fadeIn('slow');
                        $markersClicked.push(selectedPinID);
                    }

                    jQuery('#easy2mapslider' + $selectedPin.Marker.mapID + 'text').html($selectedPin.Marker.pinText).show();

                    jQuery('#easy2mapmainimage' + $selectedPin.Marker.mapID)
                            .css({'background-image': 'none', 'background-color': '#EBEBEB'})
                            .fadeIn(800, function() {
                        jQuery(this).css({
                            'background-image': 'url(' + $selectedPin.Marker.large + ')',
                            'background-repeat': 'no-repeat',
                            'background-position': 'center'
                        });
                    });

                    $mapsArray[$selectedPin.Marker.mapID].setZoom(parseInt($mapZoomArray[$selectedPin.Marker.mapID]));
                    $mapsArray[$selectedPin.Marker.mapID].setCenter(new google.maps.LatLng($selectedPin.Marker.position.lat(), $selectedPin.Marker.position.lng()));

                }
            }

        },
        e2mslider_animate: function(new_dir, mapID)
        {
            //clearTimeout(timeout_id);
            //timeout_id = setTimeout(auto_animate, 5000);
            in_progress = true;

            var dir = direction;
            var slide_widths = jQuery('#easy2mapslider' + mapID).find('.easy2mapholder > li:first').width();

            if (new_dir)
            {
                dir = new_dir;
            }

            if (dir === 'right')
            {
                var toMove = jQuery('#easy2mapslider' + mapID).find('.easy2mapholder').children('li:first');
                var oldMargin = jQuery(toMove).css('margin-right');
                jQuery(toMove).animate({
                    'margin-left': '-' + slide_widths + 'px',
                    'margin-right': '0px'
                }, speed, null, function() {
                    jQuery(this).appendTo(jQuery(this).parent()).css({
                        'margin-left': '0px',
                        'margin-right': oldMargin
                    });
                    in_progress = false;
                });
            }
            else
            {
                jQuery('#easy2mapslider' + mapID).find('.easy2mapholder').children('li:eq(2)').animate({
                }, speed, null, function() {
                });
                jQuery('#easy2mapslider' + mapID).find('.easy2mapholder').children('li:last').css('margin-left', '-' + slide_widths + 'px').prependTo('.easy2mapholder').animate({
                    'margin-left': '0px'
                }, speed, null, function() {
                    in_progress = false;
                });
            }
        },
        initialise_all_easy2imgmaps: function() {

            jQuery('div [id ^= easy2mapimg_canvas_]').each(function() {

                var mapID = jQuery(this).attr('id');
                retrieve_easy2mapimg_settings(mapID);

            });

        }
    };

})();