<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\BugAction $model
 * @var yii\bootstrap4\ActiveForm $form
 */
?>

<div class="bug-action-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="card">
            <div class="card-body">
                <?php echo $form->errorSummary($model); ?>

                <?php echo $form->field($model, 'bug_id')->textInput() ?>
                <?php echo $form->field($model, 'action_type')->dropDownList([ 'new' => 'New', 'assigned' => 'Assigned', 'fixing' => 'Fixing', 'pending_review' => 'Pending review', 'completed' => 'Completed', 'rejected' => 'Rejected', 'reopen' => 'Reopen', ], ['prompt' => '']) ?>
                <?php echo $form->field($model, 'notes')->textarea(['rows' => 6]) ?>
                <?php echo $form->field($model, 'delete_status')->dropDownList([ 'enabled' => 'Enabled', 'disabled' => 'Disabled', ], ['prompt' => '']) ?>
                
            </div>
            <div class="card-footer">
                <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
