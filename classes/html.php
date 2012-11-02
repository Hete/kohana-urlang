<?php

defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML {

    public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = TRUE, $lang = NULL) {

        if ($title === NULL) {
            // Use the URI as the title
            $title = $uri;
        }

        if ($uri === '') {
            // Only use the base URL
            $uri = Urlang::instance()->append(URL::base($protocol, $index), $lang);
        } else {
            if (strpos($uri, '://') !== FALSE) {
                if (HTML::$windowed_urls === TRUE AND empty($attributes['target'])) {
                    // Make the link open in a new window
                    $attributes['target'] = '_blank';
                }
            } elseif ($uri[0] !== '#') {
                // Make the URI absolute for non-id anchors

                $uri = URL::site($uri, $protocol, $index, $lang);
            }
        }

        // Add the sanitized link to the attributes
        $attributes['href'] = $uri;

        return '<a' . HTML::attributes($attributes) . '>' . $title . '</a>';
    }

}

?>
