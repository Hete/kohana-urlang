<?php

defined('SYSPATH') or die('No direct script access.');


class Request extends Kohana_Request {

    public static function process_uri($uri, $routes = NULL) {

        if (Kohana::$config->load("urlang.prepend"))
            $uri = Urlang::instance()->unprepend($uri);

        return parent::process_uri(Urlang::instance()->translation_to_uri($uri), $routes);
    }

}

// End Request
?>