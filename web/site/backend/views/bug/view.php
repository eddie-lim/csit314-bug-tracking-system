<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Accordion;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use common\models\Bug;
use common\models\User;

use kartik\select2\Select2;
use kartik\widgets\SwitchInput;
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

   <?php foreach ($model->tags as $tag) : ?>
      <div class="py-1 px-2 font-weight-normal text-uppercase badge badge-pill badge-secondary"><?= $tag->name ?>&nbsp;<a class="delete-tag" data-tagid="<?= $tag->id ?>" href="#"><i class="text-white fas fa-times"></i></a></div>
   <?php endforeach; ?>
   <input id="create-tag-input" type="text" name="create-tag" placeholder="e.g. copywriting">
   <a id="create-tag" class="btn btn-primary text-white">Create Tag</a>

   <div class='card d-flex' style="background:none">
      <?php if(Yii::$app->user->can(User::ROLE_DEVELOPER) || Yii::$app->user->can(User::ROLE_TRIAGER) || Yii::$app->user->can(User::ROLE_REVIEWER)): ?>
         <div class="col-12">
            <?php $taskForm = ActiveForm::begin([
                       'id' => 'taskForm',
                       'action' => 'process-interaction?id='.$model->id,
                   ]); ?>
               <div class="card mt-2">
                  <div class="card-header">
                     <a id="task-form-header" class="btn btn-sm btn-outline-primary" href="#">Update Bug Ticket Status Form</a>
                  </div>
                  <div id="task-form-body">
                     <div class="card-body">
                        <?php echo $taskForm->errorSummary($taskModel); ?>
                        <?php
                           if (Yii::$app->user->can(User::ROLE_DEVELOPER)){
                              if(($model->bug_status == Bug::BUG_STATUS_ASSIGNED || $model->bug_status == Bug::BUG_STATUS_REOPEN) && $model->developer_user_id && $model->developer_user_id == Yii::$app->user->id){
                                    echo $taskForm->field($taskModel, 'accept')->widget(SwitchInput::classname(), [
                                        'value' => true,
                                        'pluginOptions' => [
                                            'size' => 'medium',
                                            'onColor' => 'success',
                                            'offColor' => 'danger',
                                            'onText' => 'Yes',
                                            'offText' => 'No',
                                        ]
                                    ]);
                              }
                             echo $taskForm->field($taskModel, 'notes')->textarea(['rows' => 3]);
                           } elseif (Yii::$app->user->can(User::ROLE_TRIAGER)){
                              if($model->bug_status == Bug::BUG_STATUS_NEW){
                                 echo $taskForm->field($taskModel, 'developer_user_id')->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map($availableDevelopers, 'id', 'publicIdentity'),
                                    'options' => ['placeholder' => 'Select Developer ...'],                        
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                 ]);
                              }
                              echo $taskForm->field($taskModel, 'status')->dropDownList($taskModel::getStatusTriager());
                              echo $taskForm->field($taskModel, 'priority_level')->dropDownList(Bug::getAllPriorityLevel());
                              echo $taskForm->field($taskModel, 'notes')->textarea(['rows' => 3]);
                           } elseif (Yii::$app->user->can(User::ROLE_REVIEWER)){
                              if($model->bug_status == Bug::BUG_STATUS_PENDING_REVIEW){
                                 echo $taskForm->field($taskModel, 'status')->dropDownList($taskModel::getStatusReviewer());
                                 echo $taskForm->field($taskModel, 'notes')->textarea(['rows' => 3]);
                              }
                           }
                        ?>
                     </div>
                     <div class="card-footer">
                         <?php echo Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                     </div>
                  </div>
                     
               </div>
            <?php ActiveForm::end(); ?>
         </div>
      <?php endif; ?>
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
               ['class' => 'text-uppercase font-weight-normal badge badge-light', 'id'=>'updated_by']); ?>
               on
               <?= Html::tag('span', Html::encode(Yii::$app->formatter->asDateTime($model->updated_at)),
               ['class' => 'text-uppercase font-weight-normal badge badge-light', 'id'=>'updated_at']); ?>
            </span>
         </div>

         <div class="col-4 d-flex align-items-end flex-column" style="background: white">
            <div class="text-left h-100">
               <div class="h2 pt-2">
                  Status
                  <?php
                  echo Html::tag('span', Html::encode($model->bug_status),
                  ['class' => 'text-uppercase font-weight-normal badge badge-pill '.$model->bugStatusBadgeColor, 'id'=>'bug_status']);
                  ?>
               </div>
               <div class="h5 p-0">
                  Priority
                  <?php
                     echo Html::tag('span', Html::encode($model->priority_level),
                     ['class' => 'badge pl-4 pr-4 badge-pill '.$model->priorityLevelBadgeColor, 'id'=>'priority_level'])
                   ?>
               </div>
               <div class="h5 p-0">
                  Assigned
                  <?php
                     echo Html::tag('span', $model->developer_user_id == null ? "Unassigned" : Html::encode(User::findIdentity($model->developer_user_id)->publicIdentity),
                     ['class' => 'badge badge-light', 'id'=>'developer_user']);
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
                  echo "<h2>Internal Notes</h2>";
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

