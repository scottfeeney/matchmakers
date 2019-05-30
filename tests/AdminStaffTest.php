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
    //as the setUp and tearDown functionality for this class was not properly completed before
    //the same functionality was implemented elsewhere
    protected function setUp(): void {
        parent::setUp();
        //create record in user table
        $this->adminStaffUserRecord = SkillTest::staticSetupAdminUser($this->testEmail)[1]['adminUser'];
        //create record in adminStaff table
        $adminStaffSetupRes = APITest::setupAdminStaffRecord($this->adminStaffUserRecord->userId);
        if ($adminStaffSetupRes[0] != false) {
            $this->adminStaffAdminStaffRecord = $adminStaffSetupRes[1]['adminStaff'];
        }
        $this->testNonAdminUserId = UserTest::saveNewUser($this->testNonAdminEmail);
        if ($this->testNonAdminUserId == null) {
            $this->assertTrue(false, "Could not set up non-Admin user in setUp function");
        }
    }

    protected function tearDown(): void {
        APITest::tearDownAdminStaffRecord($this->adminStaffUserRecord->userId);
        SkillTest::tearDownAdminByEmail($this->adminStaffUserRecord->email);
        UserTest::staticTearDown(array($this->testNonAdminEmail));
        parent::tearDown();
    }


}

?>