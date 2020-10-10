<?php

namespace backend\controllers;

use Yii;
use common\models\Bug;
use common\models\BugComment;
use common\models\search\BugSearch;
use backend\controllers\BugCommentController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\Expression;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

class StatisticsController extends Controller
{
    public function actionIndex(){
        $actBugs = Bug::find()->where('bug_status NOT IN ("pending_review", "Rejected", "Completed")')
                          ->all();

        $resBugs = Bug::find()->where(['bug_status'=>'Completed'])->all();
        $pendBugs = Bug::find()->where(['bug_status'=>'pending_review'])->all();

        $devStats = Bug::find()->select(['COUNT(*) AS counter', 'developer_user_id'])
                            ->where(['bug_status'=>'Completed'])
                            ->groupBy('developer_user_id')
                            ->orderBy(['counter'=>SORT_DESC])
                            ->asArray()
                            ->limit(3)
                            ->all();


        $allBugStatus = Bug::find()->select(['COUNT(*) AS counter', 'bug_status'])
                         ->groupBy('bug_status')
                         ->asArray()
                         ->all();

        $curBugStatus = Bug::find()
                         ->select(['bug_status', 'created_at', 'COUNT(id) AS counter'])
                         ->where(['FROM_UNIXTIME(created_at, "%m-%Y")' => date('m-Y')])
                         ->groupBy('bug_status')
                         ->asArray()
                         ->all();

        $actBugPriority = Bug::find()->select(['COUNT(*) AS counter', 'priority_level'])
                         ->where('bug_status NOT IN ("Rejected", "Completed")')
                         ->groupBy('priority_level')
                         ->asArray()
                         ->all();

        $reportedBugs = Bug::find()
                ->select(['FROM_UNIXTIME(created_at, "%m-%Y") AS m_date', 'COUNT(id) AS counter'])
                ->where('bug_status NOT IN ("Rejected", "Completed")')
                ->groupBy('m_date')
                ->asArray()
                ->all();

        $resolvedBugs = Bug::find()
                ->select(['FROM_UNIXTIME(created_at, "%m-%Y") AS m_date', 'COUNT(id) AS counter'])
                ->where(['bug_status'=>'Completed'])
                ->groupBy('m_date')
                ->asArray()
                ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels'=>Bug::find()->all(),
            'pagination'=>false,
        ]);

        return $this->render('index', [
            'actBugs' => $actBugs,
            'resBugs' => $resBugs,
            'pendBugs' => $pendBugs,
            'actBugPriority' => $actBugPriority,
            'allBugStatus' => $allBugStatus,
            'devStats' => $devStats,
            'reportedBugs' => $reportedBugs,
            'resolvedBugs' => $resolvedBugs,
            'curBugStatus' => $curBugStatus,
            'dataProvider' => $dataProvider,
        ]); 
    }
}
