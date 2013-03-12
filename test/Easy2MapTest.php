<?php

/**
  class Easy2MapTest
  Basic library for run-time tests. */
if (!class_exists('Easy2MapTest')):

    class Easy2MapTest {

        public static $errors = array(); // Any errors thrown. 

        public static function print_notices() {
            if (!empty(self::$errors)) {
                $error_items = '';
                foreach (self::$errors as $e) {
                    $error_items .= "<li>$e</li>";
                }
                print '<div id="easy2map-error" class="error"><p><strong>'
                        . __('The &quot;Easy2Map&quot; plugin encountered errors! It cannot load!') . '</strong> ' . "<ul style='margin-left:30px;'>$error_items</ul>"
                        . '</p>' . '</div>';
            }
        }
        
        public static function min_php_version($min_php_version) {
            $exit_msg = "The plugin requires PHP $min_php_version or newer. Contact your system administrator about updating your version of PHP";
            if (version_compare(phpversion(), $min_php_version, '<')) {
                self::$errors[] = $exit_msg; 
            }
        }
        
        public static function min_wordpress_version($min_wp_versione) {
            global $wp_version;
            $exit_msg = "The plugin requires $min_wp_versione or newer . <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update!</a>";
            if (version_compare($wp_version, $min_wp_versione,'<')) {
                self::$errors[] = $exit_msg; 
            }
        }

    }

    

endif; /*EOF*/