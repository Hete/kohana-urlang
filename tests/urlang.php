<?php

/**
 * Tests for Urlang.
 * 
 * Lang files are un url/tests/*.php
 * 
 * @package Urlang
 * @category Tests
 * @author Guillaume Poirier-Morency <gui>
 * @copyright (c) 2012, Hète.ca Inc.
 */
class Urlang_Test extends Unittest_TestCase {

    public function setUp() {
        parent::setUp();
        // Adding tests langs only
        Urlang::instance()->langs(array("tests-fr", "tests-en"));

        // Cookie::delete("lang");
    }

    public function test_suggested_lang() {

        $this->assertEquals(Urlang::instance()->suggested_lang("tests-fr/accueil/tat"), "tests-fr");
        $this->assertEquals(Urlang::instance()->suggested_lang("tests-en/accueil/tat"), "tests-en");

        // Should be tests-fr
        $this->assertEquals(Urlang::instance()->suggested_lang("accueil/categorie"), "tests-fr");
    }

    public function test_translateable() {

        $this->assertTrue(Urlang::instance()->translateable("accueil/categorie"));
        $this->assertFalse(Urlang::instance()->translateable("http://accueil/categorie"));
    }

    /**
     * Test the prepend feature     
     */
    public function test_prepend() {

        $s = "blabla";

        $this->assertEquals("fr/blabla", Urlang::instance()->prepend($s, "fr"));
    }

    /**
     * Test common translation
     * @depends test_translateable
     * @depends test_prepend
     */
    public function test_translate() {

        $uri = "home/category";

        $translated = Urlang::instance()->translate($uri, "tests-fr");

        $this->assertEquals($translated, "tests-fr/accueil/categorie");
    }

    /**
     * Test the prepend feature     
     */
    public function test_unprepend() {

        $s = "tests-fr/blabla";

        $this->assertEquals("blabla", Urlang::instance()->unprepend($s));
    }

    /**
     * @depends test_translate
     * @depends test_unprepend
     */
    public function test_untranslate() {

        $uri = "home/category";

        $translated = Urlang::instance()->translate($uri, "tests-fr");

        $untranslated = Urlang::instance()->untranslate($translated);

        $this->assertEquals($uri, $untranslated);
    }

}

?>
