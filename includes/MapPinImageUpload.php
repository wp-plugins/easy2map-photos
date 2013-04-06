<?php
include 'ImageFunctions.php';

$imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/pin_content/" . $_GET["map_id"] . "/";

if (is_uploaded_file($_FILES["pinimage"]['tmp_name'])) {

    if (!file_exists($imagesDirectory)) {
        mkdir($imagesDirectory);
    }

    $imageName = preg_replace("/[^A-Za-z0-9 ]/", '', $_FILES["pinimage"]['name']);
    
    $uploadedFile = $_FILES["pinimage"]['tmp_name'];
    $extension = strtolower(getExtension($imageName));

    list($width, $height, $type, $attr) = getimagesize($uploadedFile);
    
    $arrImage = array();
    if (extension_loaded('exif')) {
        $arrImage = exif_read_data($uploadedFile, 0, true);
    }
    
    $lat = 0;
    $lng = 0;
    
    if (isset($arrImage['GPS'])){
    
        $GPS = $arrImage['GPS'];
        
        if (isset($GPS['GPSLatitudeRef']) && isset($GPS['GPSLatitude'])){

            //LATITUDE
            $LatitudeRef = $GPS['GPSLatitudeRef'];
            $Latitude = $GPS['GPSLatitude'];
            
            $arrLatDegrees = explode("/", $Latitude[0]);
            $arrLatMinutes = explode("/", $Latitude[1]);
            $arrLatSeconds = explode("/", $Latitude[2]);
            
            $latDegrees = floatval($arrLatDegrees[0] / $arrLatDegrees[1]);
            $latMinutes = floatval($arrLatMinutes[0] / $arrLatMinutes[1]);
            $latSeconds = floatval($arrLatSeconds[0] / $arrLatSeconds[1]);
            
            echo $latDegrees . '|' . $latMinutes . '|' . $latSeconds . '|';

            //LONGITUDE
            $LongitudeRef = $GPS['GPSLongitudeRef'];
            $Longitude = $GPS['GPSLongitude'];
            
            $arrLngDegrees = explode("/", $Longitude[0]);
            $arrLngMinutes = explode("/", $Longitude[1]);
            $arrLngSeconds = explode("/", $Longitude[2]);
            
            $lngDegrees = floatval($arrLngDegrees[0] / $arrLngDegrees[1]);
            $lngMinutes = floatval($arrLngMinutes[0] / $arrLngMinutes[1]);
            $lngSeconds = floatval($arrLngSeconds[0] / $arrLngSeconds[1]);

            $lat = DMStoDEC($latDegrees,$latMinutes,$latSeconds);
            if (strcasecmp($LatitudeRef, "S") === 0) $lat = -1 * $lat;

            $lng = DMStoDEC($lngDegrees,$lngMinutes,$lngSeconds);
            if (strcasecmp($LongitudeRef, "W") === 0) $lng = -1 * $lng;
        }
    }
    
    echo $lat . '<br>' . $lng;
        
    $imageNameExplode = explode(".", $imageName);
    $imagePlusLocation = "";
    $arrPhotoSize = explode(",", str_ireplace("px", "", $_REQUEST["photoSize"]));        
    
    if ($_FILES["pinimage"]['size'] < 10000000) {
        $arrSmallImage = resizeImage($imagesDirectory, $uploadedFile, $imageName, $width, $height, $type, '25', '25', "SMALL", false);
        $imagePlusLocation_Small = WP_CONTENT_URL . "/uploads/easy2map/images/pin_content/" . $_GET["map_id"] . "/" . $arrSmallImage[0];
        
        $arrMediumImage = resizeImage($imagesDirectory, $uploadedFile, $imageName, $width, $height, $type, '90', '90', "MEDIUM", false);
        $imagePlusLocation_Medium = WP_CONTENT_URL . "/uploads/easy2map/images/pin_content/" . $_GET["map_id"] . "/" . $arrMediumImage[0];
        
        $arrLargeImage = resizeImage($imagesDirectory, $uploadedFile, $imageName, $width, $height, $type, $arrPhotoSize[0], $arrPhotoSize[1], "LARGE", true);
        $imagePlusLocation_Large = WP_CONTENT_URL . "/uploads/easy2map/images/pin_content/" . $_GET["map_id"] . "/" . $arrLargeImage[0];
        
    }
}

function DMStoDEC($deg,$min,$sec)
{

// Converts DMS ( Degrees / minutes / seconds ) 
// to decimal format longitude / latitude

    return $deg+((($min*60)+($sec))/3600);
} 

?>

<script type="text/javascript">

    window.onload = function(){
      <?php if (strlen($imagePlusLocation_Large) == 0) { ?>
        window.parent.easy2map_imgmappin_functions.imageNotSuccessfullyUploaded(); 
      <?php } else { ?>
        window.parent.easy2map_imgmappin_functions.imageSuccessfullyUploaded('<?php echo $imagePlusLocation_Small; ?>', 
           '<?php echo $imagePlusLocation_Medium; ?>', '<?php echo $imagePlusLocation_Large; ?>', <?php echo $lat; ?>, <?php echo $lng; ?>); 
      <?php } ?>     
    }

</script>
