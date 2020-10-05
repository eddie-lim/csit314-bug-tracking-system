<?php
namespace backend\widgets;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

class MyInfoBox extends Widget
{
    public $bgColor = "";
    public $link = "";
    public $value = "";
    public $icon = "";
    public $title = "";
    public $description = "";

    public function init()
    {
        parent::init();        
    }

    public function run()
    {
        $titleHtml = $this->link != "" ? '<a style="color:white; text-decoration: underline;" href="'.$this->link.'">'.$this->title.'</a>' : $this->title;
        $content = <<<HEREDOC
        <div class="info-box">
        <div class="info-box-title $this->bgColor">$titleHtml</div>
            <span class="info-box-icon $this->bgColor">$this->icon</span>
            <div class="info-box-content">
                <p class="text-muted small" >$this->description</p>
                <p class="info-box-number">$this->value</p>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
HEREDOC;

        return $content;
    }
}
