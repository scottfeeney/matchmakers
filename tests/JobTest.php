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

    public function testNewJob() {
        $result = EmployerTest::createUserAndEmployer($this->testEmail);
        if ($result == null) {
            $this->assertFalse(true, "Failure to create new user record");
        }
        extract($result); //$oid, $eid, $employer (employer obj)
        $this->uidsToDelete[] = $oid;
        $job = new \Classes\Job();
        $categoryId = mt_rand(1,5);
        $retval = $this->createJob($eid, $categoryId, $this->testJobName);
        //var_dump($retval);
        extract($retval);
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


    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
        $this->jidsToDelete = array();
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        //var_dump($conn);
        //var_dump("In cleanup with oid ".$oid);

        foreach ($this->jidsToDelete as $jid) {
            $sql = "delete from job where jobid = ?";
            if ($stmt = $conn->prepare($sql)) {
                var_dump("Should now delete job with id ".$jid);
                $stmt->bind_param("i", $jid);
                //var_dump("Cleaning up - deleting user with id ".$oid);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_stmt_affected_rows($stmt) != 1) {
                    $this->assertTrue(false, "Could not delete test job from database");
                }
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
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