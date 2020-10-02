<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug_action".
 *
 * @property int $id
 * @property int|null $bug_id
 * @property string|null $action_type
 * @property string|null $notes
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class BugAction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bug_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['action_type', 'notes', 'status'], 'string'],
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
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
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
}
