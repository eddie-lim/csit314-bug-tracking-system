<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugAction $model
 */

$this->title = 'Create Bug Action';
$this->params['breadcrumbs'][] = ['label' => 'Bug Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-action-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
