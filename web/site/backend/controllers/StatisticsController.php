<?php

namespace backend\controllers;

use Yii;
use common\models\Bug;
use common\models\BugComment;
use common\models\BugTag;
use backend\models\ExportForm;
use common\models\search\BugSearch;
use backend\controllers\BugCommentController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\Expression;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

//jonny begins here
use yii\helpers\Html;


class StatisticsController extends Controller
{
    public function actionIndex(){

        $selection = "";
        $result = "";
        $bugModel = new Bug();
        $tagModel = new BugTag();
        $exportModel = new ExportForm();

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

        /*
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
        ];*/

        if ($exportModel->load(Yii::$app->request->post()) && $exportModel->validate()){
            $ranges = explode(" - ", $exportModel->date_range);
            $start_range = strtotime($ranges[0]);
            $end_range = strtotime($ranges[1]);

            if(isset($_POST['repb'])){
                $result = $bugModel->getReportedBugs($start_range, $end_range);
                $selection = "repb";
            }
            if(isset($_POST['resb'])){
                $result = $bugModel->getResolvedBugs($start_range, $end_range);
                $selection = "resb";
            }
            if(isset($_POST['topr'])){
                $result = new ArrayDataProvider([
                    'allModels' => $bugModel->getTopReporterData($start_range, $end_range)
                ]);
                $selection = "topr";
            }
            if(isset($_POST['topd'])){
                $result = new ArrayDataProvider([
                    'allModels' => $bugModel->getTopDeveloperData($start_range, $end_range)
                ]);
                $selection = "topd";
            }
        }

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
            'result' => $result,
            'selection' => $selection,
            'exportModel' => $exportModel,
            'dataProvider' => $dataProvider,
            //'gridColumns' => $gridColumns,
            'dataProviderPagination' => $dataProviderPagination,
        ]);


    }
}
