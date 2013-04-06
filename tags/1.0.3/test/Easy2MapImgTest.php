<?php

/**
  class Easy2MapImgTest
  Basic library for run-time tests. */
if (!class_exists('Easy2MapImgTest')):

    class Easy2MapImgTest {

        public static $errors = array(); // Any errors thrown. 

        public static function print_notices() {
            if (!empty(self::$errors)) {
                $error_items = '';
                foreach (self::$errors as $e) {
                    $error_items .= "<li>$e</li>";
                }
                print '<div id="easy2map-error" class="error"><p><strong>'
                        . __('The &quot;Easy2Map Photos&quot; plugin encountered errors! It cannot load!') . '</strong> ' . "<ul style='margin-left:30px;'>$error_items</ul>"
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
        
        /** * PHP might have been compiled without some module that you require. 
         * Pass this function an array of $required_extensions and it will 
         * register a message about any missing modules. 
         * * @param array $required_extensions an array of PHP modules you want to ensure are installed on your 
         * server, e.g. array('pcre', 'mysqli', 'mcrypt'); 
         * * @return none An error message is registered in self::$errors if the test fails. */ 
        public static function php_extensions($required_extensions){ 
            $loaded_extensions = get_loaded_extensions(); 
            foreach ( $required_extensions as $req ) { 
                if ( !in_array($req, $loaded_extensions ) ) {
                    $exit_msg = "The plugin requires the '<b>$req</b>' PHP extension. Please talk to your system administrator about reconfiguring PHP.";
                    self::$errors[] = $exit_msg;
                }
            }
        }

    }

    

endif; /*EOF*/