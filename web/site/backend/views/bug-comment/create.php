<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugComment $model
 */

$this->title = 'Create Bug Comment';
$this->params['breadcrumbs'][] = ['label' => 'Bug Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-comment-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
