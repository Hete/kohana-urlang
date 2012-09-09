<?php

class Request extends Kohana_Request {

    public static function process_uri($uri, $routes = NULL) {

        if (Kohana::$config->load("urlang.prepend"))
        $uri = Urlang::unprepend($uri);

        

        return parent::process_uri(Urlang::translation_to_uri($uri), $routes);
    }

}

// End Request
?>