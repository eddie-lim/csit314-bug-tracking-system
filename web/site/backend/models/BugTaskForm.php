<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Bug;
use common\models\User;
use common\models\BugDocument;
use common\models\BugTag;

class BugTaskForm extends Model
{
	// common
    public $model;
    public $id;
    public $notes;

    // developer
    public $accept;

    // triager
    public $developer_user_id;
    public $priority_level;

    // reviewer & triager
    public $status;

    const SCENARIO_DEVELOPER = "developer";
    const SCENARIO_TRIAGER = "triager";
    const SCENARIO_REVIEWER = "reviewer";

    public function rules()
    {
        return [
            ['id', 'required'],
            ['notes', 'string'],
			['accept', 'boolean', 'on'=>SELF::SCENARIO_DEVELOPER],
			[['developer_user_id', 'priority_level'], 'integer', 'on'=>SELF::SCENARIO_TRIAGER],
			['status', 'in', 'range'=> array(Bug::BUG_STATUS_NEW, Bug::BUG_STATUS_ASSIGNED, Bug::BUG_STATUS_REJECTED), 'on'=>SELF::SCENARIO_TRIAGER],
			['status', 'in', 'range'=> array(Bug::BUG_STATUS_COMPLETED, Bug::BUG_STATUS_REOPEN), 'on'=>SELF::SCENARIO_REVIEWER],
            [['id'], 'exist', 'skipOnError' => false, 'targetClass' => Bug::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'status' => 'Bug Ticket Status',
            'priority_level' => 'Priority Level',
            'developer_user_id' => 'Developer',
            'id' => 'ID',
            'accept' => 'Acknowledge',
            'notes' => 'Notes',
        ];
    }

    public static function getStatusTriager(){
        return [
            Bug::BUG_STATUS_NEW => "New",
            Bug::BUG_STATUS_ASSIGNED => "Assign",
            Bug::BUG_STATUS_REJECTED => "Reject",
        ];
    }

    public static function getStatusReviewer(){
        return [
            Bug::BUG_STATUS_COMPLETED => "Complete",
            Bug::BUG_STATUS_REOPEN => "Re-open",
        ];
    }
    
    public function __construct($id){
    	$this->id = $id;
        $this->model = $this->findModel();
        if($this->model){
            $this->notes = $this->model->notes;
        }
    }

    public function process(){
        if ($this->scenario == User::ROLE_REVIEWER){
            return $this->feedback();
        } elseif ($this->scenario == User::ROLE_TRIAGER){
            return $this->assign();
        } elseif ($this->scenario == User::ROLE_DEVELOPER){
            return $this->acknowledge();
        }
    }

    private function acknowledge(){
        $this->model->bug_status = Bug::BUG_STATUS_FIXING;
        $this->model->notes = $this->notes;
        return $this->model->save();
    }

    private function assign(){
        if($this->status != Bug::BUG_STATUS_REJECTED){
            $this->model->developer_user_id = $this->developer_user_id;
            $this->model->priority_level = $this->priority_level;
        }
        $this->model->notes = $this->notes;
        $this->model->bug_status = $this->status;
        return $this->model->save();
    }

    private function feedback(){
        $this->model->bug_status = $this->status;
        $this->model->notes = $this->notes;
        return $this->model->save();
    }

    private function findModel()
    {
        return $model = Bug::findOne($this->id);
    }
}
