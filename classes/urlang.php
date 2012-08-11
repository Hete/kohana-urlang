<?php

defined('SYSPATH') or die('No direct script access.');

class Urlang {

    private static $_suggested_lang;

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

    public static function uri_to_translation($uri) {


        $parts = explode("/", $uri);
        $source = i18n::lang();

        // temporarily change target language
        i18n::lang('url-' . $source);

        // On traduit chacune des parties de l'url dans la langue de destination
        foreach ($parts as &$part) {
            $part = __($part);
        }

        i18n::lang($source);

        return implode("/", $parts);
    }

    public static function translation_to_uri($translation) {

        Urlang::$_suggested_lang = NULL;

        $parts = explode('/', $translation);
        foreach ($parts as &$part) {
            $part = Urlang::get_key($part);
        }
        
        if(Urlang::$_suggested_lang && Kohana::$config->load('urlang.autotranslate')) {
            i18n::lang(Urlang::$_suggested_lang);
            Cookie::set('lang', Urlang::$_suggested_lang);
                
        }
        

        return implode('/', $parts);
    }

}

?>
