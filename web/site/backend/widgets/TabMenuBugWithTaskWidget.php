<?php
namespace backend\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Menu
 * @package backend\components\widget
 */
class TabMenuBugWithTaskWidget extends Widget
{
    public $page = "active";

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $active1 = $active2 = $active3 = $active4 = "";
        if ($this->page == "index") {
            $active1 = "active";
        } else if ($this->page == "tasks") {
            $active2 = "active";
        } else if ($this->page == "closed") {
            $active3 = "active";
        } else if ($this->page == 'user-submissions') {
            $active4 = "active";
        }

        $link1 =  Url::to(["bug/index"]);
        $link2 =  Url::to(["bug/tasks"]);
        $link3 =  Url::to(["bug/closed"]);
        $link4 =  Url::to(["bug/user-submissions"]);

        $content = <<<HEREDOC
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link $active1" href="$link1">All</a>
    </li>
    <li class="nav-item">
        <a class="nav-link $active2" href="$link2">Pending your attention</a>
    </li>
    <li class="nav-item">
        <a class="nav-link $active3" href="$link3">Closed</a>
    </li>
    <li class="nav-item">
        <a class="nav-link $active4" href="$link4">My Submissions</a>
    </li>
</ul>
<br>
HEREDOC;

//

        //$this->registerClientScript();
        return $content;
    }
}
