<?php
namespace common\components;

use Yii;
use \yii\db\Expression;
use trntv\filekit\behaviors\UploadBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\behaviors\MyAuditTrailBehavior;
use yii\helpers\ArrayHelper;


class MyCustomActiveRecord extends \yii\db\ActiveRecord {
    const DELETE_STATUS_ENABLED = "enabled";
    const DELETE_STATUS_DISABLED = "disabled";

    public $upload_file;

    public function init() {
        if (!isset(Yii::$app->controller) || is_null(Yii::$app->controller)){
            $this->detachBehavior('timestamp');
            $this->detachBehavior('blame');
            $this->detachBehavior('auditTrail');
        }
        if(property_exists($this,'status') && !method_exists($this,'search')) {
            $this->status = SELF::DELETE_STATUS_ENABLED;
        }
        parent::init();

    }
    
    public function behaviors()
    {
        return [
            "timestamp" => TimestampBehavior::className(),
            "blame" => BlameableBehavior::className(),
            "upload" =>
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'upload_file',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url'
            ],
            "auditTrail" => MyAuditTrailBehavior::className(),  
        ];
    }
    public function rules()
    {
        return [
            [['upload_file'], 'safe'] //important for upload!!
        ];
    }

    public static function deleteStatuses()
    {
        return [
            self::DELETE_STATUS_ENABLED => Yii::t('common', 'Enabled'),
            self::DELETE_STATUS_DISABLED => Yii::t('common', 'Disabled')
        ];
    }

    static public function getStatusHtml($model) {
        $m = $model;
        if ($m->delete_status == SELF::DELETE_STATUS_DISABLED) {
            $html = "<i class='text-danger fas fa-circle'></i>";
        } else if ($m->delete_status == SELF::DELETE_STATUS_ENABLED) {
            $html = "<i class='text-success fas fa-circle'></i>";
        } 
        return $html;
    }
    static public function getUserStatusHtml($model) {
        $m = $model;
        if ($m->status == 1) {
            $html = "<i class='text-danger fas fa-circle'></i>";
        } else if ($m->status == 2) {
            $html = "<i class='text-success fas fa-circle'></i>";
        } 
        return $html;
    }
    static public function toObjectArray($models) {
        $d = [];
        foreach ($models as $m) {
            $o = $m->toObject();
            $d[] = $o;
        }
        return $d;
    }



}
