<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Join another school');
?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'linkToSchool')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app',  'Join'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>