<?php

 /*
  * Class to test SkillCategory class
  * Rather trivial, but so is the class it is testing
  * Relies on static data, obviously this test will start failing if the categories change
  */

use PHPUnit\Framework\TestCase;


final class SkillCategoryTest extends TestCase {

    private $expectedCategoryIds = array(1,2,3,4,5);
    private $expectedCategories = array(1 => "Finance", 2 => "Health",
                        3 => "Information Technology", 4 => "Marketing", 5 => "Sales");

    public function testConstructorNoInput() {
        $category = new \Classes\SkillCategory();
        $this->assertSame(0, $category->skillCategoryId);
    }

    public function testExpectedCategoriesIndividually() {
        $names = array();
        foreach ($this->expectedCategoryIds as $categoryId) {
            $name = (new \Classes\SkillCategory($categoryId))->skillCategoryName;
            $names[$categoryId] = $name;
            $this->assertGreaterThan(0, strlen($name), 
                    "Category ".$categoryId." appears to have no name");
        }
    }

    public function testGetSkillCategories() {
        $actualCategories = \Classes\SkillCategory::GetSkillCategories();
        foreach ($this->expectedCategories as $id => $name) {
            $this->assertSame($name, ($actualCategories[$id-1])->skillCategoryName,
                    "Category ".$id." was expected to have name ".$name.
                    " but has name ".($actualCategories[$id-1])->skillCategoryName);
        }
    }

}

?>