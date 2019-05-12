<?php

/** 
 * Class to test functionality of User Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;



/**
 * 
 * List of gotchas for other prospective unit test authors:
 * 
 * Any code after the first $this->assertXYZ statement in a test function,
 * will not be executed (unless that code is other $this->assert statements, or
 * the assert function is wrapped inside an if statement with a condition that
 * does not evaluate to true).
 * 
 * UPDATE: The above does not seem to be true, or at least not in all cases, as
 * JobSeekerTest::testGetJobMatchesByJobSeekerExhaustive shows execution continuing
 * after the assertion.
 * 
 * 
 * If you have setUp() or tearDown() functions that have a different method
 * signature than those in this file (INCLUDING the return type hint ': void')
 * or that don't call the corresponding parent::setUp() or parent::tearDown()
 * before and after the rest of the code in that function, PHPUnit will silently
 * fail. No error message or output of any kind.
 * 
 * 
 * echo doesn't work inside of a PHPUnit unit test, as stdout has been redirected.
 * It seems like the only way to write terminal output when inside a unit test is
 * by using var_dump (the --debug flag may also need to be passed to phpunit in order
 * to see this output), or by intentionally causing the test to fail with something like
 * $this->assertTrue(false, print_r($varIWantToInspect, true));
 * 
 * Initially attempted to use the version of PHPUnit installed through composer, but
 * ran into a number of difficult to debug issues when attempting to use that version of
 * PHPUnit with multiple sites (development and production). Ended up switching to standalone
 * phpunit.phar file available from phpunit.de
 * 
 * UDPATE: Standalone version of PHPUnit (i.e. not installed via composer) appears to
 * work when the optional (apparently not so optional) phpunit.xml file is added and
 * a custom bootstrap.php file is created.
 * 
 * FURTHER UPDATE: phpunit.xml can be ignored if the --bootstrap flag is used to indicate
 * the location of the bootstrap file (in which a custom autoload function is passed to
 * spl_autoload_register())
 * 
 * Syntax is php phpunit.phar --debug tests in top-level directory of dev site
 * to run all tests in classes in the tests/ subdirectory.
 * 
 * A further gotcha encountered later is that it seems that tests that are
 * marked as incomplete (via $this->markTestIncomplete()) result in the setUp
 * function getting executed, but the tearDown function not getting execute.
 * This can obviously cause problems if you are created temporary records in the
 * database in the setUp function under the assumption that they will always be
 * removed regardless of test result.
 * 
 * Also, creation of helper functions can be quite useful, but make sure to NOT
 * name them with the word 'test' at the start, as the unit testing framework will
 * treat them as a test and you will most likely end up with a ArgumentCountError
 * (unless the helper function takes no parameters).
 * 
 * Also, it is highly recommended to NOT halt the unit test script with CTRL-C if
 * you have any tests that write records into the database in setUp expecting that they
 * will be removed in tearDown - CTRL-C may very well break execution after setUp
 * but before tearDown leaving the database in an inconsistent state
 * 
 */

final class UserTest extends TestCase {

    private $idsToDelete = array();
    private $testEmails = array(1 => "unit@tester.com", 2 => "reallyunittesting@test.com");
    private $testPassword = "notavery1good+password";


    //Simple tests to ensure constructor fails when it should
    public function testConstructorUserIdZeroGivenZero() {
        $this->assertEquals(0, (new \Classes\User(0))->userId);
    }

