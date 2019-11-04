<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="handdifficulties-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hand')->dropDownList([ 'left' => 'Left', 'right' => 'Right', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'category')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
