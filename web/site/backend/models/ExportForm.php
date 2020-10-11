<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Bug;
use common\models\BugDocument;
use common\models\BugTag;

class ExportForm extends Model
{
    public $date_range;

    public function rules()
    {
        return [
            [['date_range'], 'required'],
        ];
    }
}
