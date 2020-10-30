<?php

namespace tests\backend;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    public function login($username, $password)
    {
        $this->amOnPage('/sign-in/login');
        $this->fillField('#loginform-username', $username);
        $this->fillField('#loginform-password', $password);
        $this->click('//button[@name="login-button"]');
        $this->wait(2);
    }

    public function logout()
    {
        $this->amOnPage('/bug/index');
        $this->clickWithLeftButton('//a[@class="nav-link dropdown-toggle" and @data-toggle="dropdown"]');
        $this->wait(1);
        $this->clickWithLeftButton('//a[@data-method="post" and @href="/sign-in/logout"]');
    }


    public function createBug($params)
    {
        $this->amOnPage('/bug/create');
        $this->fillField('#bugcreationform-title', $params["title"]);
        $this->fillField('#bugcreationform-description', $params["description"]);

        foreach($params["images"] as $image)
        { 
            $this->attachFile(
                '//input[@type="file"]', 
                $image // file located in _data
            ); 
        }


        foreach($params["tags"] as $tag)
        { 
            //https://php-webdriver.github.io/php-webdriver/1.0.0/Facebook/WebDriver/WebDriverKeys.html
            $this->pressKey(
                '.select2-search__field', 
                $tag,
                \Facebook\WebDriver\WebDriverKeys::ENTER
            );
        }

        $this->click('Create');
    }

    public function checkUploadFunction($images)
    {
        $this->amOnPage('/bug/create');
        foreach($images as $image)
        {
            $this->attachFile('//input[@type="file"]', $image);
            $this->wait(1);
            $this->seeElement('//img[@title="'.$image.'"]'); 
        }
    }

    public function checkRemoveFunction($images = null)
    {
        if($images != null){
            foreach($images as $image){
                $this->click('//button[@class="close fileinput-remove"]');
                $this->wait(1);
                $this->seeNumberOfElements('//div[@class="file-preview-thumbnails clearfix"]', 0);
            }
        } else {  
            $this->click('//button[@class="close fileinput-remove"]');
            $this->dontSeeElement('//div[@class="kv-file-content"]');
        }
    }


    public function checkAddTagFunction($tags)
    {
        $this->amOnPage('/bug/create');
        foreach($tags as $tag)
        {
            $this->pressKey(
                '.select2-search__field',
                $tag,
                \Facebook\WebDriver\WebDriverKeys::ENTER
            );
            $this->seeElement('//li[@class="select2-selection__choice" and @title="'.$tag.'"]');
        }
    }

    public function checkRemoveTagFunction($tags = null)
    {
        if($tags != null){
            foreach($tags as $tag)
            {
                $this->click('//li[@class="select2-selection__choice" and @title="'.$tag.'"]/span');
                $this->dontSeeElement('//li[@class="select2-selection__choice" and @title="'.$tag.'"]');
            }
        } else {
            $this->click('//span[@class="select2-selection__clear"]');
            $this->dontSeeElement('//li[@class="select2-selection__choice"]');
        }
    }

    public function accessBug($bug_id)
    {
        $this->wait(1);
        $this->click('//a[@href="/bug/view?id='.$bug_id.'"]');
        $this->wait(2);
        $this->seeCurrentUrlMatches('~/bug/view\?id='.$bug_id.'~');
    }

    public function checkCommentFunction($someText)
    {
        $this->fillField('#bugcomment-comment', $someText);
        $this->click('Post');
        $this->wait(1);
        $this->see($someText, '//div[@class="card-body"]');
    }

    public function checkDownloadFunction()
    {
        $img = $this->grabMultiple('//a[@title="kv-file-download btn btn-sm btn-kv btn-default btn-outline-secondary"]');
        $img = $this->grabMultiple('//a[@title="Download file"]', 'download');
        
        foreach($img as $imeji){
            $this->click('//a[@title="Download file" and @download="'.$imeji.'"]/i[@class="fas fa-download"]');
            $this->wait(2);
            $this->seeFileFound($imeji, './_downloads');
        }
    }

    public function updateBugPropertiesTriager()
    {
        // assign to one of the available developers
        $this->click('//span[@class="select2-selection select2-selection--single"]');

        $ele = $this->grabMultiple('//li[contains(@class, "select2-results__option")]', 'id');
        $selection = $ele[rand(0, count($ele)-1)];
        $dev = $this->grabTextFrom('//li[@id="'.$selection.'"]');
        $this->click('//li[@id="'.$selection.'"]');

        $this->selectOption('BugTaskForm[status]', 'Assign');
        $pri = rand(0,2);
        $this->selectOption('BugTaskForm[priority_level]', ['Low', 'Med', 'High'][$pri]);
        $this->fillField('#create-tag-input', 'tagtest1');
        $this->click('//a[@id="create-tag"]');
        $this->fillField('#bugtaskform-notes', 'this is some content inside notes');
        $this->click('Update');
        $this->wait(1);

        // check ajax status updates
        $this->see($dev, '#developer_user');
        $this->see('assigned', '#bug_status');
        $this->see($pri+1, '//span[@id="priority_level" and contains(@class, "badge")]');

        $this->click('#task-form-header');
        $this->wait(1);
        // \xc2\xa0 to replace &nbsp;
        $availTags = str_replace("\xc2\xa0", '', array_map('strtolower', $this->grabMultiple('//div[@id="tag-badge-wrapper"]/div', 'innerText')));
        assert(in_array('tagtest1', $availTags));

        return $dev;
    }

    public function acceptAssignedBug()
    {
        $this->click('//div[@class="form-group"]/div[contains(@class, "bootstrap-switch")]');
        $this->fillField('#create-tag-input', 'tagtest2');
        $this->click('//a[@id="create-tag"]');
        $this->click('Update');
        $this->wait(2);

        // check ajax
        $this->see('fixing', '#bug_status');
        $this->wait(1);
        // \xc2\xa0 to replace &nbsp;
        $availTags = str_replace("\xc2\xa0", '', array_map('strtolower', $this->grabMultiple('//div[@id="tag-badge-wrapper"]/div', 'innerText')));
        assert(in_array('tagtest2', $availTags));
    }

    public function pendingReview()
    {
        $this->checkCommentFunction("fixed, pend rev");
        $this->selectOption('BugTaskForm[status]', 'Pending Review');
        $this->click('Update');
        $this->wait(2);
        $this->see('pending_review', '#bug_status');        
    }

    public function completeBug()
    {
        $this->checkCommentFunction("bug fix confirmed, closing.");
        $this->selectOption('BugTaskForm[status]', 'Complete');
        $this->click('Update');
        $this->wait(1);
        $this->see('complete', '#bug_status');        
    }

    public function reopenBug()
    {
        $this->checkCommentFunction("bug fix failed, reopening.");
        $this->selectOption('BugTaskForm[status]', 'Re-open');
        $this->click('Update');
        $this->wait(1);
        $this->see('reopen', '#bug_status');        
    }
}