    public function testConstructorUserIdZeroGivenNegative() {
        $this->assertEquals(0, (new \Classes\User(-1))->userId);
    }

    
    //Make sure attempt to save record with email that already belongs to another record fails
    public function testSaveEmailExists() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $this->idsToDelete[] = $oid;
        $user = new \Classes\User($oid);
        $newUser = new \Classes\User();
        $newUser->email = $user->email;
        $objSave = $newUser->Save();
        $this->assertEquals('Email address '.$user->email.' exists in system', $objSave->errorMessage);
        $this->assertEquals(true, $objSave->hasError);
    }

    //helper function to create user with indicated email address.
    //UPDATE: Expanded to allow userType to be indicated also (so as to allow creation of
    //admin-type records in user table - creation of skills requires this (but does not
    //actually require a record in the admin_staff table))
    public static function saveNewUser($email, $userType = 1, $errorExpected = false) {
        $user = new \Classes\User(0);
        $user->userType = $userType;
        $user->email = $email;
        $user->active = 1;
        $user->password = NULL;
        $user->verified = 0;
        $user->enteredDetails = 0;
        $user->resetCode = NULL;
        $objSave = $user->Save();
        //var_dump($objSave);
        if ($objSave->hasError) {
            var_dump($objSave->errorMessage);
//            $existingUser = \Classes\User::GetUserByEmailAddress($email);
//            var_dump("Existing user with id: ".$existingUser->userId);
        }
        return $objSave->objectId;
    }

    
    //Test that changing a user's details (email) works
    public function testUpdateUser() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $user->email = $this->testEmails[2];
        $objSave = $user->Save();
        $oid2 = $objSave->objectId;
        $user2 = new \Classes\User($oid2);
        $this->idsToDelete[] = $oid;
        $this->idsToDelete[] = $oid2;

        $this->assertEquals($oid, $oid2);
        $this->assertEquals($this->testEmails[2], $user2->email);
    }

    
    //Test retrieval of data via creation of a new user
    public function testSaveGetUser() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        //var_dump("Testing with oid ".$oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals($this->testEmails[1], $user->email);
        $this->assertEquals(NULL, $user->password);
        $this->assertEquals(1, $user->userType);
        $this->assertEquals(1, $user->active);
        $this->assertEquals(0, $user->verified);
        $this->assertEquals(0, $user->enteredDetails);
        $this->assertEquals(NULL, $user->resetCode);
    }

    
    //test ability to check if email exists in a user record
    public function testGetEmailExists() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertTrue($user->GetEmailExists($user->email, 0));
    }


    //GetEmailExists should return false if an invalid value for email is passed to it
    public function testGetEmailExistsFalse() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertFalse($user->GetEmailExists(NULL, 0));
    }

    //GetEmailExists should return false if an email and matching userId are passed to it
    public function testGetEmailExistsFalseSameUser() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertFalse($user->GetEmailExists($user->email, $user->userId));
    }


    //Test ability to retrieve real user record by verifyCode
    public function testGetUserByVerifyCode() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->Save();
        $this->assertEquals($user, \Classes\User::GetUserByVerifyCode($user->verifyCode));
    }


    //Test failure of above method
    public function testGetUserByVerifyCodeFailure() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $user->verified = true;
        $user->Save();
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByVerifyCode($user->verifyCode));
    }

    //Test failure of above method
    public function testGetUserByVerifyCodeWrongCode() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $user->Save();
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByVerifyCode(null));
    }


    //Test ability to get user record from their email address
    public function testGetUserByEmailAddress() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->Save();
        $this->assertEquals($user, \Classes\User::GetUserByEmailAddress($user->email));
    }


    //Test failure of above method
    public function testGetUserByEmailAddressFailure() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByEmailAddress($user->email));
    }

    //Test failure of above method when given invalid email address
    public function testGetUserByEmailWrongEmail() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $user->Save();
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByEmailAddress(null));
    }

    //GetUnverifiedUserByEmailAddress
    public function testGetUnverifiedUserByEmail() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals($user, \Classes\User::GetUnverifiedUserByEmailAddress($user->email));
    }

    //test ability to get user record from their reset code
    public function testGetUserByResetCode() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->resetCode = \Utilities\Common::GetGuid();
        $user->Save();
        $this->assertEquals($user, \Classes\User::GetUserByResetCode($user->resetCode));
    }

    //test failure of above method
    public function testGetUserByResetCodeFailureNoMatch() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByResetCode($user->resetCode));
    }



    //test failure of above due to code being wrong length
    public function testGetUserByResetCodeFailureCodeLength() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->Save();
        $this->assertEquals(null, \Classes\User::GetUserByResetCode("123"));
    }


    //test ability to log in as legimate user
    public function testGetUserLogin() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->password = password_hash($this->testPassword, PASSWORD_BCRYPT);
        $user->Save();
        $this->assertEquals($user, \Classes\User::GetUserLogin($user->email, $this->testPassword));
    }


    //test failure of login if password wrong
    public function testGetUserLoginFailInvalidPassword() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->password = password_hash($this->testPassword, PASSWORD_BCRYPT);
        $user->Save();
        $this->assertSame(null, \Classes\User::GetUserLogin($user->email, "Wrong password"));
    }

    //test failure of login if email wrong
    public function testGetUserLoginFailWrongEmail() {
        $this->assertSame(null, \Classes\User::GetUserLogin(null, "Wrong password"));
    }

    //setUp function no longer needed as all records created in this class use the same emails
    protected function setUp(): void {
        parent::setUp();
    }


    //Refactored to allow use from other test classes
    public static function staticTearDown($testEmails) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($testEmails as $testEmail) {
            $sql = "delete from user where email = ?";
            if ($stmt = $conn->prepare($sql)) {
                //var_dump($stmt);
                $stmt->bind_param("s", $testEmail);
                //var_dump("Cleaning up - deleting user with id ".$oid);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                //var_dump
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                return array(false, "Error in database query in tearDown function");
            }
        }
        $conn->close();
        return array(true, "");
    }

    //tearDown function. Original contents deleted as 
    protected function tearDown(): void {
        $this->staticTearDown($this->testEmails);

        /**
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        //var_dump($conn);
        //var_dump("In cleanup with oid ".$oid);
        foreach ($this->idsToDelete as $idd) {
            $sql = 'delete from user where UserId = ?';
            if ($stmt = $conn->prepare($sql)) {
                //var_dump($stmt);
                $stmt->bind_param("i", $idd);
                //var_dump("Cleaning up - deleting user with id ".$oid);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                //var_dump
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
        }
        foreach ($this->testEmails as $testEmail) {
            $sql = 'delete from user where email = ?';
            if ($stmt = $conn->prepare($sql)) {
                //var_dump($stmt);
                $stmt->bind_param("s", $testEmail);
                //var_dump("Cleaning up - deleting user with id ".$oid);
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $stmt->close();
            } else {
                //var_dump
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
        }
        $sql = 'delete from user where email is null';
        if ($stmt = $conn->prepare($sql)) {
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            $stmt->close();
        } else {
            //var_dump
            var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
            $this->assertTrue(false, "Error in database query in tearDown function");
        }
        $conn->close();
        */
        parent::tearDown();
    }


    /**
    private function cleanup($oid) {
        
        //delete record created by test process
        //Started out life as a tearDown() process - before I read the manual properly

        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        //var_dump($conn);
        //var_dump("In cleanup with oid ".$oid);
        $sql = 'delete from user where UserId = ?';
        if ($stmt = $conn->prepare($sql)) {
            //var_dump($stmt);
            $stmt->bind_param("i", $oid);
            //var_dump("Cleaning up - deleting user with id ".$oid);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
            $stmt->close();
        } else {
            var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
        }
        $conn->close();
    }

    */

}



?>