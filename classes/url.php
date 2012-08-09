<?php

defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL {

    public static function uri_to_translation($uri) {
        
        $parts = explode("/", $uri);       


        foreach ($parts as &$part) {
            $part = __($part);
        }

        return implode("/", $parts);
    }

    /**
     * Traduire les urls depuis la langue contr
     * @param type $uri
     * @param type $protocol
     * @param type $index
     * @return type 
     */
    public static function site($uri = "", $protocol = "", $index = true) {


        return parent::site(URL::uri_to_translation($uri), $protocol, $index);
    }

}