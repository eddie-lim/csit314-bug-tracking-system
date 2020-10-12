<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bug_tag".
 *
 * @property int $id
 * @property int|null $bug_id
 * @property string|null $name
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 */
class BugTag extends \common\components\MyCustomActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_tag';
    }

    public function behaviors()
    {
        return [
            "timestamp" => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            "blame" => [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                ],
            ],
            "auditTrail" => \common\behaviors\MyAuditTrailBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bug_id', 'created_at', 'created_by'], 'integer'],
            [['delete_status'], 'string'],
            [['name'], 'string', 'max' => 128],
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
            'name' => 'Name',
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function getBug()
    {
        return $this->hasOne(Bug::className(), [ 'id' => 'bug_id' ]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugTagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugTagQuery(get_called_class());
    }

    public static function getTopBugTags(){
        return SELF::find()
                ->select(['name', 'COUNT(id) AS counter'])
                ->groupBy('name')
                ->orderBy(['counter'=>SORT_DESC])
                ->limit(3)
                ->asArray()
                ->all();
    }

    public static function makeModel($bugId, $name)
    {
        $bugTag = new BugTag();
        $bugTag->bug_id = $bugId;
        $bugTag->name = strtolower($name);
        return $bugTag;
    }
}
