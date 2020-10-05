<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserActionHistory;
use common\models\SystemAuditTrail;
use common\models\search\SystemAuditTrailSearch;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ActivityLogController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function actionAuditTrailLog()
    {
        $searchModel = new SystemAuditTrailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=50;

        return $this->render('audit-trail-log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionAuditTrailLogDetail($id)
    {
        if (($model = SystemAuditTrail::findOne($id)) == null) {
            throw new NotFoundHttpException('The requested page does not exist.');    
        }        

        return $this->render('audit-trail-log-detail', [
            'model' => $model,
        ]);
    }

}
