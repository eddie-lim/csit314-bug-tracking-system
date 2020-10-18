<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\Bug;
use common\models\BugComment;
use common\models\search\BugSearch;
use common\models\search\BugCommentSearch;

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

        return $this->render('view',[
          'model' => $this->findModel($id),
          'dataProvider' => $dataProvider,
          'comment' => $newComment,
          'taskModel' => $taskModel,
          'availableDevelopers' => $availableDevelopers,
        ]);
    }

    public function actionCreateTag() {
      $model = new BugTag();
      if ($model->load($_POST) && $model->save()) {
        print_r("success");
        exit();
      }
      if ($model->hasErrors()) {
        print_r($model->getErrors());
        exit();
      }
    }

    public function actionDeleteTag() {
      $tag = BugTag::findOne($_POST['id']);
      if($tag){
        $tag->updateAttributes(['delete_status'=>'disabled']);
      }
    }
    public function actionProcessInteraction($id = null){
      // check for id ！= null and isAjax
      if(is_null($id) || !Yii::$app->request->isAjax){
        // return $this->goBack();
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
        if (empty($_FILES['BugCreationForm'])) return [
            'error' => 'File not loaded'
        ];

        $details = $_FILES['BugCreationForm'];
        foreach ($details as $key => $value) {
            if ($key == 'name') {
                $name = $value['documents'][0];
            } else if ($key == 'tmp_name') {
                $src = $value['documents'][0];
            }
        }

        $dir = BugCreationForm::getUserUploadDir();
        if (move_uploaded_file($src, $dir . DIRECTORY_SEPARATOR . $name)) {
            return json_encode([ 'success' => "Uploaded $name" ]);
        } else {
            return json_encode([
                'error' => 'An error occurred while saving file'
            ]);
        };
    }

    /**
     * Handles ajax request for removing bug documents
     */
    public function actionRemoveFile()
    {
        if (!Yii::$app->request->isAjax) return $this->redirect(['index']);

        $dir = BugCreationForm::getUserUploadDir();
        if (isset($_POST['delete_all'])) {
            foreach(FileHelper::findFiles($dir) as $file) {
                FileHelper::unlink($file);
            }
            return json_encode([ 'status' => $files ]);
        } else {
            FileHelper::unlink($dir . DIRECTORY_SEPARATOR . $_POST['filename']);
        }

        return json_encode([ 'status' => 'delete complete' ]);
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
