<?php

/** 
 * Class to test functionality of JobSeeker Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class SkillTest extends TestCase {


    //Should be something that will never be used in reality
    private $testSkillCategoryName = "!@#!@$%asdfd    1234~!@#";
    private $testEmail = "someadminuserguy$#!@123.com";
    private $adminUser;
    private $skillCatId;
    private $testJobName = "Cloud Counter";
    private $uidsToDelete;


    public function testConstructorZero() {
        $skill = new \Classes\Skill(0);
        $this->assertSame(0, $skill->skillId);
    }

    //Need tests for (constructor, save($user) method)
    //Attempt to insert new skill to category that doesn't exist

    public static function createAdminUser($testEmail) {
        $user = UserTest::saveNewUser($testEmail);
        $user->userType = 3;
        $user->save();
        return $user;
    }

    public static function createSkill($skillName, $skillCatId, $adminUser) {
        $skill = new \Classes\Skill(0);
        $skill->skillName = "SomeSkill";
        $skill->skillCategoryId = $skillCatId;
        $objSave = $skill->Save($adminUser);
        return $objSave;
    }

    public function testSaveInvalidCategory() {
        //assume -1 will never be a valid skillCategoryId
        $objSave = createSkill("SomeSkill", -1, $this->adminUser);
        $this->assertTrue($objSave->hasError);
    }

    //Attempt to insert new skill to valid category
    //Attempt to rename a skill

    public function testSaveRenameChangeCat() {
        $origObjSave = createSkill("SomeSkill", $this->skillCatId, $this->adminUser);
        $skill = new \Classes\Skill($origObjSave->objectId);
        $skill->skillName = "SomeOtherSkill";
        $renameObjSave = $skill->Save($this->adminUser);
        $this->assertFalse($origObjSave->hasError);
        $this->assertFalse($renameObjSave->hasError);
    }

    //and following methods:
    
    //static function DeleteSkill($skillId)
    //Attempt to delete a skill using Id that doesn't exist

    public function testDeleteSkillFailure() {
        $result = \Classes\Skill::DeleteSkill(-1);
        $this->assertTrue($result->hasError);
    }

    //Attempt to delete a real skill

    public function testDeleteSkillSuccess() {
        $objSave = createSkill("Some Skill", $this->skillCatId, $this->adminUser);
        $skillId = $objSave->objectId;
        $result = \Classes\Skill::DeleteSkill($skillId);
        $this->assertFalse($result->hasError);
    }


    //Attempt to delete a skill that has related entries in both job_skill and job_seeker_skill tables
    //with and without using the "I'm sure" flag

    public static function createJobAddSkill($testEmail, $testJobName, $skillCatId, $adminUser) {
        $result = JobTest::createEmployerAndJob($testEmail, $testJobName);
        extract($result); //jid
        $skillObjSave = SkillTest::createSkill("Looking Up",
                $skillCatId, $adminUser);
        $skillId = $skillObjSave->objectId;
        \Classes\Job::SaveJobSkills($jid, $skillId);
        return array('jid' => $jid, 'skillId' => $skillId);
        
        //anything calling this method should also call the below at the end
        //JobTest::deleteJobTestTempRecords($testEmail);
    }

    public function testDeleteUsedSkill() {
        extract(SkillTest::createJobAddSkill($this->testEmail, $this->testJobName, $this->skillCatId, $this->adminUser));
        $deleteTestOneResult = \Classes\Skill::DeleteSkill($skillId);
        $deleteTestTwoResult = \Classes\Skill::DeleteSkill($skillId, true);
        $jobTestCleanupResult = JobTest::deleteJobTestTempRecords($this->testEmail);
        if ($jobTestCleanupResult[0] == false) {
            $this->assertTrue(false, $jobTestCleanupResult[1]);
        }
        $this->assertTrue($deleteTestOneResult->has_error);
        $this->assertFalse($deleteTestTwoResult->has_error);
    }

    //

    //static function GetSkillsBySkillCategory($skillCategoryId)
    //Try with both legit and illegit skillCategoryIds

    public function testGetSkillsBySkillCategoryFailure() {
        $result = \Classes\Skills::GetSkillsBySkillCategory(-1);
        $this->assertTrue(count($result) == 0);
    }

    public function testGetSkillsBySkillCategorySuccess() {
        SkillTest::createSkill("Some test skill", $this->skillCatId, $this->adminUser);
        $result = \Classes\Skills::GetSkillsBySkillCategory($this->skillCatId);
        $this->assertTrue(count($result) == 1);
    }


    //static function GetSkillsByJob($jobId)
    public function testGetSkillsByJob() {
        extract(SkillTest::createJobAddSkill($this->testEmail, $this->testJobName, $this->skillCatId, $this->adminUser));
        //gives jid, skillId
        $skillsArr = \Classes\Skill::GetSkillsByJob($jid);
        $jobTestCleanupResult = JobTest::deleteJobTestTempRecords($this->testEmail);
        if ($jobTestCleanupResult[0] == false) {
            $this->assertTrue(false, $jobTestCleanupResult[1]);
        }
        $this->assertTrue(count($skillsArr) == 1);
        $this->assertTrue(($skillsArr[0])->skillId == $skillId);
    }


    //static function GetSkillsByJobSeeker($jobSeekerId)
    public function testGetSkillsByJobSeeker() {
        //create jobseeker
        $jsTestEmail = "I_Seek_jobs@email.com";
        extract(JobSeekerTest::createUserAndJobSeeker($jsTestEmail));
        //oid, jid, jobSeeker

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

        //remove both skills
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, "");

        //check empty array
        $finalRes = \Classes\Skill::GetSkillsByJobSeeker($jid);

        $this->assertSame(array(), $initRes);
        $this->assertSame(array(), $finalRes);
        $this->assertEquals(1, count($res1));
        $this->assertEquals(2, count($res2));

        $jsTearDownRes = JobSeekerTest::tearDownByEmail($jsTestEmail);
        if ($jsTearDownRes[0] == false) {
            $this->assertTrue(false, $jsTearDownRes[1]);
        }
    }



    //static function GetSkillExists($object)

    public function testGetSkillExists($object) {
        $dodgySkill = new \Classes\Skill();
        $dodgySkill->skillName = "Whatever";
        $dodgySkill->skillCategoryId = "-1";
        $realSkillSave = createSkill("Some Skill", $this->skillCatId, $this->adminUser);
        $realSkillId = $realSkillSave->objectId;
        $realSkillDuplicate = new \Classes\Skill();
        $realSkillDuplicate->skillName = "Some Skill";
        $realSkillDuplicate->skillCategoryId = $this->skillCatId;
        $this->assertFalse(\Classes\Skill::GetSkillExists($dodgySkill));
        $this->assertFalse(\Classes\Skill::GetSkillExists($realSkillDuplicate));
    }

    //refactored to allow external use
    public static function staticSetup($skillCatName) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql = 'insert into skill_category(SkillCategoryName) values (?)';
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $skillCatName);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_stmt_affected_rows($stmt) != 1) {
                return array(false, "Failure to insert skillCategory with (intentionally unlikely to be used in reality) name '"
                                        .$skillCatName."'");
            }
            $skillCatId = $stmt->insert_id;
            $stmt->close();
        } else {
            var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
            return array(false, "Error in database query in setUp function");
        }
        $conn->close();

        //set up test admin user
        $user = UserTest::saveNewUser($testEmail);
        $user->userType = 3;
        $user->Save();
        $adminUser = $user;
        return array(true, array('adminUser' => $adminUser, 'skillCatId' => $skillCatId));
    }

    protected function setUp(): void {
        parent::setUp();
        $setupResult = $this->staticSetup($this->testSkillCategoryName);
        if ($setupResult[0] == false) {
            $this->assertTrue(false, $setupResult[1]);
        }
        $this->adminUser = $setupResult[1]['adminUser'];
        $this->skillCatId = $setupResult[1]['skillCatId'];
    }

    
    //Refactored to static to allow use from another test class

    public static function staticTearDown($skillCategoryName, $adminUser) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        //Delete all skills in test skill category, and category itself
        
        foreach (array("delete from skill where skillCategoryId in (select skillcategoryid from skill_category where skillcategoryname = ?",
                        "delete from skill_category where skillCategoryName = ?") as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $skillCategoryName);
                //var_dump("Cleaning up - deleting user with id ".$oid);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_stmt_affected_rows($stmt) != 1) {
                    return array(false, "Failure to delete skillCategory with (intentionally unlikely to be used in reality) name '"
                                            .$skillCategoryName."'");
                }
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                return array(false, "Error in database query in tearDown function");
            }
        }

        //delete the adminUser created to create the skills

        $sql = "delete from user where email = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $adminUser->email);
            //var_dump("Cleaning up - deleting user with id ".$oid);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_stmt_affected_rows($stmt) != 1) {
                return array(false, "Failure to delete adminUser with email '"
                                        .$adminUser->email."'");
            }
            $stmt->close();
        } else {
            var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
            return array(false, "Error in database query in tearDown function");
        }

        $conn->close();
        return array(true, "");
    }

    protected function tearDown(): void {
        $tearDownResult = $this->staticTearDown($this->skillCategoryName, $this->adminUser);
        //Actaully do need the if clause - without it the assert function always runs, and it seems in PHPUnit no code after
        //an assert statement runs, even if the assert statement passes
        if ($tearDownResult[0] == false) {
            $this->assertTrue(false, $tearDownResult[1]);
        }
        parent::tearDown();
    }
    

}

?>