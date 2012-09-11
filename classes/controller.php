<?php

defined('SYSPATH') or die('No direct script access.');


class Controller extends Kohana_Controller {

    public function before() {
        parent::before();

        I18n::lang(Urlang::instance()->suggested_lang(Request::current()->uri()));

        // Stockage de la langue en cookie
        Cookie::set('lang', I18n::lang());
    }

}

?>
