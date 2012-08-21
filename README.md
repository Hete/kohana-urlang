Urlang is a Kohana module that translates urls passively.

It is very simple to use. All you have to do, once the module is activated, is to create as many url lang files as you need :

i18n/
    url/
        en.php
        fr.php
        foo.php
        en/
            ca.php

Each file should contain a structure like this one :

fr.php

	return array(
		'home' => 'accueil'
		'about' => 'a-propos'
	);

As the module overrides Kohana framework at two points (when url are processed in the request and when url are printed using Url::site() or HTML::anchor), building lang files are the only necessary step to translate every urls in the website.

When you use HTML::anchor, you have to define controllers/action/... like usual, but not the translated version. The module will translate the uri on the go !
