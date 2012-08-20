<?php

defined('SYSPATH') or die('No direct script access.');

class Urlang {

    private static $_suggested_lang;
    
    public static function translate_current_page($lang) {        
        return "urlang/$lang";
    }

    /**
     * Scans all uri lang, revert associative array and tries to match a key.
     */
    private static function get_key($translated_value) {

        $configs = Kohana::$config->load('urlang.langs');
        /*
          if (Urlang::$_first_lang_to_scan) {
          if (array_count_values(Urlang::$_first_lang_to_scan) == 1) {

          $configs[array_search(Urlang::$_first_lang_to_scan, $configs)] = $configs[0];

          $configs[0] = Urlang::$_first_lang_to_scan;
          } else {
          throw new Kohana_Exception('Une clé de langue est inexistante dans les configurations de Urlang');
          }
          array_push($configs, $configs[0]);
          $configs[0] = Urlang::$_first_lang_to_scan;
          }
         * */

        // On doit mettre la langue courrante en premier dans le tableau !
        if ($index = array_search(i18n::lang(), $configs)) {
            $temp = $configs[0];
            $configs[0] = $configs[$index];
            $configs[$index] = $temp;
        }


        foreach ($configs as $lang) {

            $table = i18n::load('url-' . $lang);

            if ($key = array_search($translated_value, $table)) {
                if (!Urlang::$_suggested_lang)
                    Urlang::$_suggested_lang = $lang;
                return $key;
            }
        }
        return $translated_value;
    }

    /**
     * 
     * @param string $uri An uri to translate.
     * @param string $lang To override the destination lang.
     * @return string The uri translated version.
     */
    public static function uri_to_translation($uri, $lang = NULL) {

        $hashtag = "";
        if ($pos = strpos($uri, "?") | $pos = strpos($uri, "#")) {
            $hashtag = substr($uri, $pos);
            $uri = str_replace($hashtag, "", $uri);
        }

        $parts = explode("/", $uri);
        $source = i18n::lang();

        // temporarily change target language
        i18n::lang('url-' . ($lang ? $lang : $source));

        // On traduit chacune des parties de l'url dans la langue de destination
        foreach ($parts as &$part) {
            $part = __($part);
        }

        i18n::lang($source);

        return implode("/", $parts) . $hashtag;
    }

    public static function translation_to_uri($translation) {

        $hashtag = "";
        if ($pos = strpos($translation, "?") | $pos = strpos($translation, "#")) {
            $hashtag = substr($translation, $pos);
            $translation = str_replace($hashtag, "", $translation);
        }

        Urlang::$_suggested_lang = NULL;

        $parts = explode('/', $translation);
        foreach ($parts as &$part) {
            $part = Urlang::get_key($part);
        }

        if (Urlang::$_suggested_lang && Kohana::$config->load('urlang.autotranslate')) {
            i18n::lang(Urlang::$_suggested_lang);
            Cookie::set('lang', Urlang::$_suggested_lang);
        }


        return implode('/', $parts) . $hashtag;
    }

}

?>
