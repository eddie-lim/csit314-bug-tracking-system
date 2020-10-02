<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug".
 *
 * @property int $id
 * @property string $title
 * @property string $summary
 * @property string $bug_status
 * @property string|null $notes
 * @property string $pirority_level
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class Bug extends \yii\db\ActiveRecord
{
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
            [['title', 'summary', 'bug_status', 'pirority_level'], 'required'],
            [['summary', 'bug_status', 'pirority_level', 'status'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
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
            'summary' => 'Summary',
            'bug_status' => 'Bug Status',
            'notes' => 'Notes',
            'pirority_level' => 'Pirority Level',
            'status' => 'Status',
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
}
