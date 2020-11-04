<?php

namespace tests\common;

use Faker\Factory;
use common\models\BugTag;
use common\components\MyCustomActiveRecord;

class BugTagTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_BUG_ID = 500;
    const DEFAULT_NAME = "frontend";
    const DEFAULT_DELETE_STATUS = "enabled";
    const DEFAULT_CREATOR       = 1;
    /**
     * @var \tests\common\UnitTester
     */
    protected $bugTag;

    protected function _before()
    {
        $this->bugTag = new BugTag();
        $this->bugTag->created_at      = time();
        $this->bugTag->created_by      = SELF::DEFAULT_CREATOR;
    }

    protected function _after()
    {
    
    }
    
    public function testBugIdIsRequired()
    {
        $this->assertFalse($this->bugTag->validate(['bug_id']));
    }

    public function testBugIdIsPresent()
    {
        $this->bugTag->bug_id = SELF::DEFAULT_BUG_ID;
        $this->fieldIsPresent($this->bugTag, 'bug_id', true);
    }

    public function testBugIdIsInteger()
    {
        $this->fieldHasType($this->bugTag, 'bug_id', 'integer');
    }
    
    public function testNameIsRequired()
    {
        $this->assertFalse($this->bugTag->validate(['name']));
    }

    public function testNameIsPresent()
    {
        $this->bugTag->name = SELF::DEFAULT_NAME;
        $this->fieldIsPresent($this->bugTag, 'name', true);
    }

    public function testNameIsString()
    {
        $this->fieldHasType($this->bugTag, 'name', 'string');
    }

    public function testDeleteStatusIsPresent()
    {
        $this->bugTag->delete_status = SELF::DEFAULT_DELETE_STATUS;
        $this->fieldIsPresent($this->bugTag, 'delete_status', true);
    }

    public function testDeleteStatusHasPermittedValue()
    {
        $permit = array_keys(MyCustomActiveRecord::deleteStatuses());

        $reject = [
            'some', 'random', 'values', 'thats', 'not', 'permitted'
        ];

        $this->fieldHasPermittedValue($this->bugTag, 'delete_status', $permit, $reject);
    }

    public function testCreatedAtIsPresent()
    {
        $this->fieldIsPresent($this->bugTag, 'created_at', true);
    }

    public function testCreatedAtIsInteger()
    {
        $this->fieldHasType($this->bugTag, 'created_at', 'integer');
    }

    public function testCreatedByIsPresent()
    {
        $this->fieldIsPresent($this->bugTag, 'created_by', true);
    }

    public function testCreatedByInteger()
    {
        $this->fieldHasType($this->bugTag, 'created_by', 'integer');
    }

    public function testBugTagIsValid()
    {
        $this->assertTrue($this->bugTag->validate());
        codecept_debug('hello');
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugTag->save();
        $this->bugTag->refresh();

        $this->assertEqualsWithDelta($time, $this->bugTag->created_at, 1);
    }

}
