<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
        <?= Html::submitButton(\Yii::t('app',  'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(\Yii::t('app',  'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>