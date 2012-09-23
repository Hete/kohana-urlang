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
    public static function site($uri = "", $protocol = "", $index = true, $lang = NULL) {
        if (Kohana::$config->load('urlang.prepend'))
            $uri = Urlang::instance()->prepend($uri, $lang);

        return parent::site(Urlang::instance()->uri_to_translation($uri, $lang), $protocol, $index);
    }

}