<?php

use PHPUnit\Framework\TestCase;


final class JobSeekerTest extends TestCase {

    private $uidsToDelete;
    private $testEmail = "someJobSeekerEmail@somewhere.com";

    public function testConstructor() {
        $jobSeeker = new \Classes\JobSeeker(0);
        $this->assertSame(0, $jobSeeker->jobSeekerId);
    }

    public function testSaveUser() {
        if ($this->createUserAndJobSeeker() == null) {
            $this->assertTrue(false);
        }
        extract($this->createUserAndJobSeeker());
        $this->assertEquals($jobSeeker, new \Classes\JobSeeker($jid));
    }

    /**
     * Helper function as multiple tests will want to work on a record already in the database
     */

    private function createUserAndJobSeeker() {
        $oid = UserTest::saveNewUser($this->testEmail);
        $this->uidsToDelete[] = $oid;
        $jobSeeker = new \Classes\JobSeeker(0);
        $jobSeeker->userId = $oid;
        $oSave = $jobSeeker->save();
        $jid = $oSave->objectId;
        $jobSeeker->jobSeekerId = $jid;
        return array('oid' => $oid, 'jid' => $jid, 'jobSeeker' => $jobSeeker);
    }

    public function testEditUser() {
        if ($this->createUserAndJobSeeker() == null) {
            $this->assertTrue(false);
        }
        extract($this->createUserAndJobSeeker());
        $jobSeeker->firstName = "Bob";
        $oSave2 = $jobSeeker->save();
        $jid = $oSave2->objectId;
        $this->assertSame($jobSeeker->firstName, (new \Classes\jobSeeker($jid))->firstName);
    }

    public function testGetJobSeekerByUserIdFailure() {
        $this->assertSame(null, \Classes\JobSeeker::GetJobSeekerByUserId(0));
    }

    public function testGetJobSeekerByUserId() {
        $result = $this->createUserAndJobSeeker();
        if ($result == null) {
            $this->assertTrue(false);
        }
        extract($result);
        $this->assertEquals(\Classes\jobSeeker::GetJobSeekerByUserId($oid), $jobSeeker);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
    }

    protected function tearDown(): void {
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