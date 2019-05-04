<?php


/** 
 * Class to test functionality of Employer Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class EmployerTest extends TestCase {

    private $uidsToDelete;
    private $testEmail = "someEmployerEmail@somewhere.com";
    //private $eidsToDelete;

    public function testConstructor() {
        $employer = new \Classes\Employer(0);
        $this->assertSame(0, $employer->employerId);
    }

    public function testSaveUser() {
        $result = $this->createUserAndEmployer($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false,"Result of trying to save user was null");
        }
        //var_dump($result);
        extract($result);
        $this->uidsToDelete[] = $oid;
        //$this->eidsToDelete[] = $eid;
        $this->assertEquals($employer, new \Classes\Employer($eid));
    }

    public static function createUserAndEmployer($email) {
        $oid = UserTest::saveNewUser($email);
        if ($oid == null) {
            var_dump("ERROR: failure to create new user record with email ".$email);
            return null; //better for stuff to fail here than make a mess
                        //creating records in employer with null userId values
                        //that won't be deleted by cleanup
        }
        $employer = new \Classes\Employer(0);
        $employer->userId = $oid;
        $oSave = $employer->save();
        $eid = $oSave->objectId;
        $employer->employerId = $eid;
        return array('oid' => $oid, 'eid' => $eid, 'employer' => $employer);
    }

    public function testEditEmployer() {
        $result = $this->createUserAndEmployer($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false);
        }
        extract($result);
        $this->uidsToDelete[] = $oid;
        $employer->firstName = "Bob";
        $oSave2 = $employer->save();
        $eid = $oSave2->objectId;
        $this->assertSame($employer->firstName, (new \Classes\Employer($eid))->firstName);
    }

    public function testGetEmployerByUserIdFailure() {
        $this->assertSame(null, \Classes\Employer::GetEmployerByUserId(0));
    }

    public function testGetEmployerByUserId() {
        $result = $this->createUserAndEmployer($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false);
        }
        extract($result);
        $this->uidsToDelete[] = $oid;
        $this->assertEquals(\Classes\Employer::GetEmployerByUserId($oid), $employer);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
       // $this->eidsToDelete = array();
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        //var_dump($conn);
        //var_dump("In cleanup with oid ".$oid);
        foreach ($this->uidsToDelete as $idd) {
            foreach (array('delete from employer where userid = ?', 'delete from user where UserId = ?') as $sql) {
            //$sql = 'delete from employer where userid = ?';
                if ($stmt = $conn->prepare($sql)) {
                    //var_dump($stmt);
                    $stmt->bind_param("i", $idd);
                    //var_dump("Cleaning up - deleting user with id ".$oid);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                    $this->assertTrue(false, "Error in database query in tearDown function");
                }
            }
            $sql = 'delete from employer where userid in (Select userid from user where email = ?)';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $this->testEmail);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
            $sql = 'delete from employer where userid is null';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                //var_dump
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
        }
        $conn->close();
        parent::tearDown();
    }


}

?>