<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\Bug;
use common\models\User;
use common\components\MyCustomActiveRecord;

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bug-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="card">
            <div class="card-body">
                <?php echo $form->errorSummary($model); ?>

                <?php 
                    if (Yii::$app->user->can(User::ROLE_DEVELOPER)){
                        echo $form->field($model, 'accept')->textInput(['maxlength' => true]);
                    } elseif (Yii::$app->user->can(User::ROLE_TRIAGER)){
                        echo $form->field($model, 'developer_user_id')->textInput(['maxlength' => true]);
                        echo $form->field($model, 'status')->textarea(['rows' => 6]);
                    } elseif (Yii::$app->user->can(User::ROLE_REVIEWER)){
                        echo $form->field($model, 'status')->textarea(['rows' => 6]);
                    }
                    echo $form->field($model, 'notes')->textInput(['maxlength' => true]);
                ?>


            </div>
            <div class="card-footer">
                <?php echo Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
