<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Accordion;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\models\Bug;
use common\models\User;

use kartik\select2\Select2;
use yii\web\JsExpression;

use rmrevin\yii\fontawesome\FAS;
use yii\widgets\ListView;

/**
* @var yii\web\View $this
* @var common\models\Bug $model
*/

$this->title = "#".$model->id." ".$model->title;
$this->params['breadcrumbs'][] = ['label' => 'Bugs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bug-view">
   <div class="row m-1 mb-2">
      <!-- tag -->
      <?php
         foreach ($model->tags as $tag) {
            echo Html::tag('button', Html::encode($tag->attributes['name']) . '&nbsp;&nbsp;x',
            [
               'class' => 'btn pl-2 pr-2 text-uppercase font-weight-normal badge badge-success ml-1',
               'onclick' => "", // remove self here and delete from db??
            ]);
         }
       ?>
   </div>

   <div class='card d-flex' style="background:none">
      <div class="col-12">
         <?php $taskForm = ActiveForm::begin(); ?>
            <div class="card mt-2">
               <div class="card-body">
                  <?php echo $taskForm->errorSummary($taskModel); ?>
                  <?php
                     if (Yii::$app->user->can(User::ROLE_DEVELOPER)){
                        if(($model->bug_status == Bug::BUG_STATUS_ASSIGNED || $model->bug_status == Bug::BUG_STATUS_REOPEN) && $model->developer_user_id && $model->developer_user_id == Yii::$app->user->id){
                           echo $taskForm->field($taskModel, 'accept')->textInput(['maxlength' => true]);
                        }
                       echo $taskForm->field($taskModel, 'notes')->textInput(['maxlength' => true]);
                     } elseif (Yii::$app->user->can(User::ROLE_TRIAGER)){
                        if($model->bug_status == Bug::BUG_STATUS_NEW){
                           echo $taskForm->field($taskModel, 'developer_user_id')->textInput(['maxlength' => true]);
                        }
                        echo $taskForm->field($taskModel, 'priority_level')->textarea(['rows' => 6]);
                        echo $taskForm->field($taskModel, 'status')->textarea(['rows' => 6]);
                        echo $taskForm->field($taskModel, 'notes')->textInput(['maxlength' => true]);
                     } elseif (Yii::$app->user->can(User::ROLE_REVIEWER)){
                        if($model->bug_status == Bug::BUG_STATUS_PENDING_REVIEW){
                           echo $taskForm->field($taskModel, 'status')->textarea(['rows' => 6]);
                           echo $taskForm->field($taskModel, 'notes')->textInput(['maxlength' => true]);
                        }
                     }
                  ?>
               </div>
               <div class="card-footer">
                   <?php echo Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
               </div>
            </div>
         <?php ActiveForm::end(); ?>
      </div>
      <div class="d-flex mr-1 mt-1 justify-content-center" style="margin-left:0.7%;width:98.5%"> <!-- no background cuz covered , this row is for assigned to and status-->
         <div class="col-8 d-flex align-items-start flex-column rounded" style="background:white">
            <span class="h6 p-2">
               Submitted by
               <?php
               echo Html::tag('span', Html::encode(User::findIdentity($model->created_by)->publicIdentity),
               ['class' => 'text-uppercase font-weight-normal badge badge-light']);
               ?>
               on
               <?= Html::tag('span', Html::encode(Yii::$app->formatter->asDateTime($model->created_at)),
               ['class' => 'text-uppercase font-weight-normal badge badge-light']); ?>
            </span>
            <!-- TODO:: show list view for the ticket's lifecycle -->
            <span class="h6 p-2">
               Updated by
               <?= Html::tag('span', Html::encode(User::findIdentity($model->updated_by)->publicIdentity),
               ['class' => 'text-uppercase font-weight-normal badge badge-light']); ?>
               on
               <?= Html::tag('span', Html::encode(Yii::$app->formatter->asDateTime($model->updated_at)),
               ['class' => 'text-uppercase font-weight-normal badge badge-light']); ?>
            </span>
         </div>

         <div class="col-4 d-flex align-items-end flex-column" style="background: white">
            <div class="text-left h-100">
               <div class="h2 pt-2">
                  Status
                  <!-- TODO:: use const for the case -->
                  <?php
                  switch ($model->bug_status) {
                     case "fixing":
                        $badge_type = "badge-warning";
                        break;
                     case "completed":
                        $badge_type = "badge-success";
                        break;
                     case "assigned":
                        $badge_type = "badge-info";
                        break;
                     default:
                        $badge_type = "badge-light";
                     // more cases here
                  }
                  echo Html::tag('span', Html::encode($model->bug_status),
                  ['class' => 'text-uppercase font-weight-normal badge '.$badge_type]);
                  ?>
               </div>
               <div class="h5 p-0">
                  Priority
                  <?php
                     switch ($model->priority_level) {
                        case 1:
                           $badge_type = "badge-info";
                           break;
                        case 2:
                           $badge_type = "badge-warning";
                           break;
                        case 3:
                           $badge_type = "badge-danger";
                           break;
                        default:
                           $badge_type = "badge-light";
                           break;
                     }
                     echo Html::tag('span', Html::encode($model->priority_level),
                     ['class' => 'badge pl-4 pr-4 badge-pill '.$badge_type])
                   ?>
               </div>
               <div class="h5 p-0">
                  Assigned
                  <?php
                  if ($model->developer_user_id == null) {
                     echo Html::tag('span', "Unassigned",
                     ['class' => 'badge badge-light']);
                  } else {
                     echo Html::tag('span', Html::encode(User::findIdentity($model->updated_by)->publicIdentity),
                     ['class' => 'badge badge-light']);
                  }
                  ?>
               </div>
             </div>
         </div>
      </div>
      <!-- bug description -->
      <div class="flex-row ml-1 d-flex mr-1 mt-4">
         <div class="col-8 d-flex flex-column">
            <h2>Description</h2>
            <div class="jumbotron bg-white">
               <?php
                  echo $model->description;
                ?>
            </div>
         </div>
         <div class="col-4 d-flex flex-column"> <!-- put below -->
            <!-- insert documents here a tag, src doc loc
            maybe another jumbotron or something here-->
            <p class="text-right h2 pr-2">Attachments</p>
            <div class="jumbotron bg-white text-right">
                  <?php
                  foreach ($model->documents as $doc) {
                      $uploadPath = Yii::getAlias('@webroot') . '/uploads/bug_' . $doc->bug_id . '/' . $doc->path;
                      echo Html::a(
                          $doc->path, 
                          [
                              'download',
                              'fpath' => $uploadPath,
                          ],
                          [
                              'class' => 'btn'
                          ]
                      );
                  }
                ?>
            </div>
         </div>
      </div>

      <!-- if got special role gimme notes if not dun show -->
      <!-- add a button to go to form for them to edit / do anything according to role -->
      <div class="flex-row ml-1 mr-1 mt-4">
         <?php
            if (Yii::$app->user->can(User::ROLE_DEVELOPER) || Yii::$app->user->can(User::ROLE_TRIAGER) || Yii::$app->user->can(User::ROLE_REVIEWER)){
               if($model->notes){
                  echo "<h2>Notes</h2>";
                  echo Html::tag('div', $model->notes, [
                     'class' => 'jumbotron',
                  ]);
               }
               // note segment here
            };
          ?>
      </div>

      <!-- comment begins here -->
      <div class="flex-row ml-1 mr-1 mt-4 p-1" style="background:none">
            <!-- if no comment do something -->
         <?php
            echo Accordion::widget([
               'items' => [
                  [
                     'label' => 'Comments',
                     'content' => ListView::widget([
                        'dataProvider' => $dataProvider,
                        'itemView' => 'comment',
                        'summary' => '',
                        'viewParams' => [
                           'fullView' => true,
                        ],
                     ]),
                     'contentOptions' => ['class' => 'in']
                  ]
               ],
            ])
          ?>
       </div>
       <div class="flex-row ml-1 mr-1 mt-2 p-1" style="background:none">
         <?php $form = ActiveForm::begin(); ?>
         <?php echo $form->field($comment, 'bug_id')->hiddenInput(['value'=>$model->id])->label(false);?>   
         <?php echo $form->field($comment, 'comment')->textArea(['rows'=>6]) ?>
         <?php echo Html::submitButton('Post', ['class'=> 'btn btn-primary'])?>
         <?php ActiveForm::end();?>
      </div>
   </div>
</div>
