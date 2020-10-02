<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugDocument $model
 */

$this->title = 'Update Bug Document: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bug Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bug-document-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
