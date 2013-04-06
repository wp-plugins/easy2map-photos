<link href="<?php echo easy2mapimg_get_plugin_url('/css/bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
<link href="<?php echo easy2mapimg_get_plugin_url('/css/bootstrap-wysihtml5.css'); ?>" rel="stylesheet" media="screen">
<script src="<?php echo easy2mapimg_get_plugin_url('/scripts/bootstrap.min.js'); ?>"></script>

<style type="text/css">

    input[type=text], input[type=password] {
        height: 28px !important;
    }

    td .instructions{
        font-size:14px !important;
        text-align:left;
        font-weight:bold;
    }    

</style>

<div style="width:60%;margin-top:10px;margin-left:auto;margin-right:auto;text-align:right">
    <a id="btnBack" href="?page=easy2mapimg&action=viewmaps">Back to Map Manager</a>
</div>

<form id="save-easy2mapphoto_key" name="save-easy2mapphoto_key" action="" method="post">

    <table style="background-color:#EBEBEB;width:60%;margin-top:10px;margin-left:auto;margin-right:auto;" cellspacing="3" cellpadding="3" class="table table-bordered">
        <tr>
            <td colspan="2" style="font-size:16px;text-align:left;font-weight:bold;">Unlock all the plugin features by upgrading to the Pro Version</td>
        </tr>
        <tr><td colspan="2" style="font-size:13px;text-align:left;font-weight:bold;color:#70aa00;">Pro Version Features:</td></tr>
        <tr><td style="width:1%;padding:8px;text-align:center;"><img src="<?php echo easy2mapimg_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Edit photo map layout, with a choice of 8 different template options</td></tr>
        <tr><td style="width:1%;padding:8px;text-align:center;"><img src="<?php echo easy2mapimg_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Change map & photo size, and map settings to your exact requirements</td></tr>
        <tr><td style="width:1%;padding:8px;text-align:center;"><img src="<?php echo easy2mapimg_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Administer individual style elements to your exact requirements</td></tr>
        <tr><td style="width:1%;padding:8px;text-align:center;"><img src="<?php echo easy2mapimg_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Upload custom marker icons</td></tr>
        <tr><td style="width:1%;padding:8px;text-align:center;"><img src="<?php echo easy2mapimg_get_plugin_url('/images/tick_small.png'); ?>"/></td>
            <td style="font-weight:bold;">Easy2Map icon removed from photos</td></tr>


        <?php
        if (isset($_POST['easy2mapphoto_key']) && isset($_POST['action']) && $_POST['action'] == "update_easy2mapphotokey") {
            if (wp_verify_nonce($_POST['easy2mapphoto_key'], 'update-options')) {

                if (self::easy2MapPhotoCodeValidator($_POST['code'])) {
                    update_option('phe_171323_transient_17666766', $_POST['code']);
                    ?>

                    <tr><td colspan="2" style="text-align:center;vertical-align:middle;height:40px;font-size:1.3em;color:#70aa00;font-weight:bold;">
                            Your version has successfully been upgraded, thank you!
                        </td></tr>

                    <?php
                } else {
                    ?>

                    <tr><td colspan="2" style="text-align:center;vertical-align:middle;height:40px;font-size:1.3em;color:red;font-weight:bold;">
                            Your version upgrade was not successful!
                        </td></tr>

                    <?php
                }
            } else {
                ?><tr><td colspan="2" style="text-align:center;vertical-align:middle;height:40px;font-size:1.3em;color:red;font-weight:bold;">
                        Update failed!
                    </td></tr><?php
    }
}
        ?>

        <?php if (self::easy2MapPhotoCodeValidator('') === false) { ?>

            <tr><td colspan="2" style="text-align:center;vertical-align:middle;height:100px;">
                    <a target="_blank" href="http://easy2map.com/payment/paypal/easy2MapPhotosPro.php" style="font-size:1.3em;color:#70aa00;font-weight:bold;">Upgrade now to Pro Version for $8.99</a>
                    <img style="margin-left:30px;margin-right:auto;" src="<?php echo easy2mapimg_get_plugin_url('/images/paypal-verified.png'); ?>">

                </td></tr>

        <?php }
        ?>

    </table>

    <?php if (self::easy2MapPhotoCodeValidator('') === false) { ?>

        <h5>Please Enter Your Activation Code Here:</h5>
        <input type="text" name="code" style="margin-left:auto;margin-right:auto;width:80%" value="" />
        <br>
        <?php wp_nonce_field('update-options', 'easy2mapphoto_key'); ?>
        <input type="hidden" name="action" value="update_easy2mapphotokey">
        <input type="submit" class="btn btn-primary" style="width:150px" value="Activate">

    <?php } ?>
        
</form>
