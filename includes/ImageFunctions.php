<?php

function resizeImage($imagesDirectory, $uploadedFile, $imageName, $width, $height, $type, $required_width, $required_height, $fieldSize, $minSize) {

    if ($minSize === true) {

        //The required widths and heights are the MINIMUM permitted sizes
        $IAR = $width / $height; //IMAGE ASPECT RATIO

        if ($required_width / $IAR < $required_height) {

            $newHeight = $required_height;
            $newWidth = $newHeight * $IAR;
        } else {

            $newWidth = $required_width;
            $newHeight = $newWidth / $IAR;
        }
    } else {

        //The required widths and heights are the MAXIMUM permitted sizes

        if ($width > $required_width || $height > $required_height) {

            if ($width > $height) {

                $width_Percentage = (int) $required_width / $width;
                $newWidth = round($width * $width_Percentage);
                $newHeight = round($height * $width_Percentage);

                if ($newHeight > $required_height) {

                    $height_Percentage = (int) $required_height / $newHeight;
                    $newWidth = round($newWidth * $height_Percentage);
                    $newHeight = round($newHeight * $height_Percentage);
                }
            } else {

                $height_Percentage = (int) $required_height / $height;
                $newWidth = round($width * $height_Percentage);
                $newHeight = round($height * $height_Percentage);

                if ($newWidth > $required_width) {

                    $width_Percentage = (int) $required_width / $newWidth;
                    $newWidth = round($newWidth * $width_Percentage);
                    $newHeight = round($newHeight * $width_Percentage);
                }
            }
        } else {

            $newWidth = $width;
            $newHeight = $height;
        }
    }

    $image_tmp = imagecreatetruecolor($newWidth, $newHeight);

    if ($type == IMAGETYPE_JPEG) {
        $image_src = imagecreatefromjpeg($uploadedFile);
    } else if ($type == IMAGETYPE_PNG) {
        $image_src = imagecreatefrompng($uploadedFile);
        // integer representation of the color black (rgb: 0,0,0)
        $background = imagecolorallocate($image_tmp, 0, 0, 0);
        // removing the black from the placeholder
        imagecolortransparent($image_tmp, $background);
        // turning off alpha blending (to ensure alpha channel information 
        // is preserved, rather than removed (blending with the rest of the 
        // image in the form of black))
        imagealphablending($image_tmp, false);
        // turning on alpha channel information saving (to ensure the full range 
        // of transparency is preserved)
        imagesavealpha($image_tmp, true);
    } else {
        $image_src = imagecreatefromgif($uploadedFile);
        // integer representation of the color black (rgb: 0,0,0)
        $background = imagecolorallocate($image_tmp, 0, 0, 0);
        // removing the black from the placeholder
        imagecolortransparent($image_tmp, $background);
    }

    imagecopyresampled($image_tmp, $image_src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $uploadedImageLocation = $imagesDirectory . $imageName;
    $imageNameExplode = explode(".", $imageName);
    $newImageName = $imageNameExplode[0] . date("YmdHisu") . "_" . $fieldSize . "." . $imageNameExplode[1];
    $newImageName = str_replace(" ", "", str_replace("-", "", $newImageName));

    if ($type == IMAGETYPE_PNG) {
        imagepng($image_tmp, $imagesDirectory . $newImageName, 9);
    } else if ($type == IMAGETYPE_JPEG) {
        imagejpeg($image_tmp, $imagesDirectory . $newImageName, 100);
    } else if ($type == IMAGETYPE_GIF) {
        imagegif($image_tmp, $imagesDirectory . $newImageName);
    }
    imagedestroy($image_tmp);
    imagedestroy($image_src);

    $arrReturn = array();
    $arrReturn[0] = $newImageName;
    $arrReturn[1] = $newWidth;
    $arrReturn[2] = $newHeight;

    return $arrReturn;
}

function getExtension($str) {

    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }
    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

function extractLocationFromImageExif($uploadedFile) {

    $arrImage = array();
    $arrResult = array(0, 0);
    $lat = 0;
    $lng = 0;

    try {

        if (extension_loaded('exif')) {
            $arrImage = exif_read_data($uploadedFile, 0, true);
        }

        if (isset($arrImage['GPS'])) {

            $GPS = $arrImage['GPS'];

            if (isset($GPS['GPSLatitudeRef']) && isset($GPS['GPSLatitude'])) {

                //LATITUDE
                $LatitudeRef = $GPS['GPSLatitudeRef'];
                $Latitude = $GPS['GPSLatitude'];

                $arrLatDegrees = explode("/", $Latitude[0]);
                $arrLatMinutes = explode("/", $Latitude[1]);
                $arrLatSeconds = explode("/", $Latitude[2]);

                $latDegrees = floatval($arrLatDegrees[0] / $arrLatDegrees[1]);
                $latMinutes = floatval($arrLatMinutes[0] / $arrLatMinutes[1]);
                $latSeconds = floatval($arrLatSeconds[0] / $arrLatSeconds[1]);

                //LONGITUDE
                $LongitudeRef = $GPS['GPSLongitudeRef'];
                $Longitude = $GPS['GPSLongitude'];

                $arrLngDegrees = explode("/", $Longitude[0]);
                $arrLngMinutes = explode("/", $Longitude[1]);
                $arrLngSeconds = explode("/", $Longitude[2]);

                $lngDegrees = floatval($arrLngDegrees[0] / $arrLngDegrees[1]);
                $lngMinutes = floatval($arrLngMinutes[0] / $arrLngMinutes[1]);
                $lngSeconds = floatval($arrLngSeconds[0] / $arrLngSeconds[1]);

                $lat = DMStoDEC($latDegrees, $latMinutes, $latSeconds);
                if (strcasecmp($LatitudeRef, "S") === 0)
                    $lat = -1 * $lat;

                $lng = DMStoDEC($lngDegrees, $lngMinutes, $lngSeconds);
                if (strcasecmp($LongitudeRef, "W") === 0)
                    $lng = -1 * $lng;
            }
        }
    } catch (Exception $e) {
        
    }

    $arrResult = array($lat, $lng);
    return $arrResult;
}

function DMStoDEC($deg, $min, $sec) {

// Converts DMS ( Degrees / minutes / seconds ) 
// to decimal format longitude / latitude

    return $deg + ((($min * 60) + ($sec)) / 3600);
}

?>