<?php

/**
 * @var yii\web\View $this
 * @var common\models\Bug $model
 */

$this->title = 'Create Bug';
$this->params['breadcrumbs'][] = ['label' => 'Bugs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bug-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
