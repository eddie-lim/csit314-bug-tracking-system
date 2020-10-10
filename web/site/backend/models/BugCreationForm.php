<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Bug;
use common\models\BugDocument;
use common\models\BugTag;

class BugCreationForm extends Model
{
    public $title;
    public $description;
    public $notes;
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
            [ ['notes'], 'string', 'max' => 1028 ],
            [ ['documents'], 'safe' ],
            [ ['tags'], 'each', 'rule' => [ 'string', 'max' => 15 ] ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'notes' => 'Notes',
            'documents' => 'Upload Supporting Documents',
            'tags' => 'Tags',
        ];
    }

    public function createBug()
    {
        $bug = $this->buildNewBug();

        if ($bug->save()) {
            $this->newBugId = $bug->id;

            if (!empty($this->documents)) {
                foreach ($this->documents as $doc) {
                    $filepath = $this->copyToUploadsDir($doc);
                    $bugDocument = $this->buildNewBugDocument($filepath);
                    $bugDocument->save();
                }
            }

            if (!empty($this->tags)) {
                foreach ($this->tags as $idx => $name) {
                    $bugTag = new BugTag();
                    $bugTag->bug_id = $bug->id;
                    $bugTag->name = $name;
                    $bugTag->save();
                }
            }

            return true;
        } else {
            return false;
        }
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

    private function buildNewBug()
    {
        $bug = new Bug();
        $bug->title = $this->title;
        $bug->description = $this->description;
        $bug->notes = $this->notes;
        $bug->bug_status = Bug::BUG_STATUS_NEW;
        $bug->priority_level = Bug::PRIORITY_LOW;
        return $bug;
    }

    private function buildNewBugDocument($filepath)
    {
        $bugDoc = new BugDocument();
        $bugDoc->bug_id = $this->getNewBugId();
        $bugDoc->path = $filepath['path'];
        $bugDoc->base_url = $filepath['base_url'];
        return $bugDoc;
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
