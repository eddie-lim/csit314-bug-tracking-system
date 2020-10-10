<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
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

    public function rules()
    {
        return [
            [ ['title'], 'required' ],
            [ ['title'], 'string', 'max' => 128 ],
            [ ['description'], 'required' ],
            [ ['description'], 'string' ],
            [ ['notes'], 'string', 'max' => 1028 ],
            [ ['documents'], 'file', 'skipOnEmpty' => true,
              'extensions' => 'png, jpg, txt, csv, pdf', 'maxFiles' => 5 ],
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

            foreach ($this->documents as $doc) {
                $filepath = $this->writeFile($doc);
                $bugDocument = $this->buildNewBugDocument($bug->id, $filepath);
                $bugDocument->save();
            }

            foreach ($this->tags as $idx => $name) {
                $bugTag = new BugTag();
                $bugTag->bug_id = $bug->id;
                $bugTag->name = $name;
                $bugTag->save();
            }

            return true;
        } else {
            return false;
        }
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

    private function buildNewBugDocument($bugId, $filepath)
    {
        $bugDoc = new BugDocument();
        $bugDoc->bug_id = $bugId;
        $bugDoc->path = $filepath['path'];
        $bugDoc->base_url = $filepath['base_url'];
        return $bugDoc;
    }

    private function writeFile($doc)
    {
        $dir = 'uploads/bug_' . strval($this->getNewBugId());
        if (!file_exists($dir)) {
            exec("mkdir -p $dir");
        }
        $base = $doc->baseName;
        $ext = $doc->extension;
        $N = "";

        while (file_exists("$dir/$base$N.$ext")) {
            $N = strval(intval($N) + 1);
        }
        $doc->saveAs("$dir/$base$N.$ext");

        return [
            'path' => "$base$N.$ext",
            'base_url' => $dir,
        ];
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
}
