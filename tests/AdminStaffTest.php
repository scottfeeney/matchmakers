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



    public function testConstructor() {
        $adminStaff = new \Classes\AdminStaff(0);
        $this->assertSame(0, $adminStaff->adminStaffId);
    }

    public function testConstructorNegative() {
        $adminStaff = new \Classes\AdminStaff(-1);
        $this->assertSame(0, $adminStaff->adminStaffId);
    }

    public function testConstructorSuccess() {
        $this->assertTrue((new \Classes\AdminStaff($this->adminStaffAdminStaffRecord->adminStaffId))->adminStaffId != 0);
        $this->assertTrue((new \Classes\AdminStaff($this->adminStaffAdminStaffRecord->adminStaffId))->userId != null);
    }

    /** save method is commented out in adminstaff class, as we currently have no facility for 
     * adding new adminstaff records except via direct database query
     * So nothing to test
     
    public function testSaveUser() {
        extract($this->createUserAndAdminStaff());
        $this->assertEquals($adminStaff, new \Classes\AdminStaff($asid));
    }
*/
    /**
     * Helper function as multiple tests will want to work on a record already in the database
     *  UPDATE: commenting out as we have commented out the save method in adminstaff
     * While the functionality to manage adminstaff records via the site may be added later,
     * currently adminstaff records can be added/edited/removed by database query only

    private function createUserAndAdminStaff() {
        $oid = UserTest::saveNewUser();
        $this->uidsToDelete[] = $oid;
        $adminStaff = new \Classes\AdminStaff(0);
        $adminStaff->userId = $oid;
        $oSave = $adminStaff->save();
        $asid = $oSave->objectId;
        $adminStaff->adminStaffId = $asid;
        return array('oid' => $oid, 'asid' => $asid, 'adminStaff' => $adminStaff);
    }


    public function testEditUser() {
        extract($this->createUserAndAdminStaff());
        $adminStaff->firstName = "Bob";
        $oSave2 = $adminStaff->save();
        $asid = $oSave2->objectId;
        $this->assertSame($adminStaff->firstName, (new \Classes\AdminStaff($asid))->firstName);
    }

    */

    public function testGetAdminStaffByUserIdFailure() {
        $this->assertSame(null, \Classes\AdminStaff::GetAdminStaffByUserId(0));
    }

    public function testGetAdminStaffByUserIdSuccess() {
        $this->assertTrue(\Classes\AdminStaff::GetAdminStaffByUserId($this->adminStaff->userId) != null);
    }

    protected function setUp(): void {
        parent::setUp();
        //create record in user table
        $this->adminStaffUserRecord = SkillTest::staticSetupAdminUser($this->testEmail)[1]['adminUser'];
        //create record in adminStaff table
        $adminStaffSetupRes = APITest::setupAdminStaffRecord($this->adminStaffUserRecord->userId);
        if ($adminStaffSetupRes[0] != false) {
            $this->adminStaffAdminStaffRecord = $adminStaffSetupRes[1]['adminStaff'];
        }
    }

    protected function tearDown(): void {
        APITest::tearDownAdminStaffRecord($this->adminStaffUserRecord->userId);
        SkillTest::tearDownAdminByEmail($this->adminStaffUserRecord->email);
        parent::tearDown();
    }


}

?>