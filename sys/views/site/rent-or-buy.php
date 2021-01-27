<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Your first kokle');

?>

<div class="login-box">
    <div class="rent-or-buy-form row">
        <?php $form = ActiveForm::begin(['id' => 'rent-or-buy-form', 'enableClientValidation' => false]); ?>
        <div class="form-group col-sm-6">
        <?= $form
            ->field($model, 'fullname')
            ->label(Yii::t('app', 'Name/Surname'))
            ->textInput(['readonly' => true]) ?>

        <?= $form
            ->field($model, 'email')
            ->label(Yii::t('app', 'E-mail'))
            ->textInput() ?>

        <?= $form
            ->field($model, 'phone_number')
            ->label(Yii::t('app', 'Phone number'))
            ->textInput() ?>

        <?= $form
            ->field($model, 'address')
            ->label(Yii::t('app', 'Address'))
            ->textInput() ?>
        </div>
        
        <div class="form-group col-sm-6">            
            <div class ='rent-or-buy-radio'>
                <?= $form->field($model, 'payment_type')->radioList(['rent' => Yii::t('app', 'I would like to rent the kokle (10 euros per month)*'), 'buy' => Yii::t('app', 'I would like to buy the kokle (starts at 130 euros)'), 'payments' => Yii::t('app', 'I would like to pay for the kokle in 3 instalments (48euor/month)')])->label(Yii::t('app', 'Choose payment type')); ?> 
            </div>           
            <p style="color:red"> <?=Yii::t('app', '*Rent is available only in Latvia.')?> </p>                        
            
            <div class ='buy-options'>
                <?= $form->field($model, 'delivery_type')->radioList(['omniva' => Yii::t('app', 'I would like to receive it via Omniva (5 euros)'),'lvpost' => Yii::t('app', 'I would like to receive it via Latvian Post (5 euros)'),'express' => Yii::t('app','I would like to receive it via Express poastige (10 euros)'), 'foreign' => Yii::t('app', 'I want to receive with delivery outside Latvia (price by agreement)')])->label(Yii::t('app', 'Choose delivery type')); ?> 
            </div>
        </div>

        <div class="col-sm-12 text-center">
            <div>
                <?= Html::a(\Yii::t('app',  'Back to start'), ['index'], ['class' => 'btn btn-secondary']) ?> 
                <?= Html::submitButton(\Yii::t('app', 'Submit'), ['class' => 'btn btn-primary btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>