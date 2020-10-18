<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Bug;
use common\models\BugDocument;
use common\models\BugTag;

class BugTaskForm extends Model
{
	// common
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
			['status', 'in', 'range'=> array(Bug::BUG_STATUS_ASSIGNED, Bug::BUG_STATUS_REJECTED), 'on'=>SELF::SCENARIO_TRIAGER],
			['status', 'in', 'range'=> array(Bug::BUG_STATUS_COMPLETED, Bug::BUG_STATUS_REOPEN), 'on'=>SELF::SCENARIO_REVIEWER],
        ];
    }

    public function __construct($id){
    	$this->id = $id;
    }
}
