<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Your first kokle');

?>

<div class="login-box">
    <div class="rent-form row">

        <div class="container">
            <h2><?= Yii::t('app', 'Rent kokle') ?></h2>
            <p style="font-size:16px">
                <?= Yii::t('app', 'If you wish to rent kokle (10 euro/month), after invoice payment we will send kokle to Omniva parcel machine of your choice (shipping 5 euro)') ?>
            </p>
            <p style="color:red"> <?= Yii::t('app', 'Please fill every field') ?> </p>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'rent-form', 'enableClientValidation' => false]); ?>
        <div class="form-group col-sm-6">
            <?= $form
                ->field($model, 'fullname')
                ->label(Yii::t('app', 'Name/Surname'))
                ->textInput(['readonly' => true]) ?>

            <?= $form
                ->field($model, 'email')
                ->label(Yii::t('app', 'E-mail'))
                ->textInput() ?>
        </div>
        <div class="form-group col-sm-6">
            <?= $form
                ->field($model, 'phone_number')
                ->label(Yii::t('app', 'Phone number'))
                ->textInput() ?>

            <?= $form
                ->field($model, 'address')
                ->label(Yii::t('app', 'Omniva address'))
                ->textInput() ?>
        </div>

        <div class="col-sm-12 text-center">
            <div>
                <?= Html::submitButton(\Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>