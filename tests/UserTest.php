<?php

use PHPUnit\Framework\TestCase;



final class UserTest extends TestCase {

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
        $user = new \Classes\User(2);
        $user->userId = 1;
        $objSave = $user->save();
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
        $objSave = $user-save();
        $idsToDelete[] = $objSave->objectId;
        return $objSave->objectId;
    }

    public function testSaveGetUser() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $this->assertEquals('unit@tester.com', $user->email);
        $this->assertEquals(NULL, $user->password);
        $this->assertEquals(1, $user->userType);
        $this->assertEquals(1, $user->active);
        $this->assertEquals('65630340-1718-4A7E-8EE3-39DC3D448ED0', $user->verifyCode);
        $this->assertEquals(0, $user->verified);
        $this->assertEquals(0, $user->enteredDetails);
        $this->assertEquals(NULL, $user->resetCode);
    }

    public function testUpdateUser() {
        $oid = $this->saveNewUser();
        $user = new \Classes\User($oid);
        $user->email = "reallyunittesting@test.com";
        $oid2 = $user->save();
        $user2 = new \Classes\User($oid2);
        $this->assertEquals($oid, $oid2);
        $this->assertEquals("reallyunittesting@test.com", $user2->email);
    }

    public function tearDown() {
        //delete all records created by test process
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($idsToDelete as $currId) {
            $sql = "delete from user where id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $currId);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
			$stmt->close();
        }
        $conn->close();
    }

}



?>