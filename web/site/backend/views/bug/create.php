<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
// use common\models\Bug;
use common\components\MyCustomActiveRecord;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var common\models\Bug $model
 */

$this->title = 'Create Bug';
$this->params['breadcrumbs'][] = ['label' => 'Bugs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bug-create">

  <!-- Ignore this for the time being -->
  <?php //echo $this->render('_form', ['model' => $model,]) ?>

  <div class="bug-form">
    <?php $form = ActiveForm::begin([ 'options' => ['enctype' => 'multipart/form-data'] ]); ?>
      <div class="card">

        <div class="card-body">
          <?= $form->errorSummary($model); ?>
          <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
          <?= $form->field($model, 'notes')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'documents[]')->fileInput(['multiple' => 'true']) ?>
          <?= $form->field($model, 'tags')->widget(Select2::classname(), [
              'data' => $model->getCommonTags(),
              'showToggleAll' => false,
              'options' => [ 'multiple' => true ],
              'pluginOptions' => [ 'tags' => true,
                                   'tokenSeparators' => [ ',', ' '],
                                   'maximumInputLength' => 15,
                                   'allowClear' => true],
            ]); ?>
        </div>

        <div class="card-footer">
          <?= Html::submitButton('Create', ['class' => 'btn btn-success'])?>
        </div>

      </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>
