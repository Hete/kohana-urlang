<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 */
class Urlang {

    /**
     *
     * @var Urlang 
     */
    private static $_instance;
    
    /**
     * Cached supported langs array.
     */
    private $_langs;       

    private function __construct() {
        $this->_langs = (array) Kohana::$config->load('urlang.langs');
    }

    /**
     * 
     * @return Urlang
     */
    public static function instance() {
        return Urlang::$_instance ? Urlang::$_instance : Urlang::$_instance = new Urlang();
    }

    /**
     * Prepends the lang in I18n::lang() or the $lang parameter if specified.
     * @param string $uri 
     * @param string $lang is the lang to prepend.
     * @return string a prepended url with the lang.
     */
    public function prepend($uri, $lang = NULL) {
        return ($lang !== NULL ? $lang : I18n::lang()) . '/' . ltrim($uri, '/');
    }

    /**
     * Unprepend a lang on a uri.
     * @param string $uri
     * @return string
     */
    public function unprepend($uri) {
        // Remove the prepended language in url if exists
        //$langs = (array) Kohana::$config->load('urlang.langs');

        $uri = preg_replace('~^(?:' . implode('(-[a-z]{2})?|', $this->_langs) . '(-[a-z]{2})?)(?=/|$)~i', "", $uri);

        if (strlen($uri) > 0 && $uri[0] === "/") {
            $uri = substr($uri, 1);
        }

        return $uri;
    }

    /**
     * Turns uri into translation.
     * @param string $uri An uri to translate.
     * @param string $lang To override the destination lang.
     * @return string The uri translated version.
     */
    public function uri_to_translation($uri, $lang = NULL) {

        $parts = explode("/", $uri);
        $source = i18n::lang();

        // temporarily change target language
        i18n::lang('url-' . ($lang ? $lang : $source));

        // On traduit chacune des parties de l'url dans la langue de destination
        foreach ($parts as &$part) {
            $part = __($part);
        }

        i18n::lang($source);

        return implode("/", $parts);
    }

    /**
     * 
     * @param string $translation
     * @return string
     */
    public function translation_to_uri($translation) {

        $hashtag = "";
        if ($pos = strpos($translation, "?") | $pos = strpos($translation, "#")) {
            $hashtag = substr($translation, $pos);
            $translation = str_replace($hashtag, "", $translation);
        }

        $parts = explode('/', $translation);

        foreach ($parts as &$part) {

            // On doit mettre la langue courrante en premier dans le tableau !
            if ($index = array_search(i18n::lang(), $this->_langs)) {
                $temp = $this->_langs[0];
                $this->_langs[0] = $this->_langs[$index];
                $this->_langs[$index] = $temp;
            }

            foreach ($this->_langs as &$lang) {

                $table = i18n::load('url-' . $lang);

                if ($key = array_search($part, $table)) {
                    $part = $key;
                }
            }
        }

        return implode('/', $parts) . $hashtag;
    }

    /**
     * Retuns the suggested lang based on data in uri, cookies and browser language.
     * @param string $uri     
     * @return string
     */
    public function suggested_lang($uri, $fallback = NULL) {

        $parts = explode("/", $uri);

        // Matches the prepended language.
        /*if (count($parts) > 0 && in_array($parts[0], $this->_langs)) {
            return $parts[0];
        }*/
		if (count ($parts) > 0 AND count (explode('-', $parts[0])) > 1)
		{
			$langue = explode('-', $parts[0]);

			while (count (explode('-', $parts[0])) > 1)
			{
				if (in_array($parts[0], $this->_langs))
				{
					return $parts[0];
				}

				//Pops the last element from the array to compare the language
				$array_langue = explode('-', $parts[0]);
				array_pop($array_langue);
				$parts[0] = implode('-', $array_langue);
			}
		}

        // Match the first part of the uri that has a translated value by url files.
        foreach ($parts as &$part) {            

            foreach ($this->_langs as &$lang) {
                
                // Safe to use, translation tables are cached in I18n
                $table = i18n::load('url-' . $lang);

                if ($key = array_search($part, $table)) {
                    return $lang;
                }
            }
        }

        // Default fallback is the index 0 of langs array.
        // This array cannot be empty.
        if ($fallback === NULL)
            $fallback = $this->_langs[0];

        // If request is available, we can grab the fallback from the browser language.
        if (Request::$current !== NULL) {
            $fallback = Request::$current->headers()->preferred_language($this->_langs);
        }

        return Cookie::get("lang", $fallback);
    }

}

?>
