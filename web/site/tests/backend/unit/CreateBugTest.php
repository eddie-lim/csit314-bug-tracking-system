<?php namespace tests\backend;

use common\models\User;
use common\models\BugDocument;

class CreateBugTest extends \Codeception\Test\Unit
{
    /**
     * @var \tests\backend\UnitTester
     */
    protected $tester;
    protected $tag_id;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testBugDocumentValid()
    {
        $bugDocument = new BugDocument();
    }

}
