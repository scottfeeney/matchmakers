<?php

use PHPUnit\Framework\TestCase;



final class UserTest extends TestCase {

    //Actually can't use the below as the tearDown function is run per-test, not per test class as I'd thought
    private $idsToDelete = array();

    public function testConstructorUserIdZeroGivenZero() {
        $this->assertEquals(0, (new \Classes\User(0))->userId);
    }

    public function testConstructorUserIdZeroGivenNegative() {
        $this->assertEquals(0, (new \Classes\User(-1))->userId);
    }

    public function testConstructorDBServerInaccessible() {

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
        $user->verifyCode = '65630340-1718-4A7E-8EE3-39DC3D448ED0';
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
        $this->cleanup($oid);
        $this->cleanup($oid2);
        $this->assertEquals($oid, $oid2);
        $this->assertEquals("reallyunittesting@test.com", $user2->email);
    }

    public function testSaveGetUser() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        //var_dump("Testing with oid ".$oid);
        //Cleanup DB BEFORE assertions as it seems code after them doesn't get executed (according to hacky var_dump testing)
        $this->cleanup($oid);
        $this->assertEquals('unit@tester.com', $user->email);
        $this->assertEquals(NULL, $user->password);
        $this->assertEquals(1, $user->userType);
        $this->assertEquals(1, $user->active);
        $this->assertEquals('65630340-1718-4A7E-8EE3-39DC3D448ED0', $user->verifyCode);
        $this->assertEquals(0, $user->verified);
        $this->assertEquals(0, $user->enteredDetails);
        $this->assertEquals(NULL, $user->resetCode);
    }

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

}



?>