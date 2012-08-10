<?php

defined('SYSPATH') or die('No direct script access.');

class Urlang {

    private static $instance;
    
    private $_config;

    public static function instance() {
        return Urlang::$instance ? Urlang::$instance : Urlang::$instance = new Urlang();
    }

    private function __construct() {
    }
    
    /**
     * Returns a value based on a key in the associative array. 
     * @param type $lang
     */
    private function get_value($lang = null) {
        
        $translation_array = Kohana::$config->load($lang ? $lang : i18n::lang());
        
        
        
    }
    
    /**
     * Scans all lang, revert associative array and tries to match a key.
     */
     private function get_key() {
         
         
     }

    public function uri_to_translation($uri) {

        $parts = explode("/", $uri);


        foreach ($parts as &$part) {
            $part = __($part);
        }

        return implode("/", $parts);
    }

    public function translation_to_uri($translation) {

        $parts = explode('/', $translation);
        $source = i18n::$lang;

        // temporarily change target language
        i18n::$lang = 'url';

        foreach ($parts as &$part) {
            $part = __($part);
        }

        // die(print_r($parts));
        // restore target language
        i18n::$lang = $source;
        return implode('/', $parts);
    }

}

?>
