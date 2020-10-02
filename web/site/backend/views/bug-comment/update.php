<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugComment $model
 */

$this->title = 'Update Bug Comment: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bug Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bug-comment-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
