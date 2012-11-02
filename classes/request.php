<?php

defined('SYSPATH') or die('No direct script access.');


class Request extends Kohana_Request {

    public static function process_uri($uri, $routes = NULL) {
        return parent::process_uri(Urlang::instance()->untranslate($uri), $routes);
    }

}

// End Request
?>