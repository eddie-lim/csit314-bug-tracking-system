<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugTag $model
 */

$this->title = 'Update Bug Tag: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bug Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bug-tag-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
