<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Urlang extends Controller {

    public function action_index() {
        $url = Urlang::translation_to_uri($this->request->referrer());

        i18n::lang($this->request->param('lang'));
        Cookie::set('lang', $this->request->param('lang'));


        $this->request->redirect(Urlang::uri_to_translation($url));
    }

}

?>
