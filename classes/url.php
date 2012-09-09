<?php

defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL {

  

    /**
     * Traduire les urls depuis la langue contr
     * @param type $uri
     * @param type $protocol
     * @param type $index
     * @return type 
     */
    public static function site($uri = "", $protocol = "", $index = true) {
        
        if(Kohana::$config->load('urlang.prepend'))
            $uri = I18n::lang().'/'.ltrim($uri, '/');

        return parent::site(Urlang::uri_to_translation($uri), $protocol, $index);
    }

}