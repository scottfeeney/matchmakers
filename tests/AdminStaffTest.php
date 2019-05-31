<?php

/** 
 * Class to test functionality of AdminStaff Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class AdminStaffTest extends TestCase {


    private $adminStaffUserRecord;
    private $adminStaffAdminStaffRecord;
    private $testEmail = "anAdminPersonBeingTested@email321.com";
    private $testNonAdminEmail = "unit@tester.com";
    private $testNonAdminUserId;


    //Simple constructor tests
    public function testConstructor() {
        $adminStaff = new \Classes\AdminStaff(0);
        $this->assertSame(0, $adminStaff->adminStaffId);
    }

    public function testConstructorNegative() {
        $adminStaff = new \Classes\AdminStaff(-1);
        $this->assertSame(null, $adminStaff->adminStaffId);
    }

    public function testConstructorSuccess() {
        $this->assertTrue((new \Classes\AdminStaff($this->adminStaffAdminStaffRecord->adminStaffId))->adminStaffId != 0);
        $this->assertTrue((new \Classes\AdminStaff($this->adminStaffAdminStaffRecord->adminStaffId))->adminStaffId == $this->adminStaffAdminStaffRecord->adminStaffId);
        $this->assertEquals((new \Classes\AdminStaff($this->adminStaffAdminStaffRecord->adminStaffId)), $this->adminStaffAdminStaffRecord);
        $this->assertTrue((new \Classes\AdminStaff($this->adminStaffAdminStaffRecord->adminStaffId))->userId != null);
    }


    //Test of indicated method for failure and success
    public function testGetAdminStaffByUserIdFailure() {
        $this->assertSame(null, \Classes\AdminStaff::GetAdminStaffByUserId(0));
    }

    public function testGetAdminStaffByUserIdSuccess() {
        $this->assertEquals((\Classes\AdminStaff::GetAdminStaffByUserId($this->adminStaffUserRecord->userId))->userId, $this->adminStaffUserRecord->userId);
    }

    public function testGetAdminStaffByUserIdNonAdmin() {
        $this->assertSame(null, \Classes\AdminStaff::GetAdminStaffByUserId($this->testNonAdminUserId));
    }


    //setUp and tearDown functions utilizing setUp and tearDown functionality from other classes
    //(APITest and SkillTest) as the setUp and tearDown functionality for this class was not properly 
    //completed before the same functionality was implemented elsewhere
    protected function setUp(): void {
        parent::setUp();

        //create record in user table
        $this->adminStaffUserRecord = AdminStaffTest::staticSetupAdminUser($this->testEmail)[1]['adminUser'];

        //create record in adminStaff table
        $adminStaffSetupRes = AdminStaffTest::setupAdminStaffRecord($this->adminStaffUserRecord->userId);
        if ($adminStaffSetupRes[0] != false) {
            $this->adminStaffAdminStaffRecord = $adminStaffSetupRes[1]['adminStaff'];
        }
        $this->testNonAdminUserId = UserTest::saveNewUser($this->testNonAdminEmail);
        if ($this->testNonAdminUserId == null) {
            $this->assertTrue(false, "Could not set up non-Admin user in setUp function");
        }
    }

    public static function staticSetupAdminUser($testEmail, $verified = 0) {
        //set up test admin user
        $user = new \Classes\User(UserTest::saveNewUser($testEmail, 3, false, 1));
        return array(true, array('adminUser' => $user));
    }

    public static function setupAdminStaffRecord($uid) {
        //In most cases would use relevant class to create record, however
        //as our code isn't designed to facilitate *creation* of admin accounts
        //we need to create these records directly
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql =  "insert into admin_staff (userid, firstname, lastname, created) "
                ."values (?, 'Bob', 'Smith', UTC_TIMESTAMP())";

        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            if ($conn->affected_rows != 1) {
                $errMsg = $conn->errno.': '.$conn->error;
                var_dump($errMsg);
                $conn->close();
                return array(false, "Could not verify creation of record in adminstaff table for userId ".$uid.":".PHP_EOL.$errMsg);
            }
            $insertedId = $stmt->insert_id;
        } 
        else {
            $errMsg = $conn->errno.': '.$conn->error;
            var_dump($errMsg);
            $conn->close();
            return array(false, "Could not create record in adminStaff table for userId ".$uid.":".PHP_EOL.$errMsg);
        }
        $conn->close();
        return array(true, array('adminStaff' => new \Classes\AdminStaff($insertedId)));
    }

    protected function tearDown(): void {
        AdminStaffTest::tearDownAdminStaffRecord($this->adminStaffUserRecord->userId);
        AdminStaffTest::tearDownAdminByEmail($this->adminStaffUserRecord->email);
        UserTest::staticTearDown(array($this->testNonAdminEmail));
        parent::tearDown();
    }

    //Refactored to static to allow use from another test class
    public static function tearDownAdminByEmail($email) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql = "delete from user where email = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_stmt_affected_rows($stmt) != 1) {
                $stmt->close();
                $conn->close();
                var_dump("Failure to delete adminUser with email '".$email."'");
                return array(false, "Failure to delete adminUser with email '"
                                        .$email."'");
            }
            $stmt->close();
        } else {
            $errorMessage = $conn->errno . ' ' . $conn->error;
            $conn->close();
            var_dump("Error in database query in tearDown function:".PHP_EOL.$errorMessage);
            return array(false, "Error in database query in tearDown function:".PHP_EOL.$errorMessage);
        }        
        $conn->close();
        return array(true, "");
    }


    public static function tearDownAdminStaffRecord($uid) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        $sql =  "delete from admin_staff where userId = ?";

        
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            if ($conn->affected_rows != 1) {
                $conn->close();
                return array('false', "Could not verify deletion of record in adminstaff table for userId ".$uid);
            }
        } 
        else {
            $conn->close();
            return array('false', "Could not delete record in adminStaff table for userId ".$uid);
        }
        $conn->close();
        return array(true, '');
    }

}

?>