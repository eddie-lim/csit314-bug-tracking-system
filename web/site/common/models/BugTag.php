<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug_tag".
 *
 * @property int $id
 * @property int|null $bug_id
 * @property string|null $name
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 */
class BugTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bug_id', 'created_at', 'created_by'], 'integer'],
            [['delete_status'], 'string'],
            [['name'], 'string', 'max' => 128],
            [['id'], 'unique'],
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
            'name' => 'Name',
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugTagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugTagQuery(get_called_class());
    }
}
