<?php

namespace backend\controllers;

use Yii;
use common\models\Bug;
use common\models\BugComment;
use common\models\BugTag;
use common\models\search\BugSearch;
use backend\controllers\BugCommentController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\Expression;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

//jonny begins here
use yii\helpers\Html;


class StatisticsController extends Controller
{
    public function actionIndex(){

        $bugModel = new Bug();
        $tagModel = new BugTag();

        $dataProvider = new ArrayDataProvider([
            'allModels'=>Bug::find()->all(),
            'pagination'=>false,
        ]);

        $dataProviderPagination = new ArrayDataProvider([
            'allModels'=>Bug::find()->all(),
            'pagination'=> [
              'pageSize' => 20,
            ],
        ]);

        // jonny starts here
        $gridColumns = [
          ['class' => 'kartik\grid\SerialColumn'],
          //'id',
          [
            'attribute'=>'bug',
            'label'=>'Bug',
            'vAlign'=>'middle',
            'width'=>'190px',
            'value'=>function ($model, $key, $index, $widget) {
              return Html::a($model->title, '../bug/view?id='.$model->id, []);
            },
            'format'=>'raw'
          ],
          'description',
          'bug_status',
          'priority_level',
          'developer_user_id',
          ['class' => 'kartik\grid\ActionColumn', 'urlCreator'=>function(){return '#';}]
        ];

        return $this->render('index', [
            'actBugs' => $bugModel->getActiveBugsData(),
            'resBugs' => $bugModel->getResolvedBugsData(),
            'pendBugs' => $bugModel->getPendingBugsData(),
            'actBugPriority' => $bugModel->getPriorityLevelData(),
            'allBugStatus' => $bugModel->getBugStatusData(),
            'devStats' => $bugModel->getTopDeveloperData(),
            'reportedBugs' => $bugModel->getReportedBugsByMonth(),
            'resolvedBugs' => $bugModel->getResolvedBugsByMonth(),
            'curBugStatus' => $bugModel->getCurBugStatusData(),
            'bugTags' => $tagModel->getTopBugTags(),
            'dataProvider' => $dataProvider,
            'gridColumns' => $gridColumns,
            'dataProviderPagination' => $dataProviderPagination,
        ]);


    }
}
