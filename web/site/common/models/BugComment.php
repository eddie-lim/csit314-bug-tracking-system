<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug_comment".
 *
 * @property int $id
 * @property int|null $bug_id
 * @property string|null $comment
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 */
class BugComment extends \common\components\MyCustomActiveRecord
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
            [['bug_id', 'created_at', 'created_by'], 'integer'],
            [['comment', 'delete_status'], 'string'],
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
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
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
