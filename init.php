<?php

defined('SYSPATH') or die('No direct script access.');


// Tests and stuff

if (count(Kohana::$config->load('urlang.langs')) < 1) {
    throw new Kohana_Exception("langs parameter of urlang must contain at least one element.");
}
?>
