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
    }

    /**
     * 
     */
    public function test_is_absolute() {
        $this->assertFalse(Urlang::instance()->is_absolute(""));
        $this->assertFalse(Urlang::instance()->is_absolute("asdasd/asdasd"));
        $this->assertTrue(Urlang::instance()->is_absolute("/"));
        $this->assertTrue(Urlang::instance()->is_absolute("/asdasd/asdasd"));
    }

    public function test_extract_query() {

        list($uri, $query) = Urlang::instance()->extract_query("tata?2");

        $this->assertEquals($uri, "tata");
        $this->assertEquals($query, "?2");

        list($uri, $query) = Urlang::instance()->extract_query("tata#ha?2");

        $this->assertEquals($uri, "tata");
        $this->assertEquals($query, "#ha?2");
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
     * @depends test_is_absolute
     */
    public function test_prepend() {

        $s = "blabla";

        $this->assertEquals("fr/blabla", Urlang::instance()->prepend($s, "fr"));

        // Absolute uri must be preserved
        $this->assertEquals("/fr/blabla", Urlang::instance()->prepend("/blabla", "fr"));
    }

    /**
     * Test the prepend feature   
     * @depends test_is_absolute  
     */
    public function test_unprepend() {

        $this->assertEquals("blabla", Urlang::instance()->unprepend("tests-fr/blabla"));

        $this->assertEquals("", Urlang::instance()->unprepend("tests-fr/"));
        $this->assertEquals("", Urlang::instance()->unprepend("tests-fr"));


        // uri starting with a slash shouldn't be unprepended
        $this->assertEquals("/blabla", Urlang::instance()->unprepend("/tests-fr/blabla"));
    }

    /**
     * @depends test_is_absolute
     */
    public function test_append() {

        $this->assertEquals(Urlang::instance()->append("tata", "fr"), "tata/fr");

        // Absolute
        $this->assertEquals(Urlang::instance()->append("/tata", "fr"), "/tata/fr");
    }

    /**
     * @depends test_is_absolute
     */
    public function test_unappend() {
        $this->assertEquals(Urlang::instance()->unappend("tata/tests-fr"), "tata");

        // Absolute
        $this->assertEquals(Urlang::instance()->unappend("/tata/tests-fr"), "/tata");
    }

    /**
     * Test common translation
     * @depends test_extract_query
     * @depends test_translateable
     * @depends test_prepend
     * @depends test_is_absolute
     */
    public function test_translate() {

        $uri = "home/category";

        $translated = Urlang::instance()->translate($uri, "tests-fr");

        $this->assertEquals($translated, "tests-fr/accueil/categorie");

        $this->assertEquals(Urlang::instance()->translate("index.php/home/category", "tests-fr"), "tests-fr/index.php/accueil/categorie");


        // Preserve absoluteness
        $this->assertEquals(Urlang::instance()->translate("/home/category", "tests-fr"), "/tests-fr/accueil/categorie");

        // With parameters
        $this->assertEquals(Urlang::instance()->translate("home/category?b=2", "tests-fr"), "tests-fr/accueil/categorie?b=2");

        // With hashtag
        $this->assertEquals(Urlang::instance()->translate("home/category#b", "tests-fr"), "tests-fr/accueil/categorie#b");

        // With parameters and hashtag
        $this->assertEquals(Urlang::instance()->translate("home/category#b?b=2", "tests-fr"), "tests-fr/accueil/categorie#b?b=2");
    }

    /**
     * @depends test_translate
     * @depends test_unprepend
     * @depends test_is_absolute
     */
    public function test_untranslate() {

        $uri = "home/category";

        $translated = Urlang::instance()->translate($uri, "tests-fr");

        $untranslated = Urlang::instance()->untranslate($translated);

        $this->assertEquals($uri, $untranslated);
    }

}

?>
