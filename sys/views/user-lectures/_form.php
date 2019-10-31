<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'lecture_id')
        ->dropDownList(
            $lectures,           // Flat array ('id'=>'label')
            ['prompt'=>'']    // options
        ); ?>
        <?= $form->field($model, 'user_id')
        ->dropDownList(
            $students,           // Flat array ('id'=>'label')
            ['prompt'=>'']    // options
        ); ?>
    <?php /*
    <?= $form->field($model, 'lecture_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'assigned')->textInput() ?>

    <?= $form->field($model, 'created')->textInput() ?>
*/ ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
