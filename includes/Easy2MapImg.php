<?php

class Easy2MapImg {

    const plugin_name = 'Easy2MapImg';
    const min_php_version = '5.0';
    const min_wp_version = '3.0';

    // Used to uniquely identify this plugin's menu page in the WP manager
    const admin_menu_slug = 'easy2mapimg';

    /** Adds the necessary JavaScript and/or CSS to the pages to enable the Ajax search. */
    public static function head() {

        $src_Easy2MapImgAPI = "http://maps.google.com/maps/api/js?sensor=true";
        $src_Easy2MapImg = plugins_url('scripts/easy2mapimg.js', dirname(__FILE__));
        $src_Xml2json = plugins_url('scripts/jquery.xml2json.js', dirname(__FILE__));
        $src_Carousel = plugins_url('scripts/carousel.js', dirname(__FILE__));

        wp_register_script('easy2mapimg_js_api', $src_Easy2MapImgAPI);
        wp_register_script('easy2mapimg_js_easy2map', $src_Easy2MapImg);
        wp_register_script('easy2mapimg_js_Xml2json', $src_Xml2json);
        wp_register_script('easy2mapimg_js_Carousel', $src_Carousel);

        wp_enqueue_script('easy2mapimg_js_api');
        wp_enqueue_script('easy2mapimg_js_easy2map');
        wp_enqueue_script('easy2mapimg_js_Xml2json');
        wp_enqueue_script('easy2mapimg_js_Carousel');
    }

    /** The main function for this plugin, similar to __construct() */
    public static function initialize() {

        Easy2MapImgTest::min_php_version(self::min_php_version);
        Easy2MapImgTest::min_wordpress_version(self::min_wp_version);
        Easy2MapImgTest::php_extensions(array('gd'));
        Easy2MapImgTest::print_notices();

        wp_enqueue_script('jquery'); // make sure jQuery is loaded!
        if (is_admin()) {
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
        }
    }

    /**     * Register the shortcodes used */
    public static function register_shortcodes() {
        add_shortcode('easy2mapimg', 'Easy2MapImg::retrieve_map');
    }

