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
    private $testEmail = "jobposter@jobpostingplace.com";
    private $checkJobDeleted;
    private $checkEmployerDeleted;
    private $checkUserDeleted;

    /**
     * 
     * No longer necessary - no longer planning to do any testing of the matching algorithm from this class
     * All such testing now done in JobSeekerTest
     * 
    //For matching algorithm testing
    private $testEmployerEmail = "algoTestingEmployerPerson@unitTests.com";
    private $testJSEmail = "algoTestingJSPerson@unitTests.com";
    private $jobName = "Statue Impersonator";
    private $skillCategoryName = "algoTestSkillCat";
    private $skillNames = array("Standing Upright", "Walking a straight line" , "Mixing Cocktails", "Sleeping through construction noise",
                                "Faking interest in foreign films", "Keeping it real", "Believing in yourself", "Mouthing mindless platitudes",
                                "Getting through a Tolstoy novel", "Growing a beard like Tolstoy", "Drinking craft beer", "Cooking charcoal");
    private $testAdminEmail = "algoTestingAdminPerson@unitTests.com";
    private $location1Name = "Colombia";
    private $location2Name = "Cuba";
    private $jobType1Name = "Dodgy";
    private $jobType2Name = "Legit";
    
    private $testJobSeeker;
    private $testSkillCategory;
    private $testEmployer;
    private $testJob;
    private $testSkills;
    private $testAdmin;
    private $location1Id;
    private $location2Id;
    private $jobType1Id;
    private $jobType2Id;
    */



    //Simplest test there could be
    public function testConstructorNoInput() {
        $job = new \Classes\Job();
        $this->checkJobDeleted = 0; //indicate to cleanup that there isn't expected to be a job record to delete
        $this->checkEmployerDeleted = 0; //indicate to cleanup that there isn't expected to be a employer record to delete
        $this->checkUserDeleted = 0; //indicate to cleanup that there isn't expected to be a user record to delete
        $this->assertSame(0, $job->jobId);
    }


    //Helper function to create job record (and employer record to be associated with it)
    //Could be probably refactored into setUp, although is working fine as is
    //Assumes that there are already 5 skillCategories in the database, with ids 1-5
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


    //As above but allows for specific category to be nominated
    public static function createEmployerAndJobChooseCategory($testEmail, $testJobName, $categoryId) {
        $result = EmployerTest::createUserAndEmployer($testEmail);
        if ($result == null) {
            return null;
        }
        extract($result); //$oid, $eid, $employer (employer obj)

        $job = new \Classes\Job();
        
        //$jid, $objSave
        extract(JobTest::createJob($eid, $categoryId, $testJobName));
        
        return array('job' => new \Classes\Job($jid), 'employer' => new \Classes\Employer($eid));
    }


    //Simple test of job creation
    public function testNewJob() {
        $result = ($this->createEmployerAndJob($this->testEmail, $this->testJobName));
        if ($result == null) {
            $this->assertTrue(false, "createEmployerAndJob failed (returned null)");
        }
        //var_dump($result);
        extract($result); //$oid, $jib, $objSave, $categoryId, $eid
        $this->assertFalse($objSave->hasError);
        $this->assertNotEquals(0, $jid);
    }


    //helper function to only create job (nominating employer and category)
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



    //UPDATE: New methods added to Job object that need test function:
    
    //static function SaveJobSkills($jobId, $selectedSkills)
    //remove any skills for this job that aren't in the list but are in job_skill table
    //add any skills for this job that are in the list but aren't in job_skill table

    //static function GetJobsByEmployer($employerId)
    //return array of job objects

    //static function GetSkillsByJobString($jobId)
    //return comma-joined string of skillIDs for skills that the specified job has

    public function testSaveGetJobSkills() {
        $result = ($this->createEmployerAndJob($this->testEmail, $this->testJobName));
        if ($result == null) {
            $this->assertTrue(false, "createEmployerAndJob failed (returned null)");
        }
        extract($result); //$oid, $jid, $objSave, $categoryId, $eid
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


    //this helper function assumes the existence of skillCategories with ids 1-5, and populated with skills
    public static function GetAllSkills() {
        $allSkills = array();
    
        foreach (array(1,2,3,4,5) as $curr) {
            $allSkills[$curr] = \Classes\Skill::GetSkillsBySkillCategory($curr);
        }

        return $allSkills;
    }


    /**
     * setUp and tearDown functions
     */
    
    protected function setUp(): void {
        parent::setUp();
        $this->checkJobDeleted = 1;
        $this->checkEmployerDeleted = 1;
        $this->checkUserDeleted = 1;

        
        //For algorithm matching unit tests - UPDATE: No longer necessary. Now doing all matching algorithm testing in JobSeekerTest

        //$matchingAlgoSetupRes = JobSeekerTest::setUpForMatchingAlgoTest($this->testJSEmail, $this->testEmployerEmail, $this->jobName, 
        //                                                                $this->skillCategoryName, $this->skillNames, $this->testAdminEmail,
        //                                                                $this->location1Name, $this->location2Name, $this->jobType1Name, $this->jobType2Name);
        
        //if ($matchingAlgoSetupRes[0] == false) {
        //    $this->assertTrue(false, $matchingAlgoSetupRes[1]);
        //}

        //list($this->testJobSeeker, $this->testEmployer, $this->testJob, $this->testSkillCategory, $this->testSkills,
        //        $this->testAdmin, $locIds, $jobTypeIds) = $matchingAlgoSetupRes;
        //$this->location1Id = $locIds[0];
        //$this->location2Id = $locIds[1];

        //$this->jobType1Id = $jobTypeIds[0];
        //$this->jobType2Id = $jobTypeIds[1];
    }

    //helper function for the below
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


        //No longer necessary - no longer planning to do any matching algorithm testing from this class

        //$algoTearDownRes = JobSeekerTest::tearDownAfterMatchingAlgoTest($this->testJSEmail, $this->testEmployerEmail, $this->skillCategoryName, 
        //                                                                $this->testAdminEmail, $this->location1Id, $this->jobType1Id,
        //                                                                $this->location2Id, $this->jobType2Id);
        //if ($algoTearDownRes[0] == false) {
        //    $this->assertTrue(false, $algoTearDownRes[1]);
        //}

        parent::tearDown();
    }
}

?>