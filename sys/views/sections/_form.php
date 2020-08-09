<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="difficulties-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'is_visible')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>