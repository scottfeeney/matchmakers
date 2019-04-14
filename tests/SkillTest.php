<?php

use PHPUnit\Framework\TestCase;


final class SkillTest extends TestCase {

    public function testConstructorZero() {
        $this->markTestIncomplete(); //Skill class not yet on production
        $skill = new \Classes\Skill(0);
        $this->assertSame(0, $skill->skillId);
    }

}

?>