<?php

$script = <<< JS
   $('#taskForm').on('beforeSubmit', function() {
      var data = $('#taskForm').serialize();
      $.ajax({
         url: $('#taskForm').attr('action'),
         type: 'POST',
         data: data,
         success: function (data) {
            // Implement successful
            console.log(data)
            let success = data.success;
            let model = data.model;
            let errors = data.errors;
            if(success){
               $('#taskForm').slideUp();
               $('#updated_by').text(model.updated_by);
               $('#updated_at').text(model.updated_at);
               $('#bug_status').removeClass('badge-warning').removeClass('badge-success').removeClass('badge-info').removeClass('badge-light').addClass(model.bug_status_badge).text(model.bug_status);
               $('#priority_level').removeClass('badge-info').removeClass('badge-warning').removeClass('badge-danger').removeClass('badge-light').addClass(model.priority_level_badge).text(model.priority_level);
               $('#developer_user').text(model.developer_user);
            } else {
               processErrorResponse(errors)
            }
         },
         error: function(jqXHR, errMsg) {
            // alert(errMsg);
            console.log(jqXHR);
            console.log(errMsg);
            alert("Please try again.");
         }
      });
      return false; // prevent default submit
   });

   $('#create-tag').on('click', function(e) {
      console.log(this)
      var new_tag_name = $("#create-tag-input").val()
      var data = {
         "bug_id" : $model->id,
         "name" : new_tag_name
      }
      console.log(data)
      $.ajax({
         url: 'create-tag',
         type: 'POST',
         data: data,
         success: function (data) {
            // Implement successful
            console.log(data)
            let success = data.success;
            let model = data.model;
            let errors = data.errors;
            if(success){

            } else {
               processErrorResponse(errors)
            }
         },
         error: function(jqXHR, errMsg) {
            // alert(errMsg);
            console.log(jqXHR);
            console.log(errMsg);
            alert("Please try again.");
         }
      });
      return false; // prevent default submit
   });

   $('.delete-tag').on('click', function(e) {
      var data = {
         "id" : $(this).data().tagid
      }
      $.ajax({
         url: 'delete-tag',
         type: 'POST',
         data: data,
         success: function (data) {
            // Implement successful
            console.log(data)
            let success = data.success;
            let model = data.model;
            let errors = data.errors;
            if(success){

            } else {
               processErrorResponse(errors)
            }
         },
         error: function(jqXHR, errMsg) {
            // alert(errMsg);
            console.log(jqXHR);
            console.log(errMsg);
            alert("Please try again.");
         }
      });
      return false; // prevent default submit
   });

   function processErrorResponse(errors){
      console.log("error!")
      var errorMsg = "";
      for (i = 0; i < Object.keys(errors).length; i++) {
         const attr = Object.keys(errors)[i];
         const msg = Object.values(errors)[i];
         console.log(attr)
         console.log(msg)
         var err = attr + ": " + msg;
         errorMsg += err;
         console.log(errorMsg)
      }
      alert(errorMsg)
   }

   $('#task-form-header').on('click', function(e) {
      e.preventDefault();
      $('#task-form-body').slideToggle()
   })

JS;
$this->registerJs($script);

?>