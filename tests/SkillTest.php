<?php

use PHPUnit\Framework\TestCase;


final class SkillTest extends TestCase {


    //Should be something that will never be used in reality
    private $testSkillCategoryName;

    public function testConstructorZero() {
        $skill = new \Classes\Skill(0);
        $this->assertSame(0, $skill->skillId);
    }

    //Need tests for (constructor, save($user) method)
    //Attempt to insert new skill to category that doesn't exist
    //Attempt to insert new skill to valid category
    //Attempt to rename a skill
    //Attempt to change a skills' category

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
        //Unlikely to be in actual use
        $this->testSkillCategoryName = "!@#!@$%asdfd    1234~!@#";
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        //Delete all skills in test skill category



        //Delete test skill category
        $sql = "delete from skill_category where skillCategoryName = ?";
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
        $conn->close();
        parent::tearDown();
    }
    

}

?>