    public static function create_admin_tables() {

        global $wpdb;
        $error = "<div id='error' class='error'><p>%s</p></div>";
        $map_table = $wpdb->prefix . "easy2mapimg_maps";
        $map_points_table = $wpdb->prefix . "easy2mapimg_map_points";
        $map_point_templates_table = $wpdb->prefix . "easy2mapimg_pin_templates";
        $map_templates_table = $wpdb->prefix . "easy2mapimg_templates";

        $result = $wpdb->get_var("show tables like '$map_table'");

        if (strtolower($result) != strtolower($map_table)) {

            $SQL = "CREATE TABLE `$map_table` (
          `ID` bigint(20) NOT NULL AUTO_INCREMENT,
          `TemplateID` bigint(20) DEFAULT NULL,
          `MapName` varchar(256) DEFAULT NULL,
          `MapTitle` varchar(512) DEFAULT NULL,
          `DefaultPinImage` varchar(256) DEFAULT NULL,
          `Settings` text,
          `LastInvoked` datetime DEFAULT NULL,
          `PolyLines` text,
          `CSSValues` text,
          `CSSValuesPhoto` text,
          `CSSValuesMap` text,
          `MapHTML` text,
          `IsActive` smallint(6) DEFAULT NULL,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `ID_UNIQUE` (`ID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";

            if (!$wpdb->query($SQL)) {
                echo sprintf($error, __("Could not create easy2map photo maps table.", 'easy2map'));
                return;
            }
        }

        $result = $wpdb->get_var("show tables like '$map_points_table'");

        if (strtolower($result) != strtolower($map_points_table)) {

            $SQL = "CREATE TABLE `$map_points_table` (
            `ID` bigint(20) NOT NULL AUTO_INCREMENT,
            `MapID` bigint(20) DEFAULT NULL,
            `CreatedByUserID` bigint(20) DEFAULT NULL,
            `LatLong` varchar(128) DEFAULT NULL,
            `Title` varchar(512) DEFAULT NULL,
            `PinImageURL` varchar(512) DEFAULT NULL,
            `Settings` varchar(512) DEFAULT NULL,
            `DetailsHTML` text,
            `DetailsText` text,
            `PinImageSmall` varchar(512) DEFAULT NULL,
            `PinImageMedium` varchar(512) DEFAULT NULL,
            `PinImageLarge` varchar(512) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`),
            KEY `wp_easy2mapimg_map_points_MapID` (`MapID`),
            CONSTRAINT `easy2mapimg_map_points_MapID` FOREIGN KEY (`MapID`) REFERENCES `$map_table` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
          ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";

            if (!$wpdb->query($SQL)) {
                echo sprintf($error, __("Could not create easy2map photo pins table.", 'easy2mapimg'));
                return;
            }
        }

        $result = $wpdb->get_var("show tables like '$map_point_templates_table'");

        if (strtolower($result) != strtolower($map_point_templates_table)) {

            $SQL = "CREATE TABLE `$map_point_templates_table` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `TemplateName` varchar(128) DEFAULT NULL,
            `TemplateHTML` text,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`)
          ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";

            if (!$wpdb->query($SQL)) {
                echo sprintf($error, __("Could not create easy2map pin templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert1 = "INSERT INTO `$map_point_templates_table`
            (`TemplateName`,
            `TemplateHTML`)
            VALUES
            ('Thumbnail Image on Left', 
            '<table style=\"margin-top:8px;overflow:hidden;vertical-align:middle;width:325px;border-radius:0px;margin-left:5px;margin-right:20px;\"><tr><td style=\"vertical-align:top;padding:6px;border-style:solid;border-width:1px;vertical-align:top;border-color:#FFFFFF;\"><img style=\"border:0px solid #000000;border-radius: 0px;\" src=\"http://easy2map.com/images/css_templates/thumbnail20120424025713000000_CSS.png\" border=\"0\"></td><td style=\"width:100%;vertical-align:top;\"><p align=\"left\" style=\"border-color:#E53840;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;background-color:#E53840;border-radius:0px;margin:3px;\">Section 1</p><p align=\"left\" style=\"margin:3px;background-color:#009AD7;border-color:#009AD7;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:top;border-radius:0px;\">Section 2</p><p align=\"left\" style=\"margin:3px;background-color:#DADADA;border-color:#DADADA;border-style:solid;border-width:1px;color:#000000;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;border-radius:0px;\">Section 3</p><p align=\"left\" style=\"margin:3px;background-color:#FFFFFF;border-color:#EBEBEB;border-style:solid;border-width:1px;color:#000000;font-size:12px;font-weight:normal;padding:4px;text-align:left;border-radius:0px;\">Section 4</p></td></tr></table>')";

            if (!$wpdb->query($SQLInsert1)) {
                echo sprintf($error, __("Could not insert data into easy2map pin templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert2 = "INSERT INTO `$map_point_templates_table`
            (`TemplateName`,
            `TemplateHTML`)
            VALUES
            ('Thumbnail Image on Right',
            '<table style=\"margin-top:8px;overflow:hidden;vertical-align:middle;width:325px;border-radius:0px;margin-left:5px;margin-right:20px;\"><tr><td style=\"width:100%;vertical-align:top;\"><p align=\"left\" style=\"border-color:#E53840;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;background-color:#E53840;border-radius:0px;margin:3px;\">Section 1</p><p align=\"left\" style=\"margin:3px;background-color:#009AD7;border-color:#009AD7;border-style:solid;border-width:1px;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:top;border-radius:0px;\">Section 2</p><p align=\"left\" style=\"margin:3px;background-color:#DADADA;border-color:#DADADA;border-style:solid;border-width:1px;color:#000000;font-family:Arial, Helvetica, sans-serif;font-size:13px;font-weight:bold;padding:4px;text-align:left;text-decoration:none;vertical-align:middle;border-radius:0px;\">Section 3</p><p align=\"left\" style=\"margin:3px;background-color:#FFFFFF;border-color:#EBEBEB;border-style:solid;border-width:1px;color:#000000;font-size:12px;font-weight:normal;padding:4px;text-align:left;border-radius:0px;\">Section 4</p></td><td style=\"vertical-align:top;padding:6px;border-style:solid;border-width:1px;vertical-align:top;border-color:#FFFFFF;\"><img style=\"border:0px solid #000000;border-radius: 0px;\" src=\"http://easy2map.com/images/css_templates/thumbnail20120424025713000000_CSS.png\" border=\"0\"></td></tr></table>')";

            if (!$wpdb->query($SQLInsert2)) {
                echo sprintf($error, __("Could not insert data into easy2map pin templates table.", 'easy2mapimg'));
                return;
            }
        }

        $result = $wpdb->get_var("show tables like '$map_templates_table'");

        if (strtolower($result) != strtolower($map_templates_table)) {

            $SQL = "CREATE TABLE `$map_templates_table` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `TemplateName` varchar(256) DEFAULT NULL,
            `ExampleImage` varchar(512) DEFAULT NULL,
            `DisplayOrder` smallint(6) DEFAULT NULL,
            `CSSValues` text,
            `CSSValuesPhoto` text,
            `CSSValuesMap` text,
            `TemplateHTML` text,
            `StyleParentOnly` smallint(6) DEFAULT NULL,
            `Active` smallint(6) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`)
          ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1";

            if (!$wpdb->query($SQL)) {
                echo sprintf($error, __("Could not create easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert3 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map On Bottom', '', 1,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-bottom-width:0px;top:0px;left:0px;min-width:10px;margin-bottom:0px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div align=\"center\" id=\"easy2mapslidertext\"></div><a class=\"easy2maplogo\" id=\"easy2mapphotologo\" target=\"_blank\" href=\"http://easy2map.com\"><img src=\"[siteurl]images/e2mlogosmall.png\"></a><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr><tr><td id=\"tdMap\" style=\"border-width:0px;vertical-align:top;\"><div id=\"divMap\" style=\"border-top-width:0px;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;background-color:transparent;position:relative;\"></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert3)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert4 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map On Top', '', 2,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td id=\"tdMap\" style=\"border-width:0px;vertical-align:top;\"><div id=\"divMap\" style=\"background-color:transparent;border-style:solid;border-width:1px;border-bottom-width:0px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"></div></td></tr><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-top-width:0px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:0px;position:relative;\"><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert4)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert5 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map On Left', '', 3,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings width=\"150px\" height=\"390px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td id=\"tdMap\" style=\"border-width:0px;vertical-align:top;\"><div id=\"divMap\" style=\"background-color:transparent;border-style:solid;border-width:1px;border-right-width:0px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:0px;margin-top:5px;position:relative;\"></div></td><td style=\"width:100%;\"><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-left-width:0px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert5)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert6 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map On Right', '', 4,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings width=\"150px\" height=\"390px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td style=\"width:100%;\"><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-right-width:0px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td><td id=\"tdMap\" style=\"border-width:0px;vertical-align:top;\"><div id=\"divMap\" style=\"background-color:transparent;border-style:solid;border-width:1px;border-left-width:0px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-right:5px;margin-left:0px;margin-top:5px;position:relative;\"></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert6)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert7 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Top Left - Style 1', '', 5,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"none\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" margin-left=\"0px\" margin-top=\"0px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;top:0px;left:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert7)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert8 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Top Right - Style 1', '', 6,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"none\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" margin-right=\"0px\" margin-top=\"0px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;top:0px;right:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert8)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert9 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Bottom Left - Style 1', '', 7,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"none\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" margin-left=\"0px\" margin-bottom=\"0px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;bottom:0px;left:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert9)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }

            $SQLInsert10 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Bottom Right - Style 1', '', 8,
            '<settings background-color=\"#FFFFFF\" border-style=\"solid\" border-width=\"1px\" border-color=\"#EBEBEB\" margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#faf9f9\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"none\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#faf9f9\" margin-right=\"0px\" margin-bottom=\"0px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;bottom:0px;right:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert10)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }
            
            $SQLInsert11 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Top Left - Style 2', '', 9,
            '<settings margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#272727\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"-4px -4px 2px #888888\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#272727\" margin-left=\"-15px\" margin-top=\"-15px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:transparent;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;top:0px;left:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert11)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }
            
            
            
            $SQLInsert12 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Top Right - Style 2', '', 10,
            '<settings margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#272727\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"4px -4px 2px #888888\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#272727\" margin-right=\"-15px\" margin-top=\"-15px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:transparent;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;top:0px;right:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert12)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }
            
            $SQLInsert13 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Bottom Left - Style 2', '', 11,
            '<settings margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#272727\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"-4px 4px 2px #888888\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#272727\" margin-left=\"-15px\" margin-bottom=\"-15px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:transparent;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;bottom:0px;left:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert13)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }
            
            $SQLInsert14 = "INSERT INTO `$map_templates_table`
            (`TemplateName`,
            `ExampleImage`,
            `DisplayOrder`,
            `CSSValues`,
            `CSSValuesPhoto`,
            `CSSValuesMap`,
            `TemplateHTML`,
            `Active`
            )
            VALUES
            ('Map In Photo On Bottom Right - Style 2', '', 12,
            '<settings margin-left=\"auto\" margin-right=\"auto\" />',
            '<settings width=\"520px\" height=\"390px\" border-style=\"solid\" border-radius=\"0px\" border-width=\"1px\" border-color=\"#272727\" background-color=\"#faf9f9\" />',
            '<settings box-shadow=\"4px 4px 2px #888888\" width=\"120px\" height=\"120px\" border-radius=\"0px\" border-style=\"solid\" border-width=\"1px\" border-color=\"#272727\" margin-right=\"-15px\" margin-bottom=\"-15px\" />',
            '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-color:transparent;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;position:relative;\"><div id=\"divMap\" style=\"position:absolute;background-color:transparent;border-style:solid;border-width:1px;border-color:transparent;bottom:0px;right:0px;min-width:10px;\"></div><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>', 1)";

            if (!$wpdb->query($SQLInsert14)) {
                echo sprintf($error, __("Could not add data to easy2map templates table.", 'easy2mapimg'));
                return;
            }
            
             $SQLInsert15 = "UPDATE `$map_templates_table`
             SET `TemplateHTML` = '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td id=\"tdMap\" style=\"border-width:0px;vertical-align:top;\"><div id=\"divMap\" style=\"background-color:transparent;border-style:solid;border-width:1px;border-right-width:0px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:0px;margin-top:5px;position:relative;\"></div></td><td style=\"width:100%;\"><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-left-width:0px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td></tr></table></div>'
             WHERE `TemplateName` = 'Map On Left'";
             
             $wpdb->query($SQLInsert15);
             
             $SQLInsert16 = "UPDATE `$map_templates_table`
             SET `TemplateHTML` = '<div style=\"margin:auto;\"><table cellpadding=\"1\" cellspacing=\"1\" id=\"divMapParent\"><tr><td style=\"width:100%;\"><div align=\"center\" id=\"easy2mapmainimage\" style=\"text-align:center;border-style:solid;border-width:1px;border-right-width:0px;border-color:#FFFFFF;background-color:#FFFFFF;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-left:5px;margin-right:5px;margin-top:5px;position:relative;\"><div align=\"center\" id=\"easy2mapslidertext\"></div><div id=\"easy2mapslider\"><a class=\"easy2mapbutton easy2mapprev\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/prev.png\"></a><a class=\"easy2mapbutton easy2mapnext\" href=\"#\" rel=\"nofollow\"><img src=\"[siteurl]images/next.png\"></a><div class=\"easy2mapholder_cont\"><ul id=\"Easy2MapSliderParent\" class=\"easy2mapholder\"></ul></div><div class=\"easy2mapclear\"></div></div></div></td><td id=\"tdMap\" style=\"border-width:0px;vertical-align:top;\"><div id=\"divMap\" style=\"background-color:transparent;border-style:solid;border-width:1px;border-left-width:0px;border-color:transparent;top:0px;left:0px;min-width:10px;margin-bottom:5px;margin-right:5px;margin-left:0px;margin-top:5px;position:relative;\"></div></td></tr></table></div>'
             WHERE `TemplateName` = 'Map On Right'";
             
             $wpdb->query($SQLInsert16);

            
            
        }
    }

    /**     * Create custom post-type menu */
    public static function create_admin_menu() {
        add_menu_page('Easy2Map Photo Maps', // page title
                'Easy2MapPhotos', // menu title
                'manage_options', // capability 
                self::admin_menu_slug, // menu slug 
                'Easy2MapImg::get_admin_page', // callback 
                plugins_url('images/e2m_favicon2020.png', dirname(__FILE__)) //default icon
        );

        //add_submenu_page(self::admin_menu_slug, 'Enter Easy2MapPhotos Activation Code', 'Activation', 'manage_options', 'wp-easy2mapphotos-activation', 'Easy2MapImg::easy2mapimg_admin_validation');

        /* if (current_user_can('edit_posts') || current_user_can('edit_pages')){

          if(get_user_option('rich_editing') == 'true')
          {
          add_filter("mce_external_plugins", "Easy2MapImg::add_easy2mapimg_tinymce_plugin");
          add_filter('mce_buttons', 'Easy2MapImg::register_easy2mapimg_button');
          }
          } */
    }

    /* public static function add_easy2mapimg_tinymce_plugin($plugin_array) {
      $plugin_array['easy2map'] = plugins_url('easy2map-tinymce.php', dirname(__FILE__));
      return $plugin_array;
      }

      function register_easy2mapimg_button($buttons) {
      array_push($buttons, "|", "easy2map");
      return $buttons;
      } */

    /** Prints the administration page for this plugin. */
    public static function get_admin_page() {

        if (isset($_GET["action"]) && strcasecmp($_GET["action"], "addeditpins") == 0 && isset($_GET["map_id"])) {
            include('AddEditMapPins.php');
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "edit") == 0 && isset($_GET["map_id"])) {
            //include('AddEditMaps.php');
            //include('MapAdminister.php');
            include('MapAdmin.php');
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "mappiniconsave") == 0 && isset($_GET["map_id"])) {
            include('MapPinIconSave.php');
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "copymapsettings") == 0 && isset($_GET["map_id"])) {
            include('CopyMapSettings.php');    
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "mappinimageupload") == 0 && isset($_GET["map_id"])) {
            include('MapPinImageUpload.php');
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "mappreview") == 0 && isset($_GET["map_id"])) {
            include('MapPreview.php');
        } else if (isset($_GET["action"]) && strcasecmp($_GET["action"], "activation") == 0) {
            include('Validation.php');
        } else {
            include('ImageMapManager.php');
        }
    }

    /** Validation fucntion */
    private static function easy2MapPhotoCodeValidator($code) {
        
        if (strlen($code) == "") $code = get_option('phe_171323_transient_17666766');
        
        //code validator
        $validation = true;
        $string = substr($code, 32, -32);
        $pie_1 = substr($code, 0, 32);
        $pie_2 = substr($code, -32, 32);
        //get integers + characters in string
        $regex_first = "/[0-9]+/";
        $regex_second = "/[A-z]+/";
        preg_match_all($regex_first, $string, $integers);
        preg_match_all($regex_second, $string, $characters);
        //divide integer by key number
        $integer1 = $integers[0][0] / 3137831;
        $integer2 = $integers[0][1] / 7713;
        //validate integers
        $regex_decimal = "/[.]/";
        if (preg_match($regex_decimal, $integers[0][0]) || $integer1 <= 2211 || $integer1 >= 5353 || preg_match($regex_decimal, $integers[0][1]) || $integer2 <= 1001 || $integer2 >= 10201) {
            $validation = false;
        }
        //validate characters
        $regex_characters1 = "/[^ACEGIKMOQSUWY]/";
        $regex_characters2 = "/[^bdfhjlnprtvxz]/";
        if (preg_match($regex_characters1, $characters[0][0]) || preg_match($regex_characters2, $characters[0][1])) {
            $validation = false;
        }
        //validate MD5
        $val_1 = $string[3] . $string[15] . $string[7] . $string[4] . $string[9] . $string[13] . $string . $string[2] . $string[1] * $string[3];
        $val_2 = $string[5] . $string[1] . $string . $string[17] . $string[8] . $string[7] . $string[11] . $string[6] . $string[4] * $string[2];
        if (md5($val_1) != $pie_1 || md5($val_2) != $pie_2) {
            $validation = false;
        }
        return $validation;
    }

    /** Validation page. */
    public static function easy2mapimg_admin_validation() {
        include('Validation.php');
    }

    /**
     * * _is_searchable_page * 
     * * Any page that's not in the WP admin area is considered searchable. 
     * * @return boolean Simple true/false as to whether the current page is searchable. */
    private static function _is_searchable_page() {
        if (is_admin()) {
            return false;
        } else {
            return true;
        }
    }

    public static function retrieve_map($raw_args, $content = null) {
        $defaults = array('id' => '',);
        $sanitized_args = shortcode_atts($defaults, $raw_args);
        if (empty($sanitized_args['id'])) {
            return '';
        }

        $mapHTML = easy2mapimg_retrieve_map_HTML($sanitized_args['id']);
        $mapHTML = str_ireplace("divMapParent", "easy2mapImgMapParent" . $sanitized_args['id'], $mapHTML);
        $mapHTML = str_ireplace("divMap", "easy2mapimg_canvas_" . $sanitized_args['id'], $mapHTML);

        $mapHTML = str_ireplace("easy2mapmainimage", "easy2mapmainimage" . $sanitized_args['id'], $mapHTML);
        $mapHTML = str_ireplace("easy2mapslider", "easy2mapslider" . $sanitized_args['id'], $mapHTML);

        $sliderBG = plugins_url('/images/bg_grey.png', dirname(__FILE__));
        $waitingBG = plugins_url('/images/busy.gif', dirname(__FILE__));

        return '<script>var $easy2mapphotowaitingImage = "' . $waitingBG . '";</script><style type="text/css">
            #easy2mapslider' . $sanitized_args['id'] . 'text {display:none;text-align:center;color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:1em;position:absolute;width:100%;top:15px;font-weight:bold;z-index:999;border-color:transparent;border-radius:0px;border-style:solid;border-width:1px;text-shadow:#333333 0.09em 0.09em 0.09em;background-color:transparent;white-space:nowrap;}
            #easy2mapslider' . $sanitized_args['id'] . ' {padding-top:6px;display:none;position: absolute; bottom:0px;left:0px;z-index:99999;width: 100%; vertical-align: middle; height:67px;background-image: url(' . $sliderBG . '); background-repeat: repeat; }
            #easy2mapslider' . $sanitized_args['id'] . ' .easy2mapholder_cont {width: 100%; margin: 0 auto; overflow: hidden;  height:60px;}
            #easy2mapslider' . $sanitized_args['id'] . ' .easy2mapholder { vertical-align:middle; margin-left: 43px; margin-right: 37px; background-image: url(' . $sliderBG . '); background-repeat: repeat; }
            #easy2mapslider' . $sanitized_args['id'] . ' .easy2mapslide {cursor:pointer; vertical-align:middle; margin-top:auto;margin-bottom:auto;position: relative; margin-right: 8px; float:left; min-width: 60px; height: 60px;}
            #easy2mapslider' . $sanitized_args['id'] . ' .easy2mapbutton {position: absolute;}
            #easy2mapslider' . $sanitized_args['id'] . ' .easy2mapprev {top: 25px; margin-left:5px;left:0px;}
            #easy2mapslider' . $sanitized_args['id'] . ' .easy2mapnext {top: 25px; margin-right:5px;right:0px;}
            .easy2mapclear {clear:both;}
            .easy2maplogo{position: absolute; bottom:2px;right:2px;z-index:99999;}
            #easy2mapimg_canvas_' . $sanitized_args['id'] . ' img {
                max-width: none !important;
                border-radius: 0px !important;
                background: transparent;
                box-shadow: 0 0 0 transparent !important;
            }      
            #easy2mapslider' . $sanitized_args['id'] . ' img {
                max-width: none !important;
                border-radius: 0px !important;
                border-width:0px;
                box-shadow: 0 0 0 transparent !important;
            }
            #easy2mapslider' . $sanitized_args['id'] . ' ul {
                list-style-type: none;
                padding: 0; margin: 0;
                border-width:0px;
            }
            #easy2mapslider' . $sanitized_args['id'] . ' ul li {
                list-style: none;
                padding: 0; margin: 0;
                border-width:0px;
            }
            #easy2mapimg_canvas_' . $sanitized_args['id'] . ' table, td {
              margin: -1.5em 0 !important;
              padding: 0px !important;
              border-width:0px;
            }
            #easy2mapImgMapParent' . $sanitized_args['id'] . '{
                width:30px;
            }
            </style><input type="hidden" id="easy2mapimg_ajax_url_' . $sanitized_args['id'] . '" value="' . admin_url("admin-ajax.php") . '">' . $mapHTML;
    }

}

?>