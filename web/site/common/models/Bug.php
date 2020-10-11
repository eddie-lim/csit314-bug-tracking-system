<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bug".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $bug_status
 * @property string $priority_level
 * @property int|null $developer_user_id
 * @property string|null $notes
 * @property string $delete_status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 */
class Bug extends \common\components\MyCustomActiveRecord
{
    const BUG_STATUS_NEW = "new";
    const BUG_STATUS_ASSIGNED = "assigned";
    const BUG_STATUS_FIXING = "fixing";
    const BUG_STATUS_PENDING_REVIEW = "pending_review";
    const BUG_STATUS_COMPLETED = "completed";
    const BUG_STATUS_REJECTED ="rejected";
    const BUG_STATUS_REOPEN = "reopen";

    const PRIORITY_LOW = '1';
    const PRIORITY_MED = '2';
    const PRIORITY_HIGH = '3';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'bug_status', 'priority_level'], 'required'],
            [['description', 'bug_status', 'priority_level', 'delete_status'], 'string'],
            [['developer_user_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['notes'], 'string', 'max' => 1028],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'bug_status' => 'Bug Status',
            'priority_level' => 'Priority Level',
            'developer_user_id' => 'Developer User ID',
            'notes' => 'Notes',
            'delete_status' => 'Delete Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getDocuments()
    {
        return $this->hasMany(BugDocument::className(), [ 'bug_id' => 'id' ]);
    }

    public function getTags()
    {
        return $this->hasMany(BugTag::className(), [ 'bug_id' => 'id' ]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\BugQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\BugQuery(get_called_class());
    }

    public static function getAllBugStatus() {
      return [
          SELF::BUG_STATUS_NEW => "status: new",
          SELF::BUG_STATUS_ASSIGNED => "status: assigned",
          SELF::BUG_STATUS_FIXING => "status: fixing",
          SELF::BUG_STATUS_PENDING_REVIEW => "status: pending_review",
          SELF::BUG_STATUS_COMPLETED => "status: completed",
          SELF::BUG_STATUS_REJECTED => "status: rejected",
          SELF::BUG_STATUS_REOPEN => "status_reopen",
      ];
    }

    public static function getActiveBugsData() {
        return SELF::find()
            ->where('bug_status NOT IN ("pending_review", "Rejected", "Completed")')
            ->all();
    }

    public static function getResolvedBugsData() {
        return SELF::find()->where(['bug_status'=>'Completed'])->all();
    }

    public static function getPendingBugsData() { 
        return SELF::find()->where(['bug_status'=>'pending_review'])->all();
    }

    public static function getTopDeveloperData() {
        return SELF::find()->select(['COUNT(*) AS counter', 'developer_user_id'])
                        ->where(['bug_status'=>'Completed'])
                        ->groupBy('developer_user_id')
                        ->orderBy(['counter'=>SORT_DESC])
                        ->asArray()
                        ->limit(3)
                        ->all();
    }


    public static function getTopReporterData($st, $et){
        return SELF::find()->select(['COUNT(*) AS counter', 'created_by'])
                           ->where('bug_status NOT IN ("Rejected")')
                           ->andWhere(['>=', 'created_at', $st])
                           ->andWhere(['<=', 'created_at', $et])
                           ->groupBy('created_by')
                           ->orderBy(['counter'=>SORT_DESC])
                           ->asArray()
                           ->limit(3)
                           ->all(); 
    }

    public static function getBugStatusData(){
        return SELF::find()->select(['COUNT(*) AS counter', 'bug_status'])
                         ->groupBy('bug_status')
                         ->asArray()
                         ->all();
    }
    public static function getCurBugStatusData(){
        return SELF::find()
                 ->select(['bug_status', 'created_at', 'COUNT(id) AS counter'])
                 ->where(['FROM_UNIXTIME(created_at, "%m-%Y")' => date('m-Y')])
                 ->groupBy('bug_status')
                 ->asArray()
                 ->all();
    }

    public static function getPriorityLevelData(){
        return SELF::find()->select(['COUNT(*) AS counter', 'priority_level'])
                         ->where('bug_status NOT IN ("Rejected", "Completed")')
                         ->groupBy('priority_level')
                         ->asArray()
                         ->all();
    }

    public static function getReportedBugsByMonth(){
        return SELF::find()
                ->select(['FROM_UNIXTIME(created_at, "%m-%Y") AS m_date', 'COUNT(id) AS counter'])
                ->where('bug_status NOT IN ("Rejected", "Completed")')
                ->groupBy('m_date')
                ->asArray()
                ->all();
    }

    public static function getResolvedBugsByMonth(){
        return SELF::find()
                ->select(['FROM_UNIXTIME(created_at, "%m-%Y") AS m_date', 'COUNT(id) AS counter'])
                ->where(['bug_status'=>'Completed'])
                ->groupBy('m_date')
                ->asArray()
                ->all();
    }

    public static function getReportedBugs($start_at, $end_at){
        return SELF::find()
                ->select(['created_at', 'COUNT(id) AS counter'])
                ->where(['>=', 'created_at', $start_at])
                ->andWhere(['<=', 'created_at', $end_at])
                ->count(); 
    }

    public static function getResolvedBugs($start_at, $end_at){
        return SELF::find()
                ->select(['updated_at', 'COUNT(id) AS counter'])
                ->where(['>=', 'updated_at', $start_at])
                ->andWhere(['<=', 'updated_at', $end_at])
                ->andWhere(['bug_status'=>'Completed'])
                ->count(); 
    }
}
