<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug_comment".
 *
 * @property int $id
 * @property int|null $bug_id
 * @property string|null $comment
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class BugComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bug_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['comment', 'status'], 'string'],
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
            'comment' => 'Comment',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugCommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugCommentQuery(get_called_class());
    }
}
