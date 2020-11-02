<?php

namespace tests\common;

use common\models\BugDocument;

class BugDocumentTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_BUG_ID = 500;
    const DEFAULT_DELETE_STATUS = "enabled";
    const DEFAULT_CREATED_AT = 1604308755; // 2020-11-02T18:00:00+00:00
    const DEFAULT_CREATED_BY = 24;
    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;
    protected $bugDocument;


    protected function _before()
    {
        $this->bugDocument = new BugDocument();
        $this->bugDocument->bug_id = SELF::DEFAULT_BUG_ID;
        $this->bugDocument->delete_status = SELF::DEFAULT_DELETE_STATUS;
        $this->bugDocument->created_at = SELF::DEFAULT_CREATED_AT;
        $this->bugDocument->created_by = SELF::DEFAULT_CREATED_BY;
    }

    protected function _after()
    {
    
    }

    public function testBugDocumentIsValid()
    {
        $this->assertTrue($this->bugDocument->validate());
    }
}
