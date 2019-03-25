<?php

use PHPUnit\Framework\TestCase;

require_once "e:\\classes\\user.php";

final class UserTest extends TestCase {
    public function testConstructorUserIdZeroGivenZero() {
        $this->assertEquals(0, (new \Classes\User(0))->userId);
    }
}


?>