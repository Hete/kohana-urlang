<?php

class Controller extends Kohana_Controller {

    public function before() {
        parent::before();

        I18n::lang(Urlang::suggested_lang(Request::current()->uri(), $this->request->headers()->preferred_language(Kohana::$config->load("urlang.langs"))));

        // Stockage de la langue en cookie
        Cookie::set('lang', I18n::lang());
    }

}

?>
