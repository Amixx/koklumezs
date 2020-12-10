<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Sign up');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

$fieldOptions3 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions4 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-earphone form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-box-body login">

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => Yii::t('app', 'Username')]) ?>

        <span class="glyphicon glyphicon-info-sign info"></span>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => Yii::t('app', 'Password')]) ?>

        <?= $form
            ->field($model, 'first_name', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => Yii::t('app', 'Name')]) ?>

        <?= $form
            ->field($model, 'last_name', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => Yii::t('app', 'Surname')]) ?>

        <?= $form
            ->field($model, 'email', $fieldOptions3)
            ->label(false)
            ->textInput(['placeholder' => Yii::t('app', 'E-mail')]) ?>

        <?= $form
            ->field($model, 'phone_number', $fieldOptions4)
            ->label(false)
            ->textInput([
                'placeholder' => Yii::t('app', 'Phone number')
            ]) ?>

        <?= $form
            ->field($model, 'language')
            ->label(false)
            ->dropDownList(['lv' => \Yii::t('app',  'Latvian'), 'eng' => \Yii::t('app',  'English')], ['prompt' => '- - '.Yii::t('app', 'language').' - -', 'options'=>[$defaultLanguage => ["Selected" => true]]]) ?>
        <?= Html::label(Yii::t('app', 'Do you have your own kokle?'), 'has-own-instrument'); ?>
        <?= Html::dropDownList('has-own-instrument', null, [false => \Yii::t('app',  'No'), true => \Yii::t('app', 'Yes')], ['prompt' => '', 'style' => 'width: 64px !important']) ?>
        <div class="has-experience">
            <?= Html::label(Yii::t('app', 'Have you played kokle before?'), 'has-experience'); ?>
            <?= Html::dropDownList('has-experience', false, [false => \Yii::t('app',  'No'), true => \Yii::t('app', 'Yes')], ['style' => 'width: 64px !important']) ?>
        </div>

        <div class="row" style="margin-top: 32px;">
            <div class="col-xs-12 col-md-4">
                <?= Html::submitButton(\Yii::t('app',  'Sign up'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>