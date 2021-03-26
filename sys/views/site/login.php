<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = \Yii::t('app',  'Log in');


$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback']
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback']
];
?>

<!-- pagaidām noņemu, jo šī login lapa ir arī pārējām skolām! -->
<!-- <h2 class="login-header">Esi sveicināts Kokļu Meža attālinātajā individuālajā skolā! Koklēsim!</h2> -->

<div class="login-box login-container col-sm-12">
    <div class="login-box-body login login-form">
        <h3 id="login-header"><?= $loginTitle ?></h3>
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email'), 'class' => 'login-input']) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'class' => 'login-input']) ?>

        <div class="row">
            <div class="col-xs-7 login-remember">
                <?= $form->field($model, 'rememberMe')->label(\Yii::t('app',  'Remember me'))->checkbox() ?>
            </div>
            <div class="col-xs-5">
                <?= Html::submitButton(\Yii::t('app',  'Log in'), ['class' => 'btn btn-orange btn-block btn-flat login-button', 'name' => 'login-button']) ?>
            </div>
        </div>
        <div class='login-forgot'>
            <?= Html::a(\Yii::t('app',  'Forgot password') . '?', ['site/request-password-reset']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>