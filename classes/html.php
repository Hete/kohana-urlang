<?php

defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML {

    /**
     * Overridden anchor for adding the $lang parameter.
     */
    public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = TRUE, $lang = NULL) {
        return parent::anchor(Urlang::instance()->translate($uri, $lang), $title, $attributes, $protocol, $index);
    }

}

?>
