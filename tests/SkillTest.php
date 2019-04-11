<?php

use PHPUnit\Framework\TestCase;


final class SkillTest extends TestCase {

    public function testConstructorZero() {
        $skill = new \Classes\Skill(0);
        $this->assertSame(0, $skill->skillId);
    }

}

?>