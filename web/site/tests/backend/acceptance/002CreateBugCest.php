<?php

use tests\backend\AcceptanceTester as AT;

/* @var $scenario Codeception\Scenario */
class CreateBugCest {
    public $I;
    protected $bugId;
    protected $curTime;

    public function _before(AT $I) {
        $I->amOnPage('bug/index');
        //$I->wait(3);
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

    public function checkAttachmentFunctions(AT $I)
    {
        $I->checkUploadFunction(['doggo.jpeg']);
        $I->checkRemoveFunction(['doggo.jpeg']);
        $I->checkUploadFunction(['doggo.jpeg', 'cat.jpg']);
        $I->checkRemoveFunction();
    }

    public function checkBugTagFunctions(AT $I)
    {
        $I->checkAddTagFunction(["yoru", "ni", "kakeru"]);
        $I->checkRemoveTagFunction(["yoru"]);
        $I->checkRemoveTagFunction();
    }

    public function createBugCorrect(AT $I)
    {
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
    }

    public function createBugNoTitle(AT $I)
    {
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

    public function createBugNoDescription(AT $I)
    {
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

    public function createBugNoTitleNoDescription(AT $I)
    {
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

    public function checkFilterFunctionById(AT $I)
    {
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
