<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\Bug;
use common\models\BugTag;
use common\models\BugComment;
use common\models\BugDocument;
use common\models\search\BugSearch;
use common\models\search\BugCommentSearch;

use common\components\MyCustomActiveRecord;

use backend\models\BugCreationForm;
use backend\models\BugTaskForm;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;

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

    public function actionTasks()
    {
        $searchModel = new BugSearch();

        if (Yii::$app->user->can(User::ROLE_REVIEWER)){
          $searchModel->setFilterBy([Bug::BUG_STATUS_PENDING_REVIEW]);
        } elseif (Yii::$app->user->can(User::ROLE_TRIAGER)){
          $searchModel->setFilterByNewUnassigned(true);
        } elseif (Yii::$app->user->can(User::ROLE_DEVELOPER)) {
          $searchModel->setFilterBy([Bug::BUG_STATUS_ASSIGNED, Bug::BUG_STATUS_REOPEN]);
          $searchModel->setAssignedTo(Yii::$app->user->getID());
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'page'=>'tasks',
        ]);
    }

    public function actionClosed() {
      $searchModel = new BugSearch();
      $searchModel->setFilterBy([Bug::BUG_STATUS_COMPLETED]);
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'page' => 'closed',
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
            'page'=>'index',
        ]);
    }

    /**
     * Displays a single Bug model.
     * @param integer $id
     * @return mixed
     */

    public function actionDownload($fpath){
        if(file_exists($fpath)){
            Yii::$app->response->sendFile($fpath);
        }
    }

    public function actionView($id)
    {
        $newComment = new BugComment();
        if ($newComment->load(Yii::$app->request->post()) && $newComment->save()){
          $newComment = new BugComment();
        }

        $searchModel = new BugCommentSearch();
        $searchModel->setBugId($id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $taskModel = new BugTaskForm($id);

        $availableDevelopers = User::getAvailableDevelopers();

        BugCreationForm::mkUserUploadDir();
        return $this->render('view',[
          'model' => $this->findModel($id),
          'dataProvider' => $dataProvider,
          'comment' => $newComment,
          'taskModel' => $taskModel,
          'availableDevelopers' => $availableDevelopers,
        ]);
    }

    public function actionCreateTag(){
      // check for isAjax
      if(!Yii::$app->request->isAjax){
        return $this->goBack();
      }
      $success = false;
      $model = null;
      $errors = [];

      $model = new BugTag();

      if ($model->load(Yii::$app->request->post())) {
        $success = $model->save();
      }
      if ($model->hasErrors()) {
        $errors = $model->errors;
      }
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return [
        'success' => (bool)$success,
        'model' => $model->attributes,
        'errors'=> $errors,
      ];
    }

    public function actionDeleteTag(){
      // check for isAjax
      if(!Yii::$app->request->isAjax){
        return $this->goBack();
      }
      $success = false;
      $model = null;
      $errors = [];

      if(isset(Yii::$app->request->post()['id'])){
        $model = BugTag::findOne(Yii::$app->request->post()['id']);
        if($model){
          $model->delete_status = MyCustomActiveRecord::DELETE_STATUS_DISABLED;
          $success = $model->save();
        }
      } else {
        $errors = array('id' => 'id cannot be empty');
      }

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return [
        'success' => (bool)$success,
        'model' => $model,
        'errors'=> $errors,
      ];
    }

    public function actionProcessInteraction($id = null){
      // check for id ！= null and isAjax
      if(is_null($id) || !Yii::$app->request->isAjax){
        return $this->goBack();
      }
      $success = false;
      $model = null;
      $errors = [];

      $taskModel = new BugTaskForm($id);
      if (Yii::$app->user->can(User::ROLE_REVIEWER)){
        $taskModel->scenario = User::ROLE_REVIEWER;
      } elseif (Yii::$app->user->can(User::ROLE_TRIAGER)){
        $taskModel->scenario = User::ROLE_TRIAGER;
      } elseif (Yii::$app->user->can(User::ROLE_DEVELOPER)){
        $taskModel->scenario = User::ROLE_DEVELOPER;
      }
      if($taskModel->load(Yii::$app->request->post()) && $taskModel->validate()){
        if($taskModel->process()){
          $model = $taskModel->model->toObject();
          $success = true;
        }
      }
      if($taskModel->model){
        if($taskModel->model->hasErrors()){
          $taskModel->addErrors($taskModel->model->getErrors());
        }
      }
      if($taskModel->hasErrors()){
        $errors = $taskModel->errors;
      }

      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return [
        'success' => (bool)$success,
        'model' => $model,
        'errors'=> $errors,
      ];
    }

    /**
     * Creates a new Bug model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BugCreationForm();

        if ($model->load($_POST) && $model->createBug()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Bug created successfully!'
            ]);
            return $this->redirect([ 'view', 'id' => $model->getNewBugId() ]);
        }

        BugCreationForm::mkUserUploadDir();
        return $this->render('create', [ 'model' => $model ]);
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
      // TODO:: soft delete
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Handles ajax request for uploading bug documents
     */
    public function actionUploadFile()
    {
        if (!Yii::$app->request->isAjax) return $this->redirect(['index']);
        if (empty($_FILES['BugCreationForm'])) {
            return json_encode([ 'error' => 'No file loaded' ]);
        }

        $details = $_FILES['BugCreationForm'];
        if (isset($_POST['immediate'])) {
            return json_encode(
                BugDocument::handleAjaxImmediateUpload($details, $_POST)
            );
        } else {
            return json_encode(BugDocument::handleAjaxUpload($details));
        }
    }

    /**
     * Handles ajax request for removing bug documents
     */
    public function actionRemoveFile()
    {
        if (!Yii::$app->request->isAjax) return $this->redirect(['index']);
        if (isset($_POST['has_error']) && $_POST['has_error'] === "true") {
            // do nothing; if POST has error, file was not uploaded to filesystem
            return json_encode([]);
        }

        $dir = BugCreationForm::getUserUploadDir();
        if (isset($_POST['key']) || isset($_POST['immediate'])) {
            return json_encode(BugDocument::handleAjaxImmediateRemove($_POST));
        } else {
            return json_encode(BugDocument::handleAjaxRemove($dir, $_POST));
        }
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

    public function actionTest($id)
    {
        $model = $this->findModel($id);

        BugCreationForm::mkUserUploadDir();
        return $this->render('test', [ 'model' => $model ]);
    }
}
