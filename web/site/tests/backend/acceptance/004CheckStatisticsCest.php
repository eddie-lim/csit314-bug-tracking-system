<?php

use tests\backend\AcceptanceTester as AT;

use common\models\Bug;
use common\models\BugTag;
use common\models\User;

/* @var $scenario Codeception\Scenario */
class checkStatisticsCest{
    public $I;

    public function _before() {
    }

    public function _after() {
    }

    public function checkOverallBugsNumber(AT $I)
    {
        $I->login('Gene Williams', 'password');
        $I->reloadPage();
        $I->click('//a[@href="/statistics/index"]');
        $I->wait(1);
        $bug = new Bug();

        $activeBugs = sizeof($bug->getActiveBugsData());
        $resolvedBugs = sizeof($bug->getResolvedBugsData());
        $pendingBugs = sizeof($bug->getPendingBugsData());

        $I->seeElement('//div[contains(@class, "bg-danger")]/b[text()="Active bugs: '.$activeBugs.'"]');
        $I->seeElement('//div[contains(@class, "bg-success")]/b[text()="Resolved bugs: '.$resolvedBugs.'"]');
        $I->seeElement('//div[contains(@class, "bg-primary")]/b[text()="Bugs pending review: '.$pendingBugs.'"]');
    }

    public function checkTotalBugs(AT $I)
    {
        $bug = new Bug();
        $totalBugs = $bug->getBugStatusData();

        foreach($totalBugs as $x){
            $I->seeElement('//div[@class="card-text" and contains(text(), '.ucfirst($x['bug_status']).')]');
            $I->seeElement('//span[contains(@class, "badge") and  contains(text(), '.$x['counter'].')]');
        }
    }

    public function checkTopDevelopers(AT $I)
    {
        $bug = new Bug();
        $devData = $bug->getTopDeveloperData();

        foreach($devData as $data){
            $devName = User::findIdentity($data['developer_user_id'])->username;
            $I->waitForText($devName, 30);
            $I->seeElement('//b[text()="'.User::findIdentity($data['developer_user_id'])->username.'"]');
            $I->seeElement('//b[text()="'.User::findIdentity($data['developer_user_id'])->username.'"]/following-sibling::span[contains(@class, "badge") and contains(text(), "'.$data['counter'].'")]');
        }
    }

    public function checkTopPriorityLevel(AT $I)
    {
        $bug = new Bug();

        $priorityData = $bug->getPriorityLevelData();


        foreach($priorityData as $x){
            $I->seeElement('//div[@class="card-title"]/b[text()="Current bugs by Priority Level"]/../following-sibling::div[contains(., "Priority Level '.$x['priority_level'].'")]');
            $I->seeElement('//span[contains(@class, "badge") and contains(text(), "'.$x['counter'].'")]');
            $I->wait(1);
        }
    }

    public function checkPopularBugTags(AT $I)
    {
        $bug = new BugTag();
        $tagData = $bug->getTopBugTags();

        foreach($tagData as $x){
            $I->seeElement('//div[contains(@class, "card-text")]/div[contains(text(), "'.$x['name'].'")]');
            $I->seeElement('//div[contains(@class, "card-text")]/div/span[contains(text(), "'.$x['counter'].'")]');
        }
    }

    public function checkCurMonthReported(AT $I)
    {
        $bug = new Bug();
        $bugRes = $bug->getResolvedBugsByMonth();
        $bugRep = $bug->getReportedBugsByMonth();

        $curMonthRep = array();
        $curMonthRes = array();


        if(!array_search(date('m-Y'), array_column($bugRep, 'm_date'))){
            $bugRep[date('m-Y')] = 0;
        }

        foreach($bugRep as $data){
            $curMonthRep[$data['m_date']] = $data['counter'];
        }

        if(!array_search(date('m-Y'), array_column($bugRes, 'm_date'))){
            $bugRes[date('m-Y')] = 0;
        }

        foreach($bugRes as $data){
            $curMonthRes[$data['m_date']] = $data['counter'];
        }

        $I->seeElement('//i[contains(@class, "fa-bug")]/following-sibling::b[text()="'.$curMonthRep[date('m-Y')].'"]');
        $I->seeElement('//i[contains(@class, "fa-virus-slash")]/following-sibling::b[text()="'.$curMonthRes[date('m-Y')].'"]');
    }

    public function dateSelection(AT $I)
    {
        $I->selectDateRange('10 Apr 2020', '20 May 2020');
    }

    public function getReportedBugs(AT $I)
    {
        $I->click("Get no. of Reported Bugs");
        $I->wait(2);
        $I->seeElement('//h2[text()="Total no. of reported bugs"]');
        $I->wait(1);
    }

    public function getResolvedBugs(AT $I)
    {
        $I->click("Get no. of Resolved Bugs");
        $I->wait(2);
        $I->seeElement('//h2[text()="Total no. of resolved bugs"]');
        $I->wait(1);
    }

    public function getTopReporters(AT $I)
    {
        $I->click("Get top Reporters");
        $I->wait(2);
        $I->seeElement('//h2[text()="Top reporters"]/following-sibling::div[@class="grid-view"]');
        $I->wait(1);
    }

    public function getTopDevelopers(AT $I)
    {
        $I->click("Get top Developers");
        $I->wait(2);
        $I->seeElement('//h2[text()="Top developers"]/following-sibling::div[@class="grid-view"]');
        $I->wait(1);
    }
  }
