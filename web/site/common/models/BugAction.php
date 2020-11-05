<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bug_action".
 *
 * @property int $id
 * @property int|null $bug_id
 * @property string|null $action_type
 * @property string|null $notes
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 */
class BugAction extends \common\components\MyCustomActiveRecord
{
    const BUG_STATUS_NEW = "new";
    const BUG_STATUS_ASSIGNED = "assigned";
    const BUG_STATUS_FIXING = "fixing";
    const BUG_STATUS_PENDING_REVIEW = "pending_review";
    const BUG_STATUS_COMPLETED = "completed";
    const BUG_STATUS_REJECTED ="rejected";
    const BUG_STATUS_REOPEN = "reopen";

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_action';
    }
    
    public function behaviors()
    {
        return [
            "timestamp" => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            "blame" => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                ],
            ],
            "auditTrail" => \common\behaviors\MyAuditTrailBehavior::className(),  
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['bug_id', 'validateBugExists'],
            [['bug_id', 'created_at', 'created_by'], 'integer'],
            ['created_by', 'validateUserExists'],
            [['action_type', 'delete_status'], 'string'],
            ['notes', 'string', 'max' => 1028],
            ['action_type', 'in', 'range' => [ 
                'new', 'assigned', 'fixing', 'pending_review', 'completed', 'rejected', 'reopen'
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bug_id' => 'Bug ID',
            'action_type' => 'Action Type',
            'notes' => 'Notes',
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugActionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugActionQuery(get_called_class());
    }

    public function validateUserExists($attribute, $params, $validator)
    {
        $userExists = User::find()->where([ 'id' => $this->$attribute ])->exists();
        if (!$userExists) {
            $this->addError($attribute, "$attribute must refer to existing user");
        }
    }

    public function validateBugExists($attribute, $params, $validator)
    {
        $bugExists = Bug::find()->where([ 'id' => $this->$attribute ])->exists();
        if (!$bugExists) {
            $this->addError($attribute, "$attribute must refer to existing bug");
        }
    }

    public static function makeModel($bug_id, $action_type){
        $m = new SELF();
        $m->bug_id = $bug_id;
        $m->action_type = $action_type;

        return $m;
    }
}
