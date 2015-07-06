<?php
include 'ImageFunctions.php';

$imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/pin_content/" . $_GET["map_id"] . "/";

var_dump($_REQUEST);

if (is_uploaded_file($_FILES["pinimage"]['tmp_name'])) {

    if (!file_exists($imagesDirectory)) {
        mkdir($imagesDirectory);
    }

    $imageName = preg_replace("/[^A-Za-z0-9 ]/", '', $_FILES["pinimage"]['name']);
    
    $uploadedFile = $_FILES["pinimage"]['tmp_name'];
    $extension = strtolower(getExtension($imageName));

    list($width, $height, $type, $attr) = getimagesize($uploadedFile);
    $arrLatLng = extractLocationFromImageExif($uploadedFile);
    $lat = 0;
    $lng = 0;
    
    if (is_array($arrLatLng) && count($arrLatLng) == 2
            && is_numeric($arrLatLng[0])
            && is_numeric($arrLatLng[1]))
    {
        $lat = $arrLatLng[0];
        $lng = $arrLatLng[1];
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
