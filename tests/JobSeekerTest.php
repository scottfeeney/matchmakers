<?php

/** 
 * Class to test functionality of JobSeeker Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class JobSeekerTest extends TestCase {

    private $uidsToDelete;
    private $testEmail = "someJobSeekerEmail@somewhere.com";

    public function testConstructor() {
        $jobSeeker = new \Classes\JobSeeker(0);
        $this->assertSame(0, $jobSeeker->jobSeekerId);
    }

    public function testSaveUser() {
        if ($this->createUserAndJobSeeker($this->testEmail) == null) {
            $this->assertTrue(false);
        }
        extract($this->createUserAndJobSeeker($this->testEmail));
        $this->assertEquals($jobSeeker, new \Classes\JobSeeker($jid));
    }

    /**
     * Helper function as multiple tests will want to work on a record already in the database
     */

    private static function createUserAndJobSeeker($testEmail) {
        $oid = UserTest::saveNewUser($testEmail);
        $jobSeeker = new \Classes\JobSeeker(0);
        $jobSeeker->userId = $oid;
        $oSave = $jobSeeker->save();
        $jid = $oSave->objectId;
        $jobSeeker->jobSeekerId = $jid;
        return array('oid' => $oid, 'jid' => $jid, 'jobSeeker' => $jobSeeker);
    }

    public function testEditUser() {
        if ($this->createUserAndJobSeeker($this->testEmail) == null) {
            $this->assertTrue(false);
        }
        extract($this->createUserAndJobSeeker($this->testEmail));
        $jobSeeker->firstName = "Bob";
        $oSave2 = $jobSeeker->save();
        $jid = $oSave2->objectId;
        $this->assertSame($jobSeeker->firstName, (new \Classes\jobSeeker($jid))->firstName);
    }

    public function testGetJobSeekerByUserIdFailure() {
        $this->assertSame(null, \Classes\JobSeeker::GetJobSeekerByUserId(0));
    }

    public function testGetJobSeekerByUserId() {
        $result = $this->createUserAndJobSeeker($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false);
        }
        extract($result);
        $this->assertEquals(\Classes\jobSeeker::GetJobSeekerByUserId($oid), $jobSeeker);
    }


    //Remaining to be tested:
    //SaveJobSeekerSkills($jobSeekerId, $selectedSkills)
    //GetSkillsByJobSeekerString($jobSeekerId)

    public function testJobSeekerSkillsString() {

        //create new jobseeker
        extract($this->createUserAndJobSeeker($this->testEmail));
        //$oid, $jid (jobseekerid), $jobSeeker

        //create new skill
        $skillCatName = "Some Category";
        $skillSetupRes = SkillTest::staticSetup($skillCatName);
        if ($skillSetupRes[0] == false) {
            $this->assertTrue(false, $skillSetupRes[1]);
        }
        $adminUser = $skillSetupRes[1]['adminUser'];
        $skillCatId = $skillSetupRes[1]['skillCatId'];

        $skill1 = SkillTest::createSkill("SkillName", $skillCatId, $adminUser);
        $skill1Id = $skill1->objectId;

        //verify getSkillsByJobSeekerString returns blank string when no associated skills
        $preString = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);

        //add skill via savejobseeker skills
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, $skill1Id);

        //verify get returns expected string
        $post1String = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);

        //create another skill
        $skill2 = SkillTest::createSkill("SkillName2", $skillCatId, $adminUser);
        $skill2Id = $skill2->objectId;

        //add second skill
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, join(",", array($skill1Id, $skill2Id)));

        //verify get returns expected string
        $post2String = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);

        //remove both skills
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, "");

        //verify get returns blank string
        $post3String = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);

        $this->assertEquals($post3String, "");
        $this->assertEquals(join(",", array($skill1Id, $skill2Id)), $post2String);
        $this->assertEquals($skill1Id, $post1String);
        $this->assertSame($preString, "");

        $skillTearDownRes = SkillTest::staticTearDown($skillCatName, $adminUser);
        if ($skillTearDownRes[0] == false) {
            $this->assertTrue(false, $skillTearDownRes[1]);
        }
    }

    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
    }

    private static function tearDownByEmail($email) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        $sqls = array(  "delete from job_seeker_skill where jobseekerId in 
                            (select jobseekerId from job_seeker where userId in
                                (select userId from user where email = ? ) )",
                        "delete from job_seeker where userId in
                            (select userId from user where email = ? ) )",
                        "delete from user where email = ?");
        foreach ($sqls as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                return array(false, "Error in database query in jobSeeker tearDownByEmail function: ".PHP_EOL.$sql);
            }
        }
        $conn->close();
        return array(true, "");
    }

    protected function tearDown(): void {
        $byEmailResult = JobSeekerTest::tearDownByEmail($this->testEmail);
        if ($byEmailResult[0] == false) {
            $this->assertTrue(false, $byEmailResult[1]);
        }
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($this->uidsToDelete as $idd) {
            foreach (array('delete from job_seeker where userid = ?', 'delete from user where UserId = ?') as $sql) {
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $idd);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                    $this->assertTrue(false, "Error in database query in tearDown function");
                }
            }
            $sql = 'delete from job_seeker where userid in (Select userid from user where email = ?)';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $this->testEmail);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
            $sql = 'delete from job_seeker where userid is null';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
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