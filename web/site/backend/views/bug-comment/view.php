<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var common\models\BugComment $model
 */

//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bug Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-comment-view">
    <div class="card">
            <?php //echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php /*echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) */?>
        <div class="card-body">
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'bug_id',
                    'comment:ntext',
                    'delete_status',
                    'created_at',
                    'created_by',
                ],
            ]) ?>
        </div>
    </div>
</div>
