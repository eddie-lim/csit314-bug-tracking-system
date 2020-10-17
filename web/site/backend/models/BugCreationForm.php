<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use common\models\Bug;
use common\models\BugDocument;
use common\models\BugTag;
use Exception;

class BugCreationForm extends Model
{
    const USER_UPLOAD_BASEPATH = "uploads\\temp\\user_";

    public $title;
    public $description;
    public $documents;
    public $tags;

    private $newBugId;

    public function rules()
    {
        return [
            [ ['title'], 'required' ],
            [ ['title'], 'string', 'max' => 128 ],
            [ ['description'], 'required' ],
            [ ['description'], 'string' ],
            [ ['documents'], 'safe' ],
            [ ['tags'], 'each', 'rule' => [ 'string', 'max' => 15 ] ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'documents' => 'Upload Supporting Documents',
            'tags' => 'Tags',
        ];
    }

    public function createBug()
    {
        $dir = SELF::getUserUploadDir();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $err = new Exception('An error occurred while saving bug.');
            $bug = Bug::makeModel($this->title, $this->description);

            if ($bug->save()) {
                $this->newBugId = $bug->id;

                foreach (FileHelper::findFiles($dir) as $path) {
                    $filename = str_replace("$dir\\", "", $path);
                    $bugDocument = $this->addBugDocument($bug->id, $dir, $filename);
                    if (!$bugDocument->save()) throw $err;
                }

                if (!empty($this->tags)) {
                    foreach ($this->tags as $idx => $name) {
                        $bugTag = BugTag::makeModel($bug->id, strtolower($name));
                        if (!$bugTag->save()) throw $err;
                    }
                }
            } else {
                throw $err;
            }

        } catch (Exception $e) {
            $transaction->rollback();
            FileHelper::removeDirectory("uploads\\bug_" . strval($this->newBugId));
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => $e->getMessage()
            ]);
            return false;

        } finally {
            FileHelper::removeDirectory($dir);
        }

        $transaction->commit();
        return true;
    }

    public function getNewBugId()
    {
        return $this->newBugId;
    }

    public function getCommonTags()
    {
        $tags = BugTag::find()
                      ->select([ 'name', 'count' => 'count(*)' ])
                      ->groupBy('name')
                      ->orderBy([ 'count' => SORT_DESC, 'name' => SORT_ASC ])
                      ->limit(10)->column();
        return array_combine($tags, $tags);
    }

    public static function mkUserUploadDir()
    {
        $dir = BugCreationForm::USER_UPLOAD_BASEPATH . strval(Yii::$app->user->getId());
        FileHelper::removeDirectory($dir);
        FileHelper::createDirectory($dir);
    }

    public static function getUserUploadDir()
    {
        return BugCreationForm::USER_UPLOAD_BASEPATH . strval(Yii::$app->user->getId());
    }

    private function addBugDocument($bugId, $sourceDir, $filename)
    {
        $targetDir = 'uploads/bug_' . strval($bugId);
        FileHelper::createDirectory($targetDir);
        rename("$sourceDir/$filename", "$targetDir/$filename");
        return BugDocument::makeModel($bugId, $filename, $targetDir);
    }

}
