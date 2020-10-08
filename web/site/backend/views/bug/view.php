<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\ActiveForm;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * @var yii\web\View $this
 * @var common\models\Bug $model
 */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Bugs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="bug-view">
    <div class="card">
        <!--
        <div class="card-header">
            <?php //echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php /*echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) */?>
        </div>
        -->

        <div class="card-body">
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'title',
                    'description:ntext',
                    'bug_status',
                    'priority_level',
                    'developer_user_id',
                    'notes',
                    'delete_status',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by',
                    [
                        'label' => 'Documents [temp field]',
                        'value' => array_reduce($documents, function($a, $b) {
                            return $a . (empty($a) ? '' : ', ') . $b->attributes['path'];
                        }, '')
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>


<div class="bug-comment">
    <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'comment',
                'created_at',
                'created_by',
            ],
            'layout' => '{items}',
        ]);
    ?>


    <?php $form = ActiveForm::begin(); ?>
        <?php echo $form->field($comment, 'bug_id')->hiddenInput(['value'=> $model->id])->label(false); ?>
        <?php echo $form->field($comment, 'created_at')->hiddenInput(['value'=> 123])->label(false); ?>
        <?php echo $form->field($comment, 'created_by')->hiddenInput(['value'=> Yii::$app->user->identity->id])->label(false); ?>
        <?php echo $form->field($comment, 'comment')->textArea(['rows'=>6]) ?>

        <?php echo Html::submitButton('Post', ['class'=> 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
