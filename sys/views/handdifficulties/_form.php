<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="handdifficulties-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hand')->dropDownList(['left' => \Yii::t('app',  'Left'), 'right' => \Yii::t('app',  'Right'),], ['prompt' => '']) ?>

    <?= $form->field($model, 'category')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  \Yii::t('app',  'Save')), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>