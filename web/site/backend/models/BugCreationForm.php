<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Bug;
use common\models\BugDocument;
use common\models\BugTag;
use Exception;

class BugCreationForm extends Model
{
    public $title;
    public $description;
    public $documents;
    public $tags;

    private $newBugId;

    public function behaviours()
    {
        return [
            'document' => [
                'class' => 'trntv\filekit\behaviors\UploadBehavior',
                'multiple' => true,
                'attribute' => 'documents',
            ]
        ];
    }

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
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $err = new Exception('An error occurred while saving bug.');
            $bug = Bug::makeModel($this->title, $this->description);

            if ($bug->save()) {
                $this->newBugId = $bug->id;

                if (!empty($this->documents)) {
                    foreach ($this->documents as $doc) {
                        $filepath = $this->copyToUploadsDir($doc);
                        $bugDocument = BugDocument::makeModel(
                            $bug->id, $filepath['path'], $filepath['base_url']
                        );
                        if (!$bugDocument->save()) throw $err;
                    }
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
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => $e->getMessage()
            ]);
            return false;
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

    private function copyToUploadsDir($doc)
    {
        $dir = 'uploads/bug_' . strval($this->getNewBugId());
        if (!file_exists($dir)) {
            exec("mkdir -p $dir");
        }

        $src = Yii::getAlias('@storage/web/source') . '/' . $doc['path'];
        $dest = $this->getFilename($dir, $doc['name']);
        exec("mv -f $src $dir/$dest");

        return [ 'path' => $dest, 'base_url' => $dir ];
    }

    private function getFilename($dir, $name)
    {
        $tokens = explode('.', $name);
        $ext = array_pop($tokens);
        $base = implode($tokens);
        $N = "";

        while (file_exists("$dir/$base$N.$ext")) {
            $N = strval(intval($N) + 1);
        }
        return "$base$N.$ext";
    }
}
