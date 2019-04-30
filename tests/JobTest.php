<?php

use PHPUnit\Framework\TestCase;


final class JobTest extends TestCase {

    private $testJobName = "Widget Wrangler";
    private $jidsToDelete;
    private $uidsToDelete;
    private $testEmail = "jobposter@jobpostingplace.com";

    public function testConstructorNoInput() {
        $job = new \Classes\Job();
        $this->assertSame(0, $job->jobId);
    }

    public static function createEmployerAndJob($testEmail, $testJobName) {
        $result = EmployerTest::createUserAndEmployer($testEmail);
        if ($result == null) {
            return null;
        }
        extract($result); //$oid, $eid, $employer (employer obj)

        $job = new \Classes\Job();
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



    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
        $this->jidsToDelete = array();
        //Quick cleanup
        //$this->uidsToDelete[] = 2061;
        //$this->jidsToDelete[] = 2146;
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        //var_dump($conn);
        //var_dump("In cleanup with oid ".$oid);



        foreach ($this->jidsToDelete as $jid) {
            var_dump("Should now delete job_skill entries and job with jobid ".$jid);
            foreach(array('delete from job_skill where jobId = ?','delete from job where jobid = ?') as $sql) {
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $jid);
                    //var_dump("Cleaning up - deleting user with id ".$oid);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);

                    //uncomment after code adding skills to job is working
                    //if (mysqli_stmt_affected_rows($stmt) != 1) {
                    //    $this->assertTrue(false, "Could not delete test job from database");
                    //}
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                }
            }
        }

        foreach ($this->uidsToDelete as $idd) {
            var_dump("Should now delete employer and user with userid ".$idd);
            foreach (array('delete from employer where userid = ?', 'delete from user where UserId = ?') as $sql) {
            //$sql = 'delete from employer where userid = ?';
                if ($stmt = $conn->prepare($sql)) {
                    //var_dump($stmt);
                    $stmt->bind_param("i", $idd);
                    //var_dump("Cleaning up - deleting user with id ".$oid);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);
                    if (mysqli_stmt_affected_rows($stmt) != 1) {
                        $this->assertTrue(false, "Failure to delete employer or user record with userid ". $idd);
                    }
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                }
            }

            //Cleanup code that shouldn't be necessary unless something goes wrong in a previous run
            $sql = 'delete from employer where userid in (Select userid from user where email = ?)';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $this->testEmail);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
            }
            $sql = 'delete from employer where userid is null';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                //var_dump
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
            }
        }
        $conn->close();
        parent::tearDown();
    }
}

?>