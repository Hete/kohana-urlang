<?php

defined('SYSPATH') or die('No direct script access.');

$langs = implode("|", Kohana::$config->load('urlang.langs'));

Route::set('urlang', '<controller>/<lang>', array(
    'controller' => 'urlang',
    'lang' => $langs
        )
);
?>
