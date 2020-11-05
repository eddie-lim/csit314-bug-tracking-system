<?php

use common\models\Bug;
use tests\backend\AcceptanceTester as AT;

/* @var $scenario Codeception\Scenario */
class CreateBugCest {
    public $I;
    protected $bugId;
    protected $curTime;

    public function _before(AT $I) {
    }

    public function _after(AT $I) {
    }

    protected function updateId($bugId)
    {
        $this->bugId = $bugId;
    }

    protected function updateTime($curTime)
    {
        $this->curTime = $curTime;
    }

    public function testUploadAttachment(AT $I)
    {
        $I->login('Vernon Taylor', 'password');
        $I->amOnPage('bug/index');
        $I->checkUploadFunction(['doggo.jpeg', 'cat.jpg']);
        assert(file_exists(getcwd() . '/backend/web/uploads/temp/user_13/doggo.jpeg'));
        assert(file_exists(getcwd() . '/backend/web/uploads/temp/user_13/cat.jpg'));
    }

    public function testRemoveAttachment(AT $I)
    {
        $I->checkRemoveFunction();
        $I->wait(1);
        assert(!file_exists(getcwd() . '/backend/web/uploads/temp/user_13/doggo.jpeg'));
        assert(!file_exists(getcwd() . '/backend/web/uploads/temp/user_13/cat.jpg'));
    }

    public function testAddBugTagFunction(AT $I)
    { 
        $I->checkAddTagFunction(["yoru", "ni", "kakeru"]);
    }

    public function testRemoveBugTagFunction(AT $I)
    {
        $I->checkRemoveTagFunction();
    }

    public function testBugCreationWithCorrectInfo(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->click('//a[@href="/bug/create"]');
        $I->createBug(
            array(
                'title' => 'chickenTest',
                'description' => 'This is an extremely huge chicken.',
                'images' => ['doggo.jpeg'],
                'tags' => ['chicken', 'test'],
            )
        );
        $this->updateTime(time());
        $I->wait(2);
        $I->see('Bug created successfully!', '.alert-success');
        $I->seeCurrentUrlMatches('~(.*)/bug/view(.*)~');
       
        $this->updateId(
         $I->grabFromCurrentUrl('~/bug/view\?id=(\d+)~')
        );

        codecept_debug($this->bugId);
        assert(file_exists(getcwd() . '/backend/web/uploads/bug_' . $this->bugId . '/doggo.jpeg'));
    }

    public function testBugCreationWithNoTitle(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->click('//a[@href="/bug/create"]');
        $I->createBug(
            array(
                'title' => null,
                'description' => 'This is an extremely huge chicken.',
                'images' => ['doggo.jpeg'],
                'tags' => ['chicken', 'test'],
            )
        );
        $I->wait(1);
        $I->see('Title cannot be blank.', '.alert-danger');
        $I->seeCurrentUrlMatches('~(.*)/bug/create~');
    }

    public function testBugCreationWithNoDescription(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->click('//a[@href="/bug/create"]');
        $I->createBug(
            array(
                'title' => 'chickenTest',
                'description' => null,
                'images' => ['doggo.jpeg'],
                'tags' => ['chicken', 'test'],
            )
        );
        $I->wait(1);
        $I->see('Description cannot be blank.', '.alert-danger');
        $I->seeCurrentUrlMatches('~(.*)/bug/create~');
    }

    public function testBugCreationWithNoTitleAndNoDescription(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->click('//a[@href="/bug/create"]');
        $I->createBug(
            array(
                'title' => null,
                'description' => null,
                'images' => ['doggo.jpeg'],
                'tags' => ['chicken', 'test'],
            )
        );
        $I->wait(1);
        $I->see('Description cannot be blank.', '.alert-danger');
        $I->seeCurrentUrlMatches('~(.*)/bug/create~');
    }

    public function testViewAllBugs(AT $I)
    {
        $I->click('//a[@href="/bug/index"]');
        $I->wait(1);

        $numBugs = intval(Bug::find()->count());
        $linkCount = 0;
        while(True){ 
            $linkCount += count($I->grabMultiple('//tbody/tr', 'data-key'));
            codecept_debug($linkCount);
            try {
                $I->seeElement('//li[@class="page-item next"]');
                $I->click('//li[@class="page-item next"]/a');
                $I->wait(1);
            } catch (Exception $e){
                break;
            }
        }
        assert(($numBugs === $linkCount));

        $I->logout();
    }

    public function testViewAllPendingAttentionAsTriager(AT $I)
    {
        //$I->logout();
        $I->login('John Hendrix', 'password');
        $I->click('//a[@href="/bug/tasks"]');

        $assignedBugs = array_column(Bug::findAll(['bug_status' => 'new']), 'id');

        while(True){ 
            $links = $I->grabMultiple('//tbody/tr', 'data-key');
            foreach($links as $link){
                assert(in_array($link, $links));
            }
            try {
                $I->seeElement('//li[@class="page-item next"]');
                $I->click('//li[@class="page-item next"]/a');
                $I->wait(1);
            } catch (Exception $e){
                break;
            }
        }

        $I->logout();
    }

