<?php

use PHPUnit\Framework\TestCase;



final class UserTest extends TestCase {
    public function testConstructorUserIdZeroGivenZero() {
        $this->assertEquals(0, (new \Classes\User(0))->userId);
    }

    public function testConstructorUserIdZeroGivenNegative() {
        $this->assertEquals(0, (new \Classes\User(-1))->userId);
    }

    public function testUserFields() {
        //Will of course only work if there is a DB record
        //with the following values
        //A more complex test would specifically put these values
        //in the DB first - but would also need to make sure
        //there isn't already a record with that ID
        $user = new \Classes\User(2);
        $this->assertEquals('s3669208!1@student.rmit.edu.au', $user->email);
        $this->assertEquals('$2y$10$fu0iNNeins4UHY3JeD1oc.cdv5xVf6z0ZeYmVrhv7Vjg0LJbF36By', $user->password);
        $this->assertEquals(1, $user->userType);
        $this->assertEquals(1, $user->active);
        $this->assertEquals('143AD590-3599-4E5A-B8E6-E62160250061', $user->verifyCode);
        $this->assertEquals(1, $user->verified);
        $this->assertEquals(0, $user->enteredDetails);
        $this->assertEquals(NULL, $user->resetCode);
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

    public function testSaveNewUser() {
        //$user = new \Classes\User
    }

}



?>