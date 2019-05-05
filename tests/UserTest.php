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
 * will not be executed (unless that code is other $this->assert statements).
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
 * by using var_dump.
 * 
 * 
 * Autoloader (which is not part of PHPUnit apparently but is provider by Composer,
 * the PHP package management framework that PHPUnit was instaled through) needs to
 * be updated each time a new class is introduced to the class hierarchy in order
 * for PHPUnit to be aware of it and able to use it in unit tests.
 * 
 * 
 * Found the composer binary (well, batch file) and added to prodUnitTests batch file
 * in wwwroot directory. Problem of not being able to use PHPUnit for both our version
 * of the site that exists in that directory AND the version that exists in dev (as
 * the autloader can only track one of the two class hierarchies at any given time)
 * remains at this stage.
 * 
 * UDPATE: Standalone version of PHPUnit (i.e. not installed via composer) appears to
 * work when the optional (apparently not so optional) phpunit.xml file is added and
 * a custom bootstrap.php file is created.
 * 
 * Syntax is php phpunit.phar --debug tests in top-level directory of dev site
 * to run all tests in classes in the tests/ subdirectory.
 * 
 */

final class UserTest extends TestCase {

    private $idsToDelete = array();
    private $testEmails = array(1 => "unit@tester.com", 2 => "reallyunittesting@test.com");
    private $testPassword = "notavery1good+password";

    public function testConstructorUserIdZeroGivenZero() {
        $this->assertEquals(0, (new \Classes\User(0))->userId);
    }

    public function testConstructorUserIdZeroGivenNegative() {
        $this->assertEquals(0, (new \Classes\User(-1))->userId);
    }

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

    public function testGetEmailExists() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertSame(true, $user->GetEmailExists($user->email, 0));
    }

    public function testGetEmailExistsFailure() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertSame(false, $user->GetEmailExists(NULL, 0));
    }

    public function testGetUserByVerifyCode() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = false;
        $user->active = true;
        $user->Save();
        $this->assertEquals($user, \Classes\User::GetUserByVerifyCode($user->verifyCode));
    }

    public function testGetUserByVerifyCodeFailure() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $user->verified = true;
        $user->Save();
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByVerifyCode($user->verifyCode));
    }

    public function testGetUserByEmailAddress() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->Save();
        $this->assertEquals($user, \Classes\User::GetUserByEmailAddress($user->email));
    }

    public function testGetUserByEmailAddressFailure() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByEmailAddress($user->email));
    }

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

    public function testGetUserByResetCodeFailureNoMatch() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByResetCode($user->resetCode));
    }

    public function testGetUserByResetCodeFailurecodeLength() {
        $oid = $this->saveNewUser($this->testEmails[1]);
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->Save();
        $this->assertEquals(null, \Classes\User::GetUserByResetCode("123"));
    }

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

    protected function setUp(): void {
        parent::setUp();
        $this->idsToDelete = array();
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