<?php
include 'ImageFunctions.php';

$imagesDirectory = WP_CONTENT_DIR . "/uploads/easy2map/images/map_pins/" . $_GET["map_id"] . "/";
echo $imagesDirectory;

if (is_uploaded_file($_FILES["pinicon"]['tmp_name'])) {

    if (!file_exists($imagesDirectory)) {
        mkdir($imagesDirectory);
    }

    $imageName = $_FILES["pinicon"]['name'];
    $uploadedFile = $_FILES["pinicon"]['tmp_name'];
    $extension = strtolower(getExtension($imageName));

    list($width, $height, $type, $attr) = getimagesize($uploadedFile);
    $uploadedImageLocation = $imagesDirectory . $imageName;
    $imageNameExplode = explode(".", $imageName);
    $imagePlusLocation = "";
    
    if ($_FILES["pinicon"]['size'] < 5000000) {
        $arrSmallImage = resizeImage($imagesDirectory, $uploadedFile, $imageName, $width, $height, $type, '50', '50', "SMALL", false);
        $imagePlusLocation = WP_CONTENT_URL . "/uploads/easy2map/images/map_pins/" . $_GET["map_id"] . "/" . $arrSmallImage[0];
        
    }
}
?>

<script type="text/javascript">

    window.onload = function(){
        window.parent.jQuery('#divUploadPinIcon').fadeOut();
        window.parent.jQuery('#draggable').attr('src', '<?php echo $imagePlusLocation; ?>');
        window.parent.easy2map_imgmappin_functions.setMapPinImage(parent.window.document.getElementById('draggable'));      
    }

</script>
