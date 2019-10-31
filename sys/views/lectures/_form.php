<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Lectures;
/* @var $this yii\web\View */
/* @var $model app\models\Lectures */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'complexity')->dropDownList(Lectures::getComplexity(), ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
