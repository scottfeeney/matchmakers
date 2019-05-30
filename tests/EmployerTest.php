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


    //Simplest test possible
    public function testConstructor() {
        $employer = new \Classes\Employer(0);
        $this->assertSame(0, $employer->employerId);
    }

    //Test ability to save user (i.e. test helper class below)
    public function testSaveUser() {
        $result = $this->createUserAndEmployer($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false,"Result of trying to save user was null");
        }
        extract($result);
        $this->uidsToDelete[] = $oid;
        $this->assertEquals($employer, new \Classes\Employer($eid));
    }


    //Helper class called from a number of other test classes (as well as this one)
    public static function createUserAndEmployer($email, $verified = 0) {
        $oid = UserTest::saveNewUser($email, 1, false, $verified);
        if ($oid == null) {
            var_dump("ERROR: failure to create new user record with email ".$email);
            return null; //better for stuff to fail here than make a mess
                        //creating records in employer with null userId values
                        //that won't be deleted by cleanup
        }
        $employer = new \Classes\Employer(0);
        $employer->userId = $oid;
        $oSave = $employer->Save();
        $eid = $oSave->objectId;
        $employer->employerId = $eid;
        return array('oid' => $oid, 'eid' => $eid, 'employer' => $employer);
    }

    
    //Test persistence of altered fields after object is saved
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


    //Test that indicated method correctly fails when given invalid input
    public function testGetEmployerByUserIdFailure() {
        $this->assertSame(null, \Classes\Employer::GetEmployerByUserId(0));
    }


    //Test the same method works when given legitimate input
    public function testGetEmployerByUserId() {
        $result = $this->createUserAndEmployer($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false);
        }
        extract($result);
        $this->uidsToDelete[] = $oid;
        $this->assertEquals(\Classes\Employer::GetEmployerByUserId($oid), $employer);
    }

    //Test method correctly fails when given id for different user type
    public function testGetEmployerByUserIdWrongType() {
        $testNonEmployerUserId = UserTest::saveNewUser($this->testEmail,3);
        if ($testNonEmployerUserId == null) {
            $this->assertTrue(false, "Could not set up non-employer user");
        }
        $this->uidsToDelete[] = $testNonEmployerUserId;
        $this->assertSame(null, \Classes\Employer::GetEmployerByUserId($testNonEmployerUserId));
    }


    //setUp function to be automatically executed before any tests
    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
    }

    //created to provide teardown functionality to other classes
    public static function tearDownByEmail($email) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach (array( "delete from employer where userid in (select userId from user where email = ?)",
                        "delete from user where email = ?") as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $conn->close();
                return array(false, "Error in database query in tearDown function");
            }
        }
        $conn->close();
        return array(true, "");
    }

    //function to remove temporary test data from database
    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        foreach (array('delete from employer where userid in (select userid from user where email = ?)', 'delete from user where email = ?') as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $this->testEmail);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function:".PHP_EOL.$sql);
            }
        }
        $conn->close();
        parent::tearDown();
    }
}

?>