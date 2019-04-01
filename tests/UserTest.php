<?php

use PHPUnit\Framework\TestCase;

/**
 * 
 * List of gotchas for prospective unit test authors:
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
 */

final class UserTest extends TestCase {

    private $idsToDelete = array();

    public function testConstructorUserIdZeroGivenZero() {
        $this->assertEquals(0, (new \Classes\User(0))->userId);
    }

    public function testConstructorUserIdZeroGivenNegative() {
        $this->assertEquals(0, (new \Classes\User(-1))->userId);
    }

    public function testConstructorDBServerInaccessible() {
        //not yet implemented
        $this->markTestIncomplete();
    }

    public function testSaveEmailExists() {
        //obviously needs at least two records in the database for this to work
        $user = new \Classes\User(2);
        $user->userId = 1;
        $objSave = $user->Save();
        $this->assertEquals('Email address exists in system', $objSave->errorMessage);
        $this->assertEquals(true, $objSave->hasError);
    }

    private function saveNewUser() {
        $user = new \Classes\User(0);
        $user->userType = 1;
        $user->email = "unit@tester.com";
        $user->active = 1;
        $user->password = NULL;
        $user->verified = 0;
        $user->enteredDetails = 0;
        $user->resetCode = NULL;
        $objSave = $user->Save();
        //var_dump($objSave);

        //Actually can't use the below as the tearDown function is run per-test, not per test class as I'd thought
        //$idsToDelete[] = $objSave->objectId;

        return $objSave->objectId;
    }

    public function testUpdateUser() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $user->email = "reallyunittesting@test.com";
        $objSave = $user->Save();
        $oid2 = $objSave->objectId;
        $user2 = new \Classes\User($oid2);
        //Cleanup DB BEFORE assertions as it seems code after them doesn't get executed (according to hacky var_dump testing)
        //$this->cleanup($oid);
        //$this->cleanup($oid2);

        $this->idsToDelete[] = $oid;
        $this->idsToDelete[] = $oid2;

        $this->assertEquals($oid, $oid2);
        $this->assertEquals("reallyunittesting@test.com", $user2->email);
    }

    public function testSaveGetUser() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        //var_dump("Testing with oid ".$oid);
        //Cleanup DB BEFORE assertions as it seems code after them doesn't get executed (according to hacky var_dump testing)
        //$this->cleanup($oid);

        $this->idsToDelete[] = $oid;

        $this->assertEquals('unit@tester.com', $user->email);
        $this->assertEquals(NULL, $user->password);
        $this->assertEquals(1, $user->userType);
        $this->assertEquals(1, $user->active);
        $this->assertEquals(0, $user->verified);
        $this->assertEquals(0, $user->enteredDetails);
        $this->assertEquals(NULL, $user->resetCode);
    }

    public function testGetEmailExists() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertSame(true, $user->GetEmailExists($user->email, 0));
    }

    public function testGetEmailExistsFailure() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertSame(false, $user->GetEmailExists(NULL, 0));
    }

    public function testGetUserByVerifyCode() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = false;
        $user->active = true;
        $user->save();
        $this->assertEquals($user, \Classes\User::GetUserByVerifyCode($user->verifyCode));
    }

    public function testGetUserByVerifyCodeFailure() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $user->verified = true;
        $user->save();
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByVerifyCode($user->verifyCode));
    }

    public function testGetUserByEmailAddress() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->save();
        $this->assertEquals($user, \Classes\User::GetUserByEmailAddress($user->email));
    }

    public function testGetUserByEmailAddressFailure() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByEmailAddress($user->email));
    }

    public function testGetUserByResetCode() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->resetCode = \Utilities\Common::GetGuid();
        $user->save();
        $this->assertEquals($user, \Classes\User::GetUserByResetCode($user->resetCode));
    }

    public function testGetUserByResetCodeFailureNoMatch() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $this->assertEquals(null, \Classes\User::GetUserByResetCode($user->resetCode));
    }

    public function testGetUserByResetCodeFailurecodeLength() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->save();
        $this->assertEquals(null, \Classes\User::GetUserByResetCode("123"));
    }

    public function testGetUserLogin() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->password = password_hash("notavery1good+password", PASSWORD_BCRYPT);
        $user->save();
        $this->assertEquals($user, \Classes\User::GetUserLogin($user->email, "notavery1good+password"));
    }

    public function testGetUserLoginFailInvalidPassword() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->idsToDelete[] = $oid;
        $user->verified = true;
        $user->active = true;
        $user->password = password_hash("notavery1good+password", PASSWORD_BCRYPT);
        $user->save();
        $this->assertSame(null, \Classes\User::GetUserLogin($user->email, "notthepassword"));
    }

    protected function setUp(): void {
        parent::setUp();
        $this->idsToDelete = array();
    }

    protected function tearDown(): void {
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
            }
        }
        $conn->close();
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