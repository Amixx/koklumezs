<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app',  'Edit school requisites');
\Yii::t('app',  'Edit');
?>
<div class="school-settings-update">

    <h1><?= Html::encode($this->title) . ': ' ?></h1>

    <div class="school-settings-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'supplier')->textInput(['class' => 'form-control form-group has-feedback', 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'registration_number')->textInput(['class' => 'form-control form-group has-feedback', 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'pvn_registration_number')->textInput(['class' => 'form-control form-group has-feedback', 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'legal_address')->textInput(['class' => 'form-control form-group has-feedback', 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'bank')->textInput(['class' => 'form-control form-group has-feedback', 'disabled' => 'disabled']) ?>
        <?= $form->field($model, 'account_number')->textInput(['class' => 'form-control form-group has-feedback']) ?>

        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app',  'Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>