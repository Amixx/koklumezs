<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true,'value'=>""]) ?>    

    
    <?= $form->field($model, 'user_level')->dropDownList([ 'Admin' => 'Admin', 'Student' => 'Students', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'status')->dropDownList([ User::STATUS_INACTIVE => 'Nav aktīvs', User::STATUS_ACTIVE => 'Aktīvs', User::STATUS_DELETED => 'Dzēsts'], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
