<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\Bug;

/**
 * @var yii\web\View $this
 * @var common\models\Bug $model
 * @var yii\bootstrap4\ActiveForm $form
 */
?>

<div class="bug-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="card">
            <div class="card-body">
                <?php echo $form->errorSummary($model); ?>

                <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?php echo $form->field($model, 'description')->textarea(['rows' => 6]) ?>
                <?php echo $form->field($model, 'notes')->textInput(['maxlength' => true]) ?>

                <?php if (!$model->isNewRecord) : ?>
                    <?= $form->field($model, 'bug_status')->dropDownList(
                        [ 'new' => 'New', 'assigned' => 'Assigned', 'fixing' => 'Fixing',
                          'pending_review' => 'Pending review', 'completed' => 'Completed',
                          'rejected' => 'Rejected', 'reopen' => 'Reopen', ],
                        [ 'prompt' => '' ]); ?>
                    <?= $form->field($model, 'priority_level')->dropDownList(
                        [ 1 => '1', 2 => '2', 3 => '3', ],
                        ['prompt' => '']) ?>
                    <?= $form->field($model, 'developer_user_id')->textInput() ?>
                    <?= $form->field($model, 'delete_status')->dropDownList(
                        [ 'enabled' => 'Enabled', 'disabled' => 'Disabled', ],
                        ['prompt' => '']) ?>
                <?php endif; ?>

            </div>
            <div class="card-footer">
                <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
