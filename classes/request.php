<?php

class Request extends Kohana_Request {


    public static function process_uri($uri, $routes = NULL) {


        return parent::process_uri(Urlang::translation_to_uri($uri), $routes);
    }

}

// End Request
?>