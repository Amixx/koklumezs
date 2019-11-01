<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluations */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="evaluations-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'zvaigznes' => 'Zvaigznes', 'teksts' => 'Teksts', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
