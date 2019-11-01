<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserlectureevaluationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="userlectureevaluations-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'lecture_id') ?>

    <?= $form->field($model, 'evaluation_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'evaluation') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
