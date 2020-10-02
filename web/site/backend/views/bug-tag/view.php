<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var common\models\BugTag $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bug Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-tag-view">
    <div class="card">
        <div class="card-header">
            <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
        <div class="card-body">
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'bug_id',
                    'name',
                    'status',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by',
                    
                ],
            ]) ?>
        </div>
    </div>
</div>
