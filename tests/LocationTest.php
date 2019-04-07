<?php

use PHPUnit\Framework\TestCase;


final class LocationTest extends TestCase {

    //As we do with jobTypes, we are presuming these locations will
    //not change, at least at this stage (realistically we would probably
    //retire these locations and input more fine-grained ones)
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
        $locationsSort = $this->locations;
        usort($locationsSort, array($this, "sortByName"));
        $fromGetLocations = \Classes\Location::GetLocations();
        foreach ($locationsSort as $index => $location) {
            $this->assertEquals($location, ($fromGetLocations[$index])-> name);
        }
        $this->assertSame(count($fromGetLocations), count($locationsSort));
    }

    private function sortByName($a, $b) {
        if ($a->name == $b->name) {
            return 0;
        }
        return ($a->name < $b->name) ? -1 : 1;
    }

}

?>