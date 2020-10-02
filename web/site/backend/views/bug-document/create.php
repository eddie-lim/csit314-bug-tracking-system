<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugDocument $model
 */

$this->title = 'Create Bug Document';
$this->params['breadcrumbs'][] = ['label' => 'Bug Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-document-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
