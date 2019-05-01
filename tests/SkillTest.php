<?php

use PHPUnit\Framework\TestCase;


final class SkillTest extends TestCase {


    //Should be something that will never be used in reality
    private $testSkillCategoryName = "!@#!@$%asdfd    1234~!@#";
    private $testEmail = "someadminuserguy$#!@123.com";
    private $adminUser;
    private $skillCatId;


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
    //Attempt to delete a skill that has related entries in both job_skill and job_seeker_skill tables
    //(should we be able to do that, really?)

    //static function GetSkillsBySkillCategory($skillCategoryId)
    //Try with both legit and illegit skillCategoryIds

    //static function GetSkillsByJob($jobId)

    //static function GetSkillsByJobSeeker($jobSeekerId)

    //static function GetSkillExists($object)


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