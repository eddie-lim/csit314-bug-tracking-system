<?php

/**
 * @var yii\web\View $this
 * @var common\models\BugTag $model
 */

$this->title = 'Create Bug Tag';
$this->params['breadcrumbs'][] = ['label' => 'Bug Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-tag-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
