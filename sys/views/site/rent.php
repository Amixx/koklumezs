<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Your first kokle');

?>

<div class="login-box">
    <div class="rent-form row">

        <div class="container">
            <h2><?= Yii::t('app', 'Rent kokle') ?></h2>
            <p style="font-size:16px">
                <?= Yii::t('app', 'Fill in this application form if you want to rent a kokle. Rent 10 eur / month. Shipping with Omniva parcel terminal 5 eur.') ?>
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
                <?= Html::a(Yii::t('app', 'Back'), $backUrl, [
                    'class' => 'btn btn-info'
                ]) ?>
                <?= Html::submitButton(\Yii::t('app', 'Submit'), [
                    'class' => 'btn btn-primary btn-flat rent-submit-button',
                    'name' => 'login-button'
                ]) ?>
                <?= Html::a(Yii::t('app', 'View rent agreement'), Url::to(["documents/Kokles līgums.docx"])) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>