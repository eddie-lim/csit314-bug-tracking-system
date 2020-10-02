<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug_document".
 *
 * @property int $id
 * @property int $bug_id
 * @property string|null $file_path
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class BugDocument extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bug_id'], 'required'],
            [['bug_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['status'], 'string'],
            [['file_path'], 'string', 'max' => 2056],
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
            'file_path' => 'File Path',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugDocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugDocumentQuery(get_called_class());
    }
}
