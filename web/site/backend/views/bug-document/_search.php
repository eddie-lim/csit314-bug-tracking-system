<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\BugDocument $model
 * @var yii\bootstrap4\ActiveForm $form
 */
?>

<div class="bug-document-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>
    <?php echo $form->field($model, 'bug_id') ?>
    <?php echo $form->field($model, 'file_path') ?>
    <?php echo $form->field($model, 'delete_status') ?>
    <?php echo $form->field($model, 'created_at') ?>
    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
