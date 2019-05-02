<?php

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


    protected function setUp(): void {
        parent::setUp();
        //Set up test skill category
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql = 'insert into skill_category(SkillCategoryName) values (?)';
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $this->testSkillCategoryName);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_stmt_affected_rows($stmt) != 1) {
                $this->assertTrue(false, "Failure to insert skillCategory with (intentionally unlikely to be used in reality) name '"
                                        .$this->testSkillCategoryName."'");
            }
            $this->skillCatId = $stmt->insert_id;
            $stmt->close();
        } else {
            var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
            $this->assertTrue(false, "Error in database query in setUp function");
        }
        $conn->close();

        //set up test admin user
        $user = UserTest::saveNewUser($testEmail);
        $user->userType = 3;
        $user->Save();
        $this->adminUser = $user;
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        //Delete all skills in test skill category, and category itself
        
        foreach (array("delete from skill where skillCategoryId in (select skillcategoryid from skill_category where skillcategoryname = ?",
                        "delete from skill_category where skillCategoryName = ?") as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $this->testSkillCategoryName);
                //var_dump("Cleaning up - deleting user with id ".$oid);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_stmt_affected_rows($stmt) != 1) {
                    $this->assertTrue(false, "Failure to delete skillCategory with (intentionally unlikely to be used in reality) name '"
                                            .$this->testSkillCategoryName."'");
                }
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
        }
        $conn->close();
        parent::tearDown();
    }
    

}

?>