<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use common\models\Bug;
use common\models\BugDocument;

class BugCreationForm extends Model
{
    public $title;
    public $description;
    public $notes;
    public $documents;

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
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'notes' => 'Notes',
            'documents' => 'Upload Supporting Documents',
        ];
    }

    public function create()
    {
        $bug = $this->buildNewBug();

        if ($bug->save()) {
            $this->newBugId = $bug->id;
            foreach ($this->documents as $doc) {
                // $this->saveDocument($doc);
                $bugDocument = $this->buildNewBugDocument($bug->id, $doc->name);
                // $bugDocument->setAttribute('path', $doc->name);
                // $bugDocument->setAttribute('base_url', 'uploads');
                // $bugDocument->save();

                $details = [
                    'vars' => get_object_vars($bugDocument),
                    'methods' => get_class_methods($bugDocument)
                ];

                file_put_contents(
                    'uploads/log',
                    json_encode($details, JSON_PRETTY_PRINT),
                    FILE_APPEND
                );
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

    private function buildNewBugDocument($bugId, $path)
    {
        $bugDoc = new BugDocument();
        $bugDoc->bug_id = $bugId;
        $bugDoc->path = $path;
        $bugDoc->base_url = 'uploads';
        return $bugDoc;
    }

    private function saveDocument($doc)
    {
        $dir = 'uploads';
        $base = $doc->baseName;
        $ext = $doc->extension;
        $N = "";

        while (file_exists("$dir/$base$N.$ext")) {
            $N = strval(intval($N) + 1);
        }
        $doc->saveAs("$dir/$base$N.$ext");
    }
}
