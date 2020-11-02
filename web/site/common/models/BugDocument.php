<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use Exception;

use backend\models\BugCreationForm;
use common\components\MyCustomActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\behaviors\MyAuditTrailBehavior;
use \trntv\filekit\behaviors\UploadBehavior;

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
                    MyCustomActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            "blame" => [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    MyCustomActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                ],
            ],
            /* "upload" =>
             * [
             *     'class' => UploadBehavior::className(),
             *     'attribute' => 'upload_file',
             *     'pathAttribute' => 'path',
             *     'baseUrlAttribute' => 'base_url'
             * ], */
            "auditTrail" => MyAuditTrailBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['path', 'base_url'], 'safe'],
            [['bug_id', 'created_at', 'created_by'], 'integer'],
            [['delete_status'], 'string'],
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

    public function getBug()
    {
        return $this->hasOne(Bug::className(), [ 'id' => 'bug_id' ]);
    }

    public function getFullPath()
    {
        return $this->base_url . DIRECTORY_SEPARATOR . $this->path;
    }

    public function getPreviewData()
    {
        if ($this->getType() === 'text') {
            return file_get_contents($this->getFullPath());
        } else {
            return Yii::getAlias('@web') . '/' . $this->base_url . '/' . $this->path;
        }
    }

    public function getPreviewConfig()
    {
        return [
            'type' => $this->getType(),
            'caption' => $this->path,
            'key' => $this->id
        ];
    }

    public function getType()
    {
        $tokens = explode('.', $this->attributes['path']);
        switch (end($tokens)) {
            case 'txt': case 'csv': case 'json':
                return 'text';
            case 'pdf':
                return 'pdf';
            default:
                return 'image';
        }
    }

    public static function handleAjaxUpload($fileDetails)
    {
        $details = SELF::getAjaxFileDetails($fileDetails);
        $dir = BugCreationForm::getUserUploadDir();
        $result = move_uploaded_file(
            $details['src'], $dir . DIRECTORY_SEPARATOR . $details['name']
        );

        if ($result) {
            return [ 'success' => 'File uploaded successfully' ];
        } else {
            return [ 'error' => 'An error occurred while saving file' ];
        }
    }

    public static function handleAjaxImmediateUpload($fileDetails, $post)
    {
        $details = SELF::getAjaxFileDetails($fileDetails);
        $bugId = intval($post['bug_id']);

        // return error if document with same filename already loaded
        $matches = SELF::find()->where([
            'bug_id' => $bugId, 'path' => $details['name']
        ])->exists();
        if ($matches) return [ 'error' => 'File already uploaded' ];

        // return error if cannot move file to uploads directory
        $targetDir = 'uploads' . DIRECTORY_SEPARATOR . "bug_$bugId";
        $dest = $targetDir . DIRECTORY_SEPARATOR . $details['name'];
        FileHelper::createDirectory($targetDir);
        if (!move_uploaded_file($details['src'], $dest)) {
            return [ 'error' => 'An error occurred while saving file' ];
        };

        // delete uploaded file and return error if document cannot validate
        $document = SELF::makeModel($bugId, $details['name'], $targetDir);
        if (!$document->validate()) {
            FileHelper::unlink($dest);
            return [ 'error' => 'An error occurred while creating a new db record' ];
        }

        $document->save();
        return [ 'success' => 'File uploaded successfully' ];
    }

    public static function handleAjaxRemove($dir, $post)
    {
        try {
            if (isset($post['delete_all'])) {
                foreach(FileHelper::findFiles($dir) as $file) {
                    if (!FileHelper::unlink($file)) {
                        throw new Exception("Unable to delete $file");
                    }
                }
            } else {
                $path = $dir . DIRECTORY_SEPARATOR . $post['filename'];
                if (!FileHelper::unlink($path)) {
                    throw new Exception("Unable to delete $path");
                }
            }
        } catch (Exception $e) {
            return [ 'error' => $e->getMessage() ];
        }

        return [ 'success' => 'File(s) deleted successfully' ];
    }

    public static function handleAjaxImmediateRemove($post)
    {
        if (isset($post['immediate'])) {    // document uploaded at current time
            $doc = SELF::findOne([
                'bug_id' => intval($post['bug_id']), 'path' => $post['filename'] ]
            );
        } else {                            // document uploaded prior
            $doc = SELF::findOne(intval($post['key']));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $doc->delete();
            $file = $doc->attributes['base_url'] . DIRECTORY_SEPARATOR .
                    $doc->attributes['path'];
            if (!FileHelper::unlink($file)) {
                throw new Exception("Unable to delete $file");
            }

        } catch (Exception $e) {
            $transaction->rollback();
            return [ 'error' => $e->getMessage() ];
        }

        $transaction->commit();
        return [ 'success' => 'File deleted successfully' ];
    }

    private static function getAjaxFileDetails($details)
    {
        foreach ($details as $key => $value) {
            if ($key == 'name') {
                $name = $value['documents'][0];
            } else if ($key == 'tmp_name') {
                $src = $value['documents'][0];
            }
        }
        return [ 'name' => $name, 'src' => $src ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugDocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugDocumentQuery(get_called_class());
    }

    public static function makeModel($bugId, $path, $baseUrl)
    {
        $bugDoc = new BugDocument();
        $bugDoc->bug_id = $bugId;
        $bugDoc->path = $path;
        $bugDoc->base_url = $baseUrl;
        return $bugDoc;
    }
}
