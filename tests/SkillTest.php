<?php

/** 
 * Class to test functionality of Skill Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class SkillTest extends TestCase {


    //Should be something that will never be used in reality
    private $testSkillCategoryName;
    private $testEmail;
    private $adminUser;
    private $testEmployerEmail;
    private $skillCatId;
    private $testJobName;


    /**
     * Tests for skill creation/renaming
     * 
     */

    public function testConstructorZero() {
        $skill = new \Classes\Skill(0);
        $this->assertSame(0, $skill->skillId);
    }


    //Attempt to insert new skill to category that doesn't exist

    public static function createAdminUser($testEmail) {
        return new \Classes\User(UserTest::saveNewUser($testEmail,3));
    }

    public static function createSkill($skillName, $skillCatId, $adminUser) {
        $skill = new \Classes\Skill(0);
        $skill->skillName = $skillName;
        $skill->skillCategoryId = $skillCatId;
        $objSave = $skill->Save($adminUser);
        return $objSave;
    }

    public function testSaveInvalidCategory() {
        //assume -1 will never be a valid skillCategoryId
        $objSave = $this->createSkill("SomeSkill", -1, $this->adminUser);
        $this->assertTrue($objSave->hasError);
    }

    //Attempt to insert new skill to valid category
    //Attempt to rename a skill

    public function testSaveRenameChangeCat() {
        $origObjSave = $this->createSkill("SomeSkill", $this->skillCatId, $this->adminUser);
        $skill = new \Classes\Skill($origObjSave->objectId);
        $skill->skillName = "SomeOtherSkill";
        $renameObjSave = $skill->Save($this->adminUser);
        $this->assertFalse($origObjSave->hasError);
        $this->assertFalse($renameObjSave->hasError);
    }


    /**
     * Tests for skill deletion
     * 
     */


    //Attempt to delete a skill using Id that doesn't exist

    public function testDeleteSkillFailure() {
        //verify no skill with -1 as skillId
        $result = \Classes\Skill::DeleteSkill(-1);
        
        $this->assertTrue($result->hasError);
    }

    //Attempt to delete a real skill

    public function testDeleteSkillSuccess() {
        $objSave = $this->createSkill("Some Skill", $this->skillCatId, $this->adminUser);
        $skillId = $objSave->objectId;
        $result = \Classes\Skill::DeleteSkill($skillId);
        $this->assertFalse($result->hasError);
    }


    //Attempt to delete a skill that has related entries in both job_skill and job_seeker_skill tables
    //with and without using the "I'm sure" flag

    public static function createJobAddSkill($testEmail, $testJobName, $skillCatId, $adminUser) {
        $result = JobTest::createEmployerAndJob($testEmail, $testJobName);
        extract($result); //extract yields $jid
        
        //add category
        $job = new \Classes\Job($jid);
        $job->skillCategoryId = $skillCatId;
        $job->Save();

        //create skill
        $skillObjSave = SkillTest::createSkill("Looking Up",
                $skillCatId, $adminUser);
        $skillId = $skillObjSave->objectId;

        //add skill
        \Classes\Job::SaveJobSkills($jid, $skillId);

        return array('jid' => $jid, 'skillId' => $skillId);
        
        //anything calling this method should also call the below at the end
        //JobTest::deleteJobTestTempRecords($testEmail);
    }

    public function testDeleteUsedSkill() {
        extract(SkillTest::createJobAddSkill($this->testEmployerEmail, $this->testJobName, $this->skillCatId, $this->adminUser));
        //extract yields $jid, $skillId

        $job = new \Classes\Job($jid);

        $deleteTestOneResult = \Classes\Skill::DeleteSkill($skillId);
        $deleteTestTwoResult = \Classes\Skill::DeleteSkill($skillId, true);
        $jobTestCleanupResult = JobTest::deleteJobTestTempRecords($this->testEmployerEmail);
        if ($jobTestCleanupResult[0] == false) {
            $this->assertTrue(false, $jobTestCleanupResult[1]);
        }
        $this->assertTrue($deleteTestOneResult->hasError);
        $this->assertFalse($deleteTestTwoResult->hasError);
    }


    /**
     * Tests for GetSkillsBySkillCategory($skillCategoryId)
     * 
     */


    //Try with both legit and illegit skillCategoryIds

    public function testGetSkillsBySkillCategoryFailure() {
        $result = \Classes\Skill::GetSkillsBySkillCategory(-1);
        $this->assertTrue(count($result) == 0);
    }

    public function testGetSkillsBySkillCategorySuccess() {
        SkillTest::createSkill("Some test skill", $this->skillCatId, $this->adminUser);
        $result = \Classes\Skill::GetSkillsBySkillCategory($this->skillCatId);
        $this->assertTrue(count($result) == 1);
    }



    /**
     * Test for GetSkillsByJob($jobId)
     * 
     */

    public function testGetSkillsByJob() {
        extract(SkillTest::createJobAddSkill($this->testEmployerEmail, $this->testJobName, $this->skillCatId, $this->adminUser));
        //extract gives $jid, $skillId

        $skillsArr = \Classes\Skill::GetSkillsByJob($jid);
        $jobTestCleanupResult = JobTest::deleteJobTestTempRecords($this->testEmployerEmail);
        if ($jobTestCleanupResult[0] == false) {
            $this->assertTrue(false, $jobTestCleanupResult[1]);
        }
        $this->assertTrue(count($skillsArr) == 1);
        $this->assertTrue(($skillsArr[0])->skillId == $skillId);
    }



    /**
     * Test for GetSkillsByJobSeeker($jobSeekerId)
     * 
     */

    public function testGetSkillsByJobSeeker() {
        //create jobseeker
        $jsTestEmail = "I_Seek_jobs@email.com";
        extract(JobSeekerTest::createUserAndJobSeeker($jsTestEmail));
        //extract yields $oid, $jid, $jobSeeker

        //Set job seeker category
        $jobSeeker->skillCategoryId = $this->skillCatId;
        $jobSeeker->Save();

        //check empty array if jobseeker has no skills
        $initRes = \Classes\Skill::GetSkillsByJobSeeker($jid);

        //create skill1
        $skill1 = SkillTest::createSkill("Some test skill", $this->skillCatId, $this->adminUser);

        //add skill1
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, $skill1->objectId);

        //check array of 1
        $res1 = \Classes\Skill::GetSkillsByJobSeeker($jid);

        //create skill2
        $skill2 = SkillTest::createSkill("Some other test skill", $this->skillCatId, $this->adminUser);

        //add skill2
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, join(",", array($skill1->objectId, $skill2->objectId)));

        //check array of 2
        $res2 = \Classes\Skill::GetSkillsByJobSeeker($jid);


        $this->assertSame(array(), $initRes);
        $this->assertEquals(1, count($res1));
        $this->assertEquals(2, count($res2));

        $jsTearDownRes = JobSeekerTest::tearDownByEmail($jsTestEmail);
        if ($jsTearDownRes[0] == false) {
            $this->assertTrue(false, $jsTearDownRes[1]);
        }
    }


    /**
     * Test for GetSkillExists($object)
     * 
     */

    public function testGetSkillExists() {
        $dodgySkill = new \Classes\Skill();
        $dodgySkill->skillName = "Whatever";
        $dodgySkill->skillCategoryId = "-1";

        $realSkillSave = $this->createSkill("Some Skill", $this->skillCatId, $this->adminUser);
        $realSkillId = $realSkillSave->objectId;

        $realSkillDuplicate = new \Classes\Skill();
        $realSkillDuplicate->skillName = "Some Skill";
        $realSkillDuplicate->skillCategoryId = $this->skillCatId;

        $this->assertFalse(\Classes\Skill::GetSkillExists($dodgySkill));
        $this->assertTrue(\Classes\Skill::GetSkillExists($realSkillDuplicate));
    }


    
    /**
     * setUp and tearDown section
     * 
     */

    //refactored to allow external use
    public static function staticSetupSkillCat($skillCatName) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql = 'insert into skill_category(SkillCategoryName) values (?)';
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $skillCatName);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_stmt_affected_rows($stmt) != 1) {
                $errMsg = $conn->errno . ' ' . $conn->error;
                $stmt->close();
                $conn->close();
                return array(false, "Failure to insert skillCategory with (intentionally unlikely to be used in reality) name '"
                                        .$skillCatName."':".PHP_EOL.$errMsg);
            }
            $skillCatId = $stmt->insert_id;
            $stmt->close();
        } else {
            $errMsg = $conn->errno . ' ' . $conn->error;
            $conn->close();
            return array(false, "Error in database query in setUp function:".PHP_EOL.$errMsg);
        }
        $conn->close();
        return array(true, array('skillCatId' => $skillCatId));
    }


    public static function staticSetup($skillCatName, $testEmail, $verified = 0) {
        $skillCatRes = SkillTest::staticSetupSkillCat($skillCatName);
        if ($skillCatRes[0] == false) {
            return $skillCatRes;
        }
        $skillCatId = $skillCatRes[1]['skillCatId'];

        $adminSetupRes = AdminStaffTest::staticSetupAdminUser($testEmail, $verified);
        if ($adminSetupRes[0] == false) {
            return $adminSetupRes;
        }
        $adminUser = $adminSetupRes[1]['adminUser'];
        return array(true, array('adminUser' => $adminUser, 'skillCatId' => $skillCatId));
    }


    protected function setUp(): void {
        parent::setUp();
        
        $this->testSkillCategoryName = "!@#!@$%asdfd    1234~!@#"; //unlikely to conflict with anything ever used
        $this->testEmail = "someadminuserguy$#!@123.com";
        $this->testJobName = "Cloud Counter";
        $this->testEmployerEmail = "SomeEmployerDude@com.com.com";

        $setupResult = $this->staticSetup($this->testSkillCategoryName, $this->testEmail);
        if ($setupResult[0] == false) {
            $this->assertTrue(false, print_r($setupResult[1], true));
        }
        $this->adminUser = $setupResult[1]['adminUser'];
        $this->skillCatId = $setupResult[1]['skillCatId'];
    }


    public static function staticTearDownSkillCat($skillCategoryName) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        //Delete all skills in test skill category, and category itself
        
        foreach (array("delete from skill where skillCategoryId in (select skillcategoryid from skill_category where skillcategoryname = ?)",
                        "delete from skill_category where skillCategoryName = ?") as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $skillCategoryName);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_stmt_affected_rows($stmt) != 1 && $sql == "delete from skill_category where skillCategoryName = ?") {
                    $errorMessage = $conn->errno . ' ' . $conn->error;
                    $stmt->close();
                    $conn->close();
                    var_dump("Failure to delete skillCategory with (intentionally unlikely to be used in reality) name '"
                            .$skillCategoryName."':".PHP_EOL.$errorMessage);
                    return array(false, "Failure to delete skillCategory with (intentionally unlikely to be used in reality) name '"
                                        .$skillCategoryName."':".PHP_EOL.$errorMessage);
                }
                $stmt->close();
            } else {
                $errorMessage = $conn->errno . ' ' . $conn->error;
                var_dump($errorMessage);
                return array(false, "Error in database query in tearDown function:".PHP_EOL.$errorMessage);
            }
        }

        $conn->close();
        return array(true, "");
    }

    public static function staticTearDown($skillCategoryName, $adminUser) {
        $skillCatRes = SkillTest::staticTearDownSkillCat($skillCategoryName);
        if ($skillCatRes[0] == false) {
            return $skillCatRes;
        }

        //delete the adminUser created to create the skills
        $adminRes = AdminStaffTest::tearDownAdminByEmail($adminUser->email);

        return $adminRes;
    }

    protected function tearDown(): void {
        $tearDownResult = $this->staticTearDown($this->testSkillCategoryName, $this->adminUser);
        //Actaully do need the if clause - without it the assert function always runs, and it seems in PHPUnit no code after
        //an assert statement runs, even if the assert statement passes
        if ($tearDownResult[0] == false) {
            $this->assertTrue(false, $tearDownResult[1]);
        }
        parent::tearDown();
    }
    

}

?>