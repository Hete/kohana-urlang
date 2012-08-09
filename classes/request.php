<?php

class Request extends Kohana_Request {

    public static function translation_to_uri($uri) {
        $parts = explode('/', $uri);
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

    public static function process_uri($uri, $routes = NULL) {


        return parent::process_uri(Request::translation_to_uri($uri), $routes);
    }

}

// End Request
?>