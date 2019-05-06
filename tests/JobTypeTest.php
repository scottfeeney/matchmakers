<?php

/**
 * (Ludicrously) Trivial test class for the jobType class
 * Given the simplicity of this class, a good argument could probably be made
 * for not creating a test class for it at all.
 * 
 * Author(s): Blair
 */

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
        foreach ($this->jobTypes as $index => $jobType) {
            $this->assertSame($jobType, (new \Classes\JobType($index))->jobTypeName);
        }
    }

    public function testGetJobTypes() {
        //Construct array to compare to, elements in order by jobTypeName as that is 
        //the order criteria in the method being tested's query

        $compArr = array(   new \Classes\JobType(1),
                            new \Classes\JobType(4),
                            new \Classes\JobType(2),
                            new \Classes\JobType(3),
                            new \Classes\JobType(5));
        $fromGetJobTypes = \Classes\JobType::GetJobTypes();
        $this->assertEquals($compArr, $fromGetJobTypes);
        $this->assertSame(count($compArr), count($fromGetJobTypes));
    }

}

?>