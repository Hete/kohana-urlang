<?php

defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {

    /**
     * Overriden method to untranslate and implement suggested lang to translate
     * subsequent urls.
     * 
     * @see Kohana_Request::process_uri       
     */
    public static function process_uri($uri, $routes = NULL) {

        // Untranslate the uri before its being processed
        $untranslated_uri = parent::process_uri(Urlang::instance()->untranslate($uri), $routes);

        // Updating lang upon this information
        I18n::lang(Urlang::instance()->suggested_lang($uri));

        // Stockage de la langue en cookie
        Cookie::set('lang', I18n::lang());

        return $untranslated_uri;
    }

}