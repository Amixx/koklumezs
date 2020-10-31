<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = \Yii::t('app',  'Sign up');
$this->params['breadcrumbs'][] = $this->title;

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-box-body login">

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => Yii::t('app', 'Username')]) ?>

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
            ->field($model, 'phone_number', $fieldOptions1)
            ->label(false)
            ->textInput([
                // 'type' => 'number',
                'placeholder' => Yii::t('app', 'Phone number')
            ]) ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => Yii::t('app', 'E-mail')]) ?>

        <?= $form
            ->field($model, 'language')
            ->label(false)
            ->dropDownList(['lv' => \Yii::t('app',  'Latvian'), 'eng' => \Yii::t('app',  'English')], ['prompt' => '- - '.Yii::t('app', 'language').' - -']) ?>

        <div class="row">
            <div class="col-xs-12 col-md-4">
                <?= Html::submitButton(\Yii::t('app',  'Sign up'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>