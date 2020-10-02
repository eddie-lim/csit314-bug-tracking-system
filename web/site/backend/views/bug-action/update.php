<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugAction $model
 */

$this->title = 'Update Bug Action: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bug Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bug-action-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
