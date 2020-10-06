<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bug_document".
 *
 * @property int $id
 * @property int $bug_id
 * @property string|null $file_path
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 */
class BugDocument extends \common\components\MyCustomActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_document';
    }

    public function behaviors()
    {
        return [
            "timestamp" => [
                'class' => yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            "blame" => [
                'class' => yii\behaviors\BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                ],
            ],
            "upload" =>
            [
                'class' => trntv\filekit\behaviors\UploadBehavior::className(),
                'attribute' => 'upload_file',
                'pathAttribute' => 'path',
                'baseUrlAttribute' => 'base_url'
            ],
            "auditTrail" => common\behaviors\MyAuditTrailBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['bug_id', 'created_at', 'created_by'], 'integer'],
            [['delete_status'], 'string'],
            [['file_path'], 'string', 'max' => 2056],
        ], parent::rules());
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
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
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
