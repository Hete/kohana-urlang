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
    
    
    
    /**
     * Test common translation
     */
    public function test_translate() {}
    
    /**
     * Test the prepend feature
     */
    public function test_prepend() {
        
        $s = "blabla";
        
        $this->assertEquals("fr/blabla", Urlang::instance()->prepend($s, "fr"));
        
    }
    
}

?>
