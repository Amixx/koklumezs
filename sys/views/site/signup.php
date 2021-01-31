<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app',  'Sign up');

function getFieldOptions($fieldName, $hasInfo = false){
    $glyphon = 'user';
    $classes = 'form-group has-feedback';

    switch($fieldName) {
        case 'password': $glyphon = 'lock'; break;
        case 'email': $glyphon = 'envelope'; break;
        case 'phone_number': $glyphon = 'earphone'; break;
        default: break;
    } 
    if($hasInfo) $classes .= ' field-with-info-widget';

    return [
        'options' => ['class' => $classes],
        'inputTemplate' => "{input}<span class='glyphicon glyphicon-" . $glyphon . " form-control-feedback'></span>"
    ];
}
?>

<div class="login-box">
    <div class="login-box-body login">
        <h3><?= $registration_title ?></h3>
        
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <div class="signup-form-section">
            <?= $form
                ->field($model, 'email', getFieldOptions('email'))
                ->label(false)
                ->textInput(['placeholder' => Yii::t('app', 'E-mail')]) ?>

            <?= $form
                ->field($model, 'password', getFieldOptions('password'))
                ->label(false)
                ->passwordInput(['placeholder' => Yii::t('app', 'Password')]) ?>
        </div>

        <div class="signup-form-section">
            <?= $form
                ->field($model, 'first_name', getFieldOptions('first_name'))
                ->label(false)
                ->textInput(['placeholder' => Yii::t('app', 'Name')]) ?>

            <?= $form
                ->field($model, 'last_name', getFieldOptions('last_name'))
                ->label(false)
                ->textInput(['placeholder' => Yii::t('app', 'Surname')]) ?>
        </div>    

        <div id="has-instrument">
            <?= $form->field(
                $model, 
                'ownsInstrument', 
                ['labelOptions' => ['class' => 'signup-checkbox-label']]
            )->dropDownList(
                [false => \Yii::t('app',  'No'), true => \Yii::t('app', 'Yes')],
                ['prompt' => '', 'class' => 'small-dropdown']
            )->label(Yii::t('app', 'Do you have your own kokle?')) ?>
        </div>
        <div class="has-experience">
            <?= $form->field(
                $model, 
                'hasExperience', 
                ['labelOptions' => ['class' => 'signup-checkbox-label']]
            )->dropDownList(
                [false => \Yii::t('app',  'No'), true => \Yii::t('app', 'Yes')],
                ['prompt' => '', 'class' => 'small-dropdown']
            )->label(Yii::t('app', 'Have you played kokle before?')) ?>
        </div>
        
        <div style='margin-top: 16px;'>
        <?= $form->field($model, 'agree')->checkBox()
            ->label(\Yii::t('app','I agree to receive emails regarding information about lectures, the education process, and events')); ?>
        </div>  

        <div class="row" style="margin-top: 32px;"> 
            <div class="col-xs-12 col-md-4">
                <?= Html::submitButton(\Yii::t('app',  'Sign up'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button', 'id' => 'registration-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>