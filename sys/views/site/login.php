<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = \Yii::t('app',  'Log in');


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
    <p>
        Hei! Sākot ar jauno gadu mainīsies veids kā varēsi tikt iekšā savā profilā - jau tagad vari izmantot epasta adresi, ko esi piereģistrējis un savu jau esošo paroli. 
        Ja neatceries  - raksti uz <strong>skola@koklumezs.lv</strong>
    </p>
    <div class="login-box-body login">

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->label(\Yii::t('app',  'Remember me'))->checkbox() ?>
            </div>
            <div class="col-xs-12 col-md-4">
                <?= Html::submitButton(\Yii::t('app',  'Log in'), ['class' => 'btn btn-orange btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>
        <div style="color:#999;margin:1em 0">
            <?= Html::a(\Yii::t('app',  'Forgot password') . '?', ['site/request-password-reset']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>