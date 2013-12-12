<?php

defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML {
   
    /**
     * Overriden method to include a $lang attribute.
     * 
     * @see Kohana_HTML::anchor
     * 
     * @param string $lang lang into which the uri will be translated. 
     */
    public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = TRUE, $lang = NULL) {
        return parent::anchor(Urlang::instance()->translate($uri, $lang), $title, $attributes, $protocol, $index);
    }

}
