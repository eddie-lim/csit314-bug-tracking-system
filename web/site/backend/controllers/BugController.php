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

use yii\data\ArrayDataProvider;

/**
 * BugController implements the CRUD actions for Bug model.
 */
class BugController extends Controller
{

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionTasks() {
      $searchModel = new BugSearch();

      //$userRole = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->getID()))[0];
      if (Yii::$app->user->can('reviewer')) $searchModel->setFilterBy([Bug::BUG_STATUS_PENDING_REVIEW]);
      if (Yii::$app->user->can('triager')) $searchModel->setFilterBy([Bug::BUG_STATUS_NEW, Bug::BUG_STATUS_REOPEN, Bug::BUG_STATUS_REJECTED]);
      if (Yii::$app->user->can('developer')) $searchModel->setAssignedTo(Yii::$app->user->getID());;

      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
      ]);
    }
    /**
     * Lists all Bug models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BugSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bug model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $newComment = new BugComment();

        if ($newComment->load(Yii::$app->request->post()) && $newComment->save()){
            $newComment = new BugComment();
        }

        $commentData = BugComment::findAll(['bug_id'=>$id]);
        $provider = new ArrayDataProvider([
            'allModels' => $commentData,
        ]);

        return $this->render('view',
            [
                'model' => $this->findModel($id),
                'dataProvider' => $provider,
                'comment' => $newComment,
            ]);
    }

    /**
     * Creates a new Bug model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bug();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Bug model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Bug model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bug model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bug the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bug::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
