<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $bug_status
 * @property string $priority_level
 * @property int|null $developer_user_id
 * @property string|null $notes
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class Bug extends \common\components\MyCustomActiveRecord
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
        return 'bug';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'bug_status', 'priority_level'], 'required'],
            [['description', 'bug_status', 'priority_level', 'delete_status'], 'string'],
            [['developer_user_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['notes'], 'string', 'max' => 1028],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'bug_status' => 'Bug Status',
            'priority_level' => 'Priority Level',
            'developer_user_id' => 'Developer User ID',
            'notes' => 'Notes',
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugQuery(get_called_class());
    }

    public static function getAllBugStatus() {      
      return [
          SELF::BUG_STATUS_NEW => "status: new",
          SELF::BUG_STATUS_ASSIGNED => "status: assigned",
          SELF::BUG_STATUS_FIXING => "status: fixing",
          SELF::BUG_STATUS_PENDING_REVIEW => "status: pending_review",
          SELF::BUG_STATUS_COMPLETED => "status: completed",
          SELF::BUG_STATUS_REJECTED => "status: rejected",
          SELF::BUG_STATUS_REOPEN => "status_reopen",
      ];
    }
}
