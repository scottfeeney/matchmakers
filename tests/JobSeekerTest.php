<?php

use PHPUnit\Framework\TestCase;


final class JobSeekerTest extends TestCase {

    private $uidsToDelete;

    public function testConstructor() {
        $jobSeeker = new \Classes\JobSeeker(0);
        $this->assertSame(0, $jobSeeker->jobSeekerId);
    }

    public function testSaveUser() {
        extract($this->createUserAndJobSeeker());
        $this->assertEquals($jobSeeker, new \Classes\JobSeeker($jid));
    }

    /**
     * Helper function as multiple tests will want to work on a record already in the database
     */

    private function createUserAndJobSeeker() {
        $oid = UserTest::saveNewUser();
        $this->uidsToDelete[] = $oid;
        $jobSeeker = new \Classes\JobSeeker(0);
        $jobSeeker->userId = $oid;
        $oSave = $jobSeeker->save();
        $jid = $oSave->objectId;
        $jobSeeker->jobSeekerId = $jid;
        return array('oid' => $oid, 'jid' => $jid, 'jobSeeker' => $jobSeeker);
    }

    public function testEditUser() {
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
        extract($this->createUserAndJobSeeker());
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
                }
            }
        }
        $conn->close();
        parent::tearDown();
    }


}

?>