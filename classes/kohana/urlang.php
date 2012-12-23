<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Urlang is an url translator. All utility functions are found in here.
 * 
 * @package Urlang
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2012, Hète.ca Inc.
 */
class Kohana_Urlang {

    /**
     * Singleton
     * @var Urlang 
     */
    protected static $_instance;

    /**
     * Supported langs.
     */
    private $_langs;

    protected function __construct() {
        $this->_langs = (array) Kohana::$config->load('urlang.langs');
    }

    /**
     * Instance for the singleton.
     * @return Urlang
     */
    public static function instance() {
        return Urlang::$_instance ? Urlang::$_instance : Urlang::$_instance = new Urlang();
    }

    /**
     * Alter supported langs.
     * @param array|string $lang
     * @return \Kohana_Urlang for builder syntax.
     */
    public function langs($lang = NULL) {
        if ($lang === NULL) {
            return $this->_langs;
        }

        if (Arr::is_array($lang)) {
            $this->_langs = $lang;
        } else {
            $this->_langs[] = $lang;
        }

        // Remove duplicated entries
        $this->_langs = array_unique($this->_langs);

        return $this;
    }

    /**
     * Appends the lang in I18n::lang() or the $lang parameter if specified.
     * @param string $uri 
     * @param string $lang is the lang to prepend.
     * @return string a prepended url with the lang.     * 
     */
    public function append($uri, $lang = NULL) {
        return rtrim($uri, '/') . '/' . ($lang !== NULL ? $lang : I18n::lang());
    }

    /**
     * Unappend an uri.
     * @param string $uri
     * @return string
     */
    public function unappend($uri) {
        return preg_replace("/\/*(" . implode('|', $this->_langs) . ")\/?/", "", $uri);
    }

    /**
     * Prepends the lang in I18n::lang() or the $lang parameter if specified.
     * @param string $uri 
     * @param string $lang is the lang to prepend.
     * @return string a prepended url with the lang.
     */
    public function prepend($uri, $lang = NULL) {
        return ($this->is_absolute($uri) ? "/" : "") . ($lang !== NULL ? $lang : I18n::lang()) . '/' . ltrim($uri, '/');
    }

    /**
     * Unprepend a lang on a uri.
     * @param string $uri
     * @return string
     */
    public function unprepend($uri) {
        
        return preg_replace("/^(" . implode('|', $this->_langs) . ")(\/)?/", "", $uri, 1);
    }

    /**
     * Alias for uri_to_translation.
     * @param type $uri
     * @param type $lang
     * @return type
     */
    public function translate($uri, $lang = NULL) {

        if (Kohana::$profiling) {
            $benchmark = Profiler::start("Urlang", __FUNCTION__);
        }

        if (!$this->translateable($uri)) {
            return $uri;
        }     

        // Translate
        $translated = $this->uri_to_translation($uri, $lang);

        $prepended = $this->prepend($translated, $lang);

        if (isset($benchmark)) {
            Profiler::stop($benchmark);
        }

        // Return prepended version
        return $prepended;
    }

    /**
     * Alias for translation_to_uri.
     * @param string $uri uri to untranslate 
     * @return string untranslated uri
     */
    public function untranslate($uri) {

        if (Kohana::$profiling) {
            $benchmark = Profiler::start("Urlang", __FUNCTION__);
        }

        if (!$this->translateable($uri)) {
            return $uri;
        }

        // Unprepend
        $uri = $this->unprepend($uri);

        $untranslated = $this->translation_to_uri($uri);

        if (isset($benchmark)) {
            Profiler::stop($benchmark);
        }

        return $untranslated;
    }

    /**
     * Determine if an uri is translateable.
     * @param string $uri
     * @return boolean 
     */
    public function translateable($uri) {

        // Do not translate uri containing ://, it's generaly external request

        if (strpos($uri, "://") !== FALSE) {
            return FALSE;
        }

        // In all other cases, we assume it is translateable

        return TRUE;
    }

    /**
     * Turns uri into translation.
     * @param string $uri An uri to translate.
     * @param string $lang To override the destination lang.
     * @return string The uri translated version.
     */
    public function uri_to_translation($uri, $lang = NULL) {

        list($uri, $query) = $this->extract_query($uri);

        $parts = explode("/", $uri);
        $source = i18n::lang();

        // temporarily change target language
        i18n::lang('url-' . ($lang ? $lang : $source));

        // On traduit chacune des parties de l'url dans la langue de destination
        foreach ($parts as &$part) {
            $part = __($part);
        }

        i18n::lang($source);

        return ($this->is_absolute($uri) ? "/" : "") . implode("/", $parts) . $query;
    }

    /**
     * Take a translated uri and get its original value.
     * @param string $translation
     * @return string
     */
    public function translation_to_uri($translation) {

        list($translation, $query) = $this->extract_query($translation);

        $parts = explode('/', $translation);

        foreach ($parts as &$part) {

            // On doit mettre la langue courrante en premier dans le tableau !
            if ($index = array_search(i18n::lang(), $this->_langs)) {
                $temp = $this->_langs[0];
                $this->_langs[0] = $this->_langs[$index];
                $this->_langs[$index] = $temp;
            }

            foreach ($this->_langs as $lang) {

                $table = I18n::load('url-' . $lang);

                if ($key = array_search($part, $table)) {
                    $part = $key;
                }
            }
        }

        return ($this->is_absolute($translation) ? "/" : "") . implode('/', $parts) . $query;
    }

    /**
     * Extracts end of string query such as hashtags or question mark.
     * @param string $uri uri to be extracted.
     * @return array first element is the cleaned uri, second is the query.
     */
    public function extract_query($uri) {

        // Find pos of first ?

        $pos_query = strpos($uri, "?") ? strpos($uri, "?") : strlen($uri);


        $pos_hashtag = strpos($uri, "#") ? strpos($uri, "#") : strlen($uri);

        $pos = $pos_query < $pos_hashtag ? $pos_query : $pos_hashtag;

        $cleaned_uri = substr($uri, 0, $pos);

        $query = substr($uri, $pos);

        return array($cleaned_uri, $query);
    }

    /**
     * Retuns the suggested lang based on data in uri, cookies and browser language.
     * @param string $uri is the uri on which a lang will be suggested.
     * @param string $fallback lang to use if everything has failed.
     * @return string a suggested and supported lang.
     */
    public function suggested_lang($uri, $fallback = NULL) {

        // Default fallback is the index 0 of langs array.
        // This array cannot be empty.
        if ($fallback === NULL)
            $fallback = $this->_langs[0];

        $parts = explode("/", $uri);

        // Matches the prepended language.
        if (count($parts) > 0 && in_array($parts[0], $this->_langs)) {
            return $parts[0];
        }

        // Match the first part of the uri that has a translated value by url files.
        foreach ($parts as &$part) {

            foreach ($this->_langs as $lang) {

                // Safe to use, translation tables are cached in I18n
                $table = i18n::load('url-' . $lang);

                if ($key = array_search($part, $table)) {
                    return $lang;
                }
            }
        }

        // If request is available, we can grab the fallback from the browser language.
        if (Request::$current !== NULL) {
            $fallback = Request::$current->headers()->preferred_language($this->_langs);
        }

        // Cookie of fallback
        return Cookie::get("lang", $fallback);
    }

    /**
     * Test if an uri is absolute (starting with /)
     * @param string $uri
     */
    public function is_absolute($uri) {

        if (strlen($uri) === 0)
            return FALSE;

        return $uri[0] === "/";
    }

}

?>
