<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\BugDocument $model
 * @var yii\bootstrap4\ActiveForm $form
 */
?>

<div class="bug-document-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="card">
            <div class="card-body">
                <?php echo $form->errorSummary($model); ?>

                <?php echo $form->field($model, 'bug_id')->textInput() ?>
                <?php echo $form->field($model, 'file_path')->textInput(['maxlength' => true]) ?>
                <?php echo $form->field($model, 'status')->dropDownList([ 'enabled' => 'Enabled', 'disabled' => 'Disabled', ], ['prompt' => '']) ?>
                <?php echo $form->field($model, 'created_at')->textInput() ?>
                <?php echo $form->field($model, 'created_by')->textInput() ?>
                <?php echo $form->field($model, 'updated_at')->textInput() ?>
                <?php echo $form->field($model, 'updated_by')->textInput() ?>
                
            </div>
            <div class="card-footer">
                <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