    public function testViewAllPendingAttentionAsDeveloper(AT $I)
    {
        $I->login('Vernon Taylor', 'password');
        $I->click('//a[@href="/bug/tasks"]'); 
        $I->wait(1);

        $assignedBugs = array_column(Bug::findAll(['developer_user_id' => 13]), 'id');

        while(True){ 
            $links = $I->grabMultiple('//tbody/tr', 'data-key');
            foreach($links as $link){
                assert(in_array($link, $links));
            }
            try {
                $I->seeElement('//li[@class="page-item next"]');
                $I->click('//li[@class="page-item next"]/a');
                $I->wait(1);
            } catch (Exception $e){
                break;
            }
        }
        $I->logout();
    }

    public function testViewAllPendingAttentionAsReviewer(AT $I)
    {
        $I->login('Alvin Bruce', 'password');
        $I->click('//a[@href="/bug/tasks"]'); 
        $I->wait(1);

        $assignedBugs = array_column(Bug::findAll(['bug_status' => 'pending_review']), 'id');

        while(True){ 
            $links = $I->grabMultiple('//tbody/tr', 'data-key');
            foreach($links as $link){
                assert(in_array($link, $links));
            }
            try {
                $I->seeElement('//li[@class="page-item next"]');
                $I->click('//li[@class="page-item next"]/a');
                $I->wait(1);
            } catch (Exception $e){
                break;
            }
        }
        $I->logout();
    }

    public function testViewAllCompletedBugs(AT $I)
    {
        $I->login('Vernon Taylor', 'password');
        $I->click('//a[@href="/bug/closed"]'); 
        $I->wait(1);

        $assignedBugs = array_column(Bug::findAll(['bug_status' => 'completed']), 'id');

        while(True){ 
            $links = $I->grabMultiple('//tbody/tr', 'data-key');
            foreach($links as $link){
                assert(in_array($link, $links));
            }
            try {
                $I->seeElement('//li[@class="page-item next"]');
                $I->click('//li[@class="page-item next"]/a');
                $I->wait(1);
            } catch (Exception $e){
                break;
            }
        }

        $I->logout();
    }

    public function testViewAllSubmittedBugs(AT $I)
    {
        $I->login('Vernon Taylor', 'password');
        $I->click('//a[@href="/bug/user-submissions"]'); 
        $I->wait(1);

        $assignedBugs = array_column(Bug::findAll(['created_by' => 13]), 'id');

        while(True){ 
            $links = $I->grabMultiple('//tbody/tr', 'data-key');
            foreach($links as $link){
                assert(in_array($link, $links));
            }
            try {
                $I->seeElement('//li[@class="page-item next"]');
                $I->click('//li[@class="page-item next"]/a');
                $I->wait(1);
            } catch (Exception $e){
                break;
            }
        }

        $I->click('//a[@href="/bug/index"]');
    }

    public function checkFilterFunctionById(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->pressKey('//input[@name="BugSearch[id]"]', $this->bugId, \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->wait(1);
        $I->seeElement('//tr[@data-key='.$this->bugId.']');
        $I->clearField('BugSearch[id]');
        $I->wait(1);
    }

    public function checkFilterFunctionByTitle(AT $I)
    {
        $I->pressKey('//input[@name="BugSearch[title]"]', 'chickenTest', \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->wait(1);
        $I->seeElement('//tr[@data-key='.$this->bugId.']');
        $I->clearField('BugSearch[title]');
        $I->wait(1);
    }

    public function checkFilterFunctionByStatus(AT $I)
    {
        $I->pressKey('//input[@name="BugSearch[bug_status]"]', 'new', \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->wait(1);
        $I->seeElement('//tr[@data-key='.$this->bugId.']');
        $I->clearField('BugSearch[bug_status]');
        $I->wait(1);
    }

    public function checkFilterFunctionByPriority(AT $I)
    {
        $I->pressKey('//input[@name="BugSearch[priority_level]"]', '1', \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->wait(1);
        $I->seeElement('//tr[@data-key='.$this->bugId.']');
        $I->clearField('BugSearch[priority_level]');
        $I->wait(1);
        $I->logout();
    }

    // search is via unix timestamp
    /*
    public function checkFilterFunctionByTime(AT $I)
    {
        codecept_debug($this->curTime);
        $I->pressKey('//input[@name="BugSearch[created_at]"]', $this->curTime, \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->wait(1);
        $I->seeElement('//tr[@data-key='.$this->bugId.']');
        $I->clearField('BugSearch[created_at]');
        $I->wait(1);
    }
     */
}
