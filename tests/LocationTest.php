<?php

/**
 * (Ludicrously) Trivial test class for the location class
 * Given the simplicity of this class, a good argument could probably be made
 * for not creating a test class for it at all.
 * 
 * Author(s): Blair
 */

use PHPUnit\Framework\TestCase;


final class LocationTest extends TestCase {

    //As we do with jobTypes, we are presuming these locations will
    //not change, at least at this stage (realistically we would probably
    //retire these locations and input more fine-grained ones)
    private $locationsSorted = array( 8 => "Sydney", 6 => "Melbourne",
            2 => "Brisbane", 7 => "Perth", 1 => "Adelaide",
            3 => "Canberra", 5 => "Hobart", 4 => "Darwin");

    private $locations = array( 1 => "Sydney", 2 => "Melbourne",
            3 => "Brisbane", 4 => "Perth", 5 => "Adelaide",
            6 => "Canberra", 7 => "Hobart", 8 => "Darwin");

    public function testConstructorZero() {
        $location = new \Classes\Location(0);
        $this->assertSame(0, $location->locationId);
    }

    public function testConstructorValues() {
        foreach ($this->locations as $index => $location) {
            $this->assertSame($location, (new \Classes\Location($index))->name);
        }
    }

    public function testGetLocations() {
        //Construct array to compare to, elements in order by jobTypeName as that is 
        //the order criteria in the method being tested's query
        $fromGetLocations = \Classes\Location::GetLocations();
        foreach ($this->locationsSorted as $index => $location) {
            $this->assertEquals($location, ($fromGetLocations[$index-1])-> name);
        }
        $this->assertSame(count($fromGetLocations), count($this->locationsSorted));
    }

}

?>