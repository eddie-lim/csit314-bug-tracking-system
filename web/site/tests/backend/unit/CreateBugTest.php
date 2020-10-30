<?php namespace tests\backend;

use common\models\User;
use common\models\BugComment;
use common\models\BugTag;
use backend\models\BugCreationForm;

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

    public function testBingBong()
    {
        $someval = $this->tester->seeRecord('common\models\BugComment', array('comment' => 'sdfs'));
        //$asd = new BugComment();
        codecept_debug($someval);
        $asd->id = 2;
        $asd->bug_id = 368;
        $asd->comment = "somechickencomment";
        $asd->delete_status= "enabled";
        codecept_debug($asd->id);
    }

    /*
    public function testDing()
    { 
        $id = $this->tester->grabRecord(BugComment::class, ['comment' => 'sdfs']);
        //$comment = BugComment::findOne($id);

        $new = new BugComment();
        $new->id = 2;
        $new->bug_id = 368;
        $new->comment = "chicken_comment";
        $new->delete_status = "enabled";
        $new->created_at = 1603708899;
        $new->created_by = 1;

        $this->tester->haveRecord(BugComment::class, ['xd'=> 2, 'comment' => 'azxcvm']);
    }

    public function testCreateBug()
    {
        $form = new BugCreationForm();

        $form->title = "chickenTest";
        $form->description = "This is an extremely big chicken where we have to delete.";
        $form->createBug();

        codecept_debug($form);
        //$this->tester->seeRecord('common\models\Bug', array('title' => 'chickenTest'));
    }
     */
}
