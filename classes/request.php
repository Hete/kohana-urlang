<?php

class Request extends Kohana_Request {

    public static function process_uri($uri, $routes = NULL) {


        if (Kohana::$config->load("urlang.prepend")) {

            // Remove the prepended language in url if exists
            $langs = (array) Kohana::$config->load('urlang.langs');

            $uri = preg_replace('~^(?:' . implode('|', $langs) . ')(?=/|$)~i', "", $uri);

            if ($uri[0] === "/") {
                $uri = substr($uri, 1);
            }
        }

        return parent::process_uri(Urlang::translation_to_uri($uri), $routes);
    }

}

// End Request
?>