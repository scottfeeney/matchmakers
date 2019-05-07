<?php

/** 
 * Class to test functionality of Job Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class JobTest extends TestCase {

    private $testJobName = "Widget Wrangler";
    private $jidsToDelete;
    private $uidsToDelete;
    private $testEmail = "jobposter@jobpostingplace.com";
    private $checkJobDeleted;
    private $checkEmployerDeleted;
    private $checkUserDeleted;

    public function testConstructorNoInput() {
        $job = new \Classes\Job();
        $this->checkJobDeleted = 0; //indicate to cleanup that there isn't expected to be a job record to delete
        $this->checkEmployerDeleted = 0; //indicate to cleanup that there isn't expected to be a employer record to delete
        $this->checkUserDeleted = 0; //indicate to cleanup that there isn't expected to be a user record to delete
        $this->assertSame(0, $job->jobId);
    }

    public static function createEmployerAndJob($testEmail, $testJobName) {
        $result = EmployerTest::createUserAndEmployer($testEmail);
        if ($result == null) {
            return null;
        }
        extract($result); //$oid, $eid, $employer (employer obj)

        $job = new \Classes\Job();
        //Assumes there are 5 legitimate skillcategories with ids 1-5
        $categoryId = mt_rand(1,5);
        
        //$jid, $objSave
        extract(JobTest::createJob($eid, $categoryId, $testJobName));
        
        return array('oid' => $oid, 'jid' => $jid, 'objSave' => $objSave,
                     'categoryId' => $categoryId, 'eid' => $eid);
    }

    public function testNewJob() {
        $result = ($this->createEmployerAndJob($this->testEmail, $this->testJobName));
        if ($result == null) {
            $this->assertTrue(false, "createEmployerAndJob failed (returned null)");
        }
        //var_dump($result);
        extract($result); //$oid, $jib, $objSave, $categoryId, $eid
        $this->uidsToDelete[] = $oid;
        $this->jidsToDelete[] = $jid;
        $this->assertFalse($objSave->hasError);
        $this->assertNotEquals(0, $jid);
    }

    public static function createJob($eid, $catid, $name) {
        $job = new \Classes\Job();
        $job->employerId = $eid;
        $job->skillCategoryId = $catid;
        $job->jobName = $name;
        //var_dump($job);
        $objSave = $job->Save();
        //var_dump($objSave);
        return array("jid" => $objSave->objectId, "objSave" => $objSave);
    }

    //New methods added to Job object that need test functions:
    
    //static function SaveJobSkills($jobId, $selectedSkills)
    //remove any skills for this job that aren't in the list but are in job_skill table
    //add any skills for this job that are in the list but aren't in job_skill table

    //static function GetJobsByEmployer($employerId)
    //return array of job objects

    //static function GetSkillsByJobString($jobId)
    //return comma-joined string of skillIDs for skills that the specified job has
    //(according to Skill::GetSkillsByJob - should both of these methods be in the
    //same class?)

    public function testSaveGetJobSkills() {
        $result = ($this->createEmployerAndJob($this->testEmail, $this->testJobName));
        if ($result == null) {
            $this->assertTrue(false, "createEmployerAndJob failed (returned null)");
        }
        extract($result); //$oid, $jid, $objSave, $categoryId, $eid
        $this->uidsToDelete[] = $oid;
        $this->jidsToDelete[] = $jid;
        $allSkills = $this->GetAllSkills();

        //Pick some random skills, save, get, make sure they match
        $numSkills = 9; //mt_rand(4,9);
        $skillsStrings = array();
        $skillsStrings['initSave'] = "";
        $skillsStrings['initGot'] = "";
        $skillsStrings['secondSave'] = "";
        $skillsStrings['secondGot'] = "";
        $origSkillsArr = array();

        //var_dump($allSkills[$categoryId][mt_rand(0, count($allSkills[$categoryId]))]);
        //$this->tearDown();
        //die;

        for ($i = 0; $i < $numSkills; $i++) {
            do {
                $newSkill = $allSkills[$categoryId][mt_rand(0, count($allSkills[$categoryId])-1)];
            //make sure we don't select the same skill twice
            //(or input string won't match output string as duplicates
            //will have been removed)
            } while (in_array($newSkill->skillId, $origSkillsArr));
            $origSkillsArr[] = $newSkill->skillId;
            if ($skillsStrings['initSave'] == "") {
                $skillsStrings['initSave'] = "".$newSkill->skillId;
            } else {
                $skillsStrings['initSave'] .= ",".$newSkill->skillId;
            }
        }


        \Classes\Job::SaveJobSkills($jid, $skillsStrings['initSave']);
        $skillsStrings['initGot'] = \Classes\Job::GetSkillsByJobString($jid);

        //Repeat (ensuring that at least one new skill is added and one old skill
        //removed)
        do {
            //var_dump("Iteration");
            $newSkillsArr = array();
            $skillsStrings['secondSave'] = "";
            for ($i = 0; $i < $numSkills; $i++) {
                do {
                    $newSkill = $allSkills[$categoryId][mt_rand(0, count($allSkills[$categoryId])-1)];
                //as above - skills must be unique
                } while (in_array($newSkill->skillId, $newSkillsArr));
                $newSkillsArr[] = $newSkill->skillId;
                if ($skillsStrings['secondSave'] == "") {
                    $skillsStrings['secondSave'] = "".$newSkill->skillId;
                } else {
                    $skillsStrings['secondSave'] .= ",".$newSkill->skillId;
                }
            }
        //if skill list is the same, regenerate. If different, must have
        //at least one skill to remove and one to add, as same number of skills
        //generated.

        //array_diff returns number of items in first array that aren't in second array
        //as we know both arrays have same number of skills, don't need to do inverse
        //check
        } while (count(array_diff($newSkillsArr,$origSkillsArr)) == 0);

        \Classes\Job::SaveJobSkills($jid, $skillsStrings['secondSave']);
        $skillsStrings['secondGot'] = \Classes\Job::GetSkillsByJobString($jid);
        
        $jobsByEmp = \Classes\Job::GetJobsByEmployer($eid);

        //var_dump(explode(",",$skillsStrings['initSave']));
        $skillsArr['initSave'] = explode(",",$skillsStrings['initSave']);
        $skillsArr['initGot'] = explode(",",$skillsStrings['initGot']);
        $skillsArr['secondSave'] = explode(",",$skillsStrings['secondSave']);
        $skillsArr['secondGot'] = explode(",",$skillsStrings['secondGot']);


        //check results
        $this->assertEquals(0, count(array_diff($skillsArr['initSave'],$skillsArr['initGot'])));
        $this->assertEquals(0, count(array_diff($skillsArr['secondSave'],$skillsArr['secondGot'])));
        $this->assertEquals(1, count($jobsByEmp));
        $this->assertEquals($jid, ($jobsByEmp[0])->jobId);
    }

    public static function GetAllSkills() {
        $allSkills = array();
    
        foreach (array(1,2,3,4,5) as $curr) {
            $allSkills[$curr] = \Classes\Skill::GetSkillsBySkillCategory($curr);
        }

        return $allSkills;
    }

    private function matchingFormula($job, $jobSeeker) {
        return JobSeekerTest::matchingFormula($job, $jobSeeker);
    }

    //Test Matching
    //Tests required:
    
    //Only location matches - not listed, 25%
    
    //only jobtype matches - not listed, 25%
    
    //Both jobtype and location match, no skills match, seeker has no skills selected (yes, currently impossible through UI, even so)
    //listed, 50%

    //Both jobtype and location match, no skills match, seeker has 3 skills selected, job has 2 skills
    //listed, 50%

    //Both jobtype and location match, seeker has 5 skills, 4 of them match, job has 4 skills
    //listed, 97.72 repeating %

    //Both jobtype and location match, seeker has 5 skills, 4 of them match, job has 8 skills
    //listed 74.43 18 repeating %

    //Both jobtype and location match, seeker has 5 skills, 5 of them match, job has 5 skills
    //listed 100%

    //both jobtype and location match, seeker has 5 skills, 5 of them match, job has 12 skills
    //listed 70.8 3 repeating

    //jobtype doesn't match location does, seeker has 5 skills, 4 match, job has 8 skills
    //not listed 49.43 18 repeating %

    //location doesn't match jobtype does seeker has 5 skills 4 match job has 8 skills
    //not listed 49 43 18 repeating %

    //jobtype doesn't match location does, seeker has 5 skills, 4 match, job has 6 skills
    //listed 57.57 repeating

    //location doesn't match jobtype does, seeker has 5 skills, 4 match, job has 6 skills
    //listed 57.57 repeating

    //neither location or jobtype match, seeker has 5 skills 5 match job has 5 skills
    //listed 50

    //neither location or jobtype match, seeker has 5 skills 4 match job has 4 skills
    //not listed 47 72 repeating
    
    //neither location or jobtype match, seeker has 5 skills 5 match job has 6 skills
    //not listed 41.6 repeating

    public function testGetJobSeekerMatchesByJob() {
        //$this->assertFalse(true, print_r(\Classes\Job::GetJobMatchesByJobSeeker(1040)));
        $this->markTestIncomplete();
    }


    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
        $this->jidsToDelete = array();
        $this->checkJobDeleted = 1;
        $this->checkEmployerDeleted = 1;
        $this->checkUserDeleted = 1;
        //Quick cleanup
        //$this->uidsToDelete[] = 2061;
        //$this->jidsToDelete[] = 2146;
    }

    private static function runDelete($query, $failStr, $checkAffected, $testEmail) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        //delete skills
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $testEmail);
            //var_dump("Cleaning up - deleting user with id ".$oid);
            $stmt->execute();
            if ($checkAffected > 0) {
                $affected = $stmt->affected_rows;
                if ($affected != $checkAffected) {
                    $errorMessage = $conn->errno . ' ' . $conn->error;
                    $conn->close();
                    return array(false, $failStr.":".PHP_EOL.$affected." rows affected instead of 1".PHP_EOL.$errorMessage);
                }
            }
            $stmt->close();
        } else {
            $errorMessage = $conn->errno . ' ' . $conn->error;
            var_dump($errorMessage);
            $conn->close();
            return array(false, "Error in database query in deleteJobTestTempRecords function:".PHP_EOL.$errorMessage
                                .PHP_EOL."Query was ".$sql);
        }
        $conn->close();
        return array(true,"");
    }


    //Refactored to allow use from other test classes
    public static function deleteJobTestTempRecords($testEmail, $checkJobDeleted = 1, $checkEmployerDeleted = 1, $checkUserDeleted = 1) {

        $cleanupQueries = array(
            array("delete from job_skill where jobId in (Select jobId from job where employerId in (select employerId from employer where userId in (select userId from user where email = ?)))",
                "",0),
            array("delete from job where employerId in (select employerId from employer where userId in (select userId from user where email = ?))",
                "Could not delete test Job from database based on email ".$testEmail, $checkJobDeleted),
            array("delete from employer where userId in (select userId from user where email = ?)",
                "Could not delete test Employer from database based on email ".$testEmail, $checkEmployerDeleted),
            array("delete from api_token where userId in (Select userId from user where email = ?)", "", 0),
            array("delete from user where email = ?",
                "Could not delete test user from database based on email ".$testEmail, $checkUserDeleted)
            
        );

        foreach ($cleanupQueries as $cleanupQuery) {
            $query = $cleanupQuery[0];
            $failStr = $cleanupQuery[1];
            $checkAffected = $cleanupQuery[2];
            $result = JobTest::runDelete($query, $failStr, $checkAffected, $testEmail);
            if ($result[0] == false) {
                return $result;
            }
        }

        return array(true,"");
    }

    protected function tearDown(): void {
        $result = JobTest::deleteJobTestTempRecords($this->testEmail, $this->checkJobDeleted, $this->checkEmployerDeleted, $this->checkUserDeleted);
        //var_dump($result);
        if ($result[0] == false) {
            $this->assertTrue(false, $result[1]);
        }
        parent::tearDown();
    }
}

?>