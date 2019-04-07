<?php

use PHPUnit\Framework\TestCase;


final class JobTypeTest extends TestCase {

    //Presumed entirely static
    private $jobTypes = array( 1 => "Casual", 2 => "Full-time",
            3 => "Part-time", 4 => "Contract", 5 => "Temporary");

    public function testConstructorZero() {
        $jobType = new \Classes\JobType(0);
        $this->assertSame(0, $jobType->jobTypeId);
    }

    public function testConstructorValues() {
        $this->assertSame("Casual", (new \Classes\JobType(1))->jobTypeName);
        $this->assertSame("Full-time", (new \Classes\JobType(2))->jobTypeName);
        $this->assertSame("Part-time", (new \Classes\JobType(3))->jobTypeName);
        $this->assertSame("Contract", (new \Classes\JobType(4))->jobTypeName);
        $this->assertSame("Temporary", (new \Classes\JobType(5))->jobTypeName);
    }

    public function testGetJobTypes() {
        //Construct array to compare to, elements in order by jobTypeName as that is 
        //the order criteria in the method being tested's query
        //Could use usort but we are presuming static jobTypeIds
        $compArr = array(   new \Classes\JobType(1),
                            new \Classes\JobType(4),
                            new \Classes\JobType(2),
                            new \Classes\JobType(3),
                            new \Classes\JobType(5));
        $this->assertEquals($compArr, \Classes\JobType::GetJobTypes());
    }

}

?>