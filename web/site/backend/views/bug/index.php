<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var common\models\search\BugSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = 'Bugs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-index">
    <div class="card">
        <div class="card-header">
            <?php echo Html::a('Create Bug', ['create'], ['class' => 'btn btn-success']) ?>
            <?php echo Html::a('All Bugs', ['index'], ['class' => 'btn btn-success']) ?>
            <?php echo Html::a('My Tasks', ['tasks'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body p-0">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?php echo GridView::widget([
                'layout' => "{items}\n{pager}",
                'options' => [
                    'class' => ['gridview', 'table-responsive'],
                ],
                'tableOptions' => [
                    'class' => ['table', 'text-nowrap', 'table-striped', 'table-bordered', 'mb-0'],
                ],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    [
                        'attribute'=>'title',
                        'format'=>'raw',
                        'value'=> function($data){
                            return Html::a(
                                $data->title,
                                ['bug/view', 'id'=>$data->id],
                                ['bugcomment/view', 'id'=>$data->id],
                                ['bug'=>'View', 'class'=>'no-pjax'],
                            );
                        }
                    ],
                    //'description:ntext',
                    'bug_status',
                    'priority_level',
                    // 'developer_user_id',
                    // 'notes',
                    // 'delete_status',
                    // 'created_at',
                    // 'created_by',
                    // 'updated_at',
                    // 'updated_by',

                    ['class' => \common\widgets\ActionColumn::class],
                ],
            ]); ?>

        </div>
        <div class="card-footer">
            <?php echo getDataProviderSummary($dataProvider) ?>
        </div>
    </div>

</div